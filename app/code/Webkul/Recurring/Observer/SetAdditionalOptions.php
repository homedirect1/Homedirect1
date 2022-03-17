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
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Webkul\Recurring\Model\SubscriptionType;
use Webkul\Recurring\Model\Term;
use Magento\Framework\Pricing\Helper\Data as FormatPrice;
use Magento\Checkout\Model\Session as CheckoutSession;
 
class SetAdditionalOptions implements ObserverInterface
{
    const PLAN_ID             = 'plan_id';
    const TERM_ID             = 'term_id';
    const START_DATE          = 'start_date';
    const INITIAL_FEE         = 'initial_fee';
    const SUBSCRIPTION_CHARGE = 'subscription_charge';

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Subscription
     */
    protected $plans;

    /**
     * @var Subscription
     */
    protected $term;
    
    /**
     * @var FormatPrice
     */
    protected $priceHelper;

    /**
     * @var FormatPrice
     */
    protected $checkoutSession;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var \Webkul\Recurring\Helper\Data
     */
    private $helper;
    
    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param RequestInterface $request
     * @param SubscriptionType $plans
     * @param Term $term
     * @param FormatPrice $priceHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param CheckoutSession $checkoutSession
     * @param \Webkul\Recurring\Helper\Data $helper
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        RequestInterface $request,
        SubscriptionType $plans,
        Term $term,
        FormatPrice $priceHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        CheckoutSession $checkoutSession,
        \Webkul\Recurring\Helper\Data $helper
    ) {
        $this->request          = $request;
        $this->customerSession  = $customerSession;
        $this->jsonHelper       = $jsonHelper;
        $this->priceHelper      = $priceHelper;
        $this->checkoutSession  = $checkoutSession;
        $this->plans            = $plans;
        $this->term             = $term;
        $this->helper           = $helper;
    }
 
    /**
     * Get additional options
     *
     * @param array $data
     * @return array
     */
    private function getAdditionalOption($data)
    {
        $additionalOptions = [];
        foreach ($data as $key => $value) {
            switch ($key) {
                case self::PLAN_ID:
                    $customArray = $this->getCustomValues($key, $value, $data);
                    if (is_array($customArray)) {
                        $additionalOptions[] = $customArray;
                    }
                    break;
                case self::INITIAL_FEE:
                    $customArray = $this->getCustomValues($key, $value, $data);
                    if (is_array($customArray)) {
                        $additionalOptions[] = $customArray;
                    }
                    break;
                case self::START_DATE:
                    $customArray = $this->getCustomValues($key, $value, $data);
                    if (is_array($customArray)) {
                        $additionalOptions[] = $customArray;
                    }
                    break;
                case self::SUBSCRIPTION_CHARGE:
                    $customPrice = $value;
                    break;
            }
        }
        return $additionalOptions;
    }
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $fullActionName = $this->request->getFullActionName();
            $actionArray = [
                "checkout_cart_updateItemOptions",
                'checkout_cart_add'
            ];
            if (in_array($fullActionName, $actionArray) && $this->customerSession->isLoggedIn()) {
                $product = $observer->getProduct();
                $data = $this->request->getParams();
                if ($this->validate($data, self::PLAN_ID)) {
                    if ($this->validate($data, self::START_DATE)) {
                        $additionalOptions = $this->getAdditionalOption($data);
                        $observer->getProduct()->addCustomOption(
                            'additional_options',
                            $this->jsonHelper->jsonEncode($additionalOptions)
                        );
                    }
                } else {
                    $this->checkoutSession->setInitialFee('');
                }
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger("Observer_SetAdditionalOptions_execute: ".$e->getMessage());
        }
    }

    /**
     * This function is used to validate the params data for subscriptions
     *
     * @param array $data
     * @param string $key
     * @return void
     */
    private function validate($data, $key)
    {
        if (isset($data[$key]) && $data[$key] != '') {
            return true;
        }
        return false;
    }

    /**
     * This function return the custom options for slected plans terms and date
     *
     * @param string $key
     * @param mixed $value
     * @param array $data
     * @return array
     */
    private function getCustomValues($key, $value, $data)
    {
        if ($key == self::PLAN_ID) {
            $model = $this->plans->load($value);
            if (!$model->getId()) {
                return '';
            }
            return [
                'label' => __("Subscription"),
                'value' => $model->getName()
            ];
        } elseif ($key == self::INITIAL_FEE) {
            if (!$value) {
                $this->checkoutSession->setInitialFee('');
                return '';
            }
                $this->checkoutSession->setInitialFee($value);
            return [
                'label' => __("Initial fee"),
                'value' => $this->priceHelper->currency($value, true, false)
            ];
        } elseif ($key == self::TERM_ID) {
            $model = $this->term->load($value);
            if (!$model->getId()) {
                return '';
            }
            return [
                'label' => $model->getTitle(),
                'value' => $model->getTitle() .'_'.$model->getId()
            ];
        } elseif ($key == self::START_DATE) {
            if (!$value) {
                return '';
            }
            return [
                'label' => __("Start Date"),
                'value' => $value
            ];
        }
        return [];
    }
}
