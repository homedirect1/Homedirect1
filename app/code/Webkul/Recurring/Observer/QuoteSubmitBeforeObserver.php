<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Observer;
 
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Webkul\Recurring\Model\SubscriptionType;
use Webkul\Recurring\Model\Subscriptions;
use Webkul\Recurring\Model\Term;
 
class QuoteSubmitBeforeObserver implements ObserverInterface
{
    /**
     * @var array
     */
    private $quoteItems = [];

    /**
     * @var Quote
     */
    private $quote = null;

    /**
     * @var Order
     */
    private $order = null;

    /**
     * @var SubscriptionType
     */
    private $plans;

    /**
     * @var term
     */
    private $term;

    /**
     * @var subscriptions
     */
    private $subscriptions;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;
    
    /**
     *
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $coreSession;
    
    /**
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $timeZone;

    /**
     * @var \Webkul\Recurring\Helper\Data
     */
    private $helper;
     
    /**
     * @param SubscriptionType $plans
     * @param Term $term
     * @param Subscriptions $subscriptions
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timeZone
     * @param \Magento\Framework\Session\SessionManager $coreSession
     * @param \Webkul\Recurring\Helper\Data $helper
     */
    public function __construct(
        SubscriptionType $plans,
        Term $term,
        Subscriptions $subscriptions,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timeZone,
        \Magento\Framework\Session\SessionManager $coreSession,
        \Webkul\Recurring\Helper\Data $helper
    ) {
        $this->coreSession      = $coreSession;
        $this->timeZone         = $timeZone;
        $this->jsonHelper       = $jsonHelper;
        $this->plans            = $plans;
        $this->term             = $term;
        $this->subscriptions    = $subscriptions;
        $this->helper           = $helper;
    }
    
    /**
     * This function is used to save subscription
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        try {
            $this->quote = $observer->getQuote();
            $this->order = $observer->getOrder();
            $refProfileId = '';
            $flag = 0;
            foreach ($this->order->getItems() as $orderItem) {
                if ($quoteItem = $this->getQuoteItemById($orderItem->getQuoteItemId())) {
                    if ($additionalOptionsQuote = $quoteItem->getOptionByCode('additional_options')) {
                        $additionalOptionsQuote = $this->jsonHelper->jsonDecode(
                            $additionalOptionsQuote->getValue()
                        );
                        if ($additionalOptionsOrder = $orderItem->getProductOptionByCode('additional_options')) {
                            $additionalOptions = $this->getMergedArray(
                                $additionalOptionsQuote,
                                $additionalOptionsOrder
                            );
                        } else {
                            $additionalOptions = $additionalOptionsQuote;
                        }
                        $options = $orderItem->getProductOptions();
                        $options['additional_options'] = $additionalOptions;
                        $orderItem->setProductOptions($options);
                    }
                    if ($customAdditionalOptionsQuote = $quoteItem->getOptionByCode('custom_additional_options')) {
                        $allOptions = $this->jsonHelper->jsonDecode(
                            $customAdditionalOptionsQuote->getValue()
                        );
                        $subscriptionTypeId =  '';
                        $startDate =  '';
                        list(
                            $subscriptionTypeId, $startDate
                        ) = $this->getSubscriptionData($allOptions);
                        if ($subscriptionTypeId) {
                            $firstName         = $this->order->getCustomerFirstname();
                            $lastName          = $this->order->getCustomerLastname();
                            $customerName      = $firstName." ".$lastName;
                            $planData          = $this->getPlanData($subscriptionTypeId);
                            $currentDateObject = $this->timeZone->date();
                            $currentDate       = $currentDateObject->format('Y-m-d H:i:s.u');
                            $currentDate       = str_replace(".000000", "", $currentDate);
                            $explodedValues    =  explode(" ", $currentDate);
                            $currentTime       =  $explodedValues[1] ?? "00:00:00";
                            $startDate         = date_format(date_create($startDate), "Y-m-d H:i:s");
                            $startDate         = str_replace("00:00:00", $currentTime, $startDate);
                            $data = [
                            'order_id'       =>  $this->order->getId(),
                            'product_id'     =>  $quoteItem->getProductId(),
                            'product_name'   =>  $quoteItem->getProduct()->getName(),
                            'customer_id'    =>  $this->order->getCustomerId(),
                            'customer_name'  =>  $customerName,
                            'plan_id'        =>  $subscriptionTypeId,
                            'start_date'     =>  $startDate,
                            'extra'          =>  $this->jsonHelper->jsonEncode($planData),
                            'status'         =>  true,
                            'ref_profile_id' =>  $refProfileId,
                            'created_at'     =>  $currentDate
                            ];
                            $this->saveSubscriptionData($data);
                        }
                    }
                }
            }
            $this->order->save();
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "Observer_QuoteSubmitBefore_execute: ".$e->getMessage()
            );
        }
    }

    /**
     * Get Plan Data
     *
     * @param integer $subscriptionTypeId
     * @return array
     */
    private function getPlanData($subscriptionTypeId)
    {
        return $this->plans->load($subscriptionTypeId)->getData();
    }

    /**
     * Save Subscription Data
     *
     * @param array $data
     * @return void
     */
    private function saveSubscriptionData($data)
    {
        $subscriptionsModel = $this->subscriptions;
        $subscriptionsModel->setData($data);
        $subscriptionsModel->save();
    }

    /**
     * This functiion returns then valid quote item id
     *
     * @param integer $id
     * @return mixed
     */
    private function getQuoteItemById($id)
    {
        if (empty($this->quoteItems)) {
            foreach ($this->quote->getAllVisibleItems() as $item) {
                $this->quoteItems[$item->getId()] = $item;
            }
        }
        if (array_key_exists($id, $this->quoteItems)) {
            return $this->quoteItems[$id];
        }
        return null;
    }

    /**
     * Get start date and subscription typeId
     *
     * @param array $allOptions
     * @return array
     */
    private function getSubscriptionData($allOptions)
    {
        $subscriptionTypeId = '';
        $startDate = '';
        foreach ($allOptions as $key => $option) {
            if ($option['label'] == 'Plan Id') {
                $subscriptionTypeId = $option['value'];
            }
            if ($option['label'] == 'Start Date') {
                $startDate = $option['value'];
            }
        }
        return [
            $subscriptionTypeId, $startDate
        ];
    }

    /**
     * Get Merged Array from two Arrays
     *
     * @param array $additionalOptionsQuote
     * @param array $additionalOptionsOrder
     * @return array
     */
    private function getMergedArray($additionalOptionsQuote, $additionalOptionsOrder)
    {
        $additionalOptions = array_merge($additionalOptionsQuote, $additionalOptionsOrder);
        return array_unique($additionalOptions, SORT_REGULAR);
    }
}
