<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_Recurring
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\Recurring\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\Framework\App\Response\Http as responseHttp;
use Magento\Framework\UrlInterface;

class SalesQuoteAddItem implements ObserverInterface
{
    const SUBSCRIPTION_CHARGE = 'subscription_charge';

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var \Webkul\Recurring\Helper\Data
     */
    private $recurringHelper;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param OrderFactory $orderFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param QuoteFactory $quoteFactory
     * @param responseHttp $response
     * @param UrlInterface $url
     * @param \Webkul\Recurring\Helper\Data $recurringHelper
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        OrderFactory $orderFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        QuoteFactory $quoteFactory,
        responseHttp $response,
        UrlInterface $url,
        \Webkul\Recurring\Helper\Data $recurringHelper
    ) {
        $this->customerSession = $customerSession;
        $this->orderFactory    = $orderFactory;
        $this->request         = $request;
        $this->jsonHelper      = $jsonHelper;
        $this->messageManager  = $messageManager;
        $this->checkoutSession = $checkoutSession;
        $this->quoteFactory    = $quoteFactory;
        $this->response        = $response;
        $this->url             = $url;
        $this->recurringHelper = $recurringHelper;
    }
    
    /**
     * Add quote item handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if ($this->customerSession->isLoggedIn()) {
                $quoteItem = $observer->getQuoteItem();
                $quoteItemId = $quoteItem->getId();
                $additionalOptions = [];
                $count = 0;
                $planData = $this->request->getParams();
                if (isset($planData['plan_id']) && $planData['plan_id'] != '') {
                    $additionalOptions[] = [
                                'label' => 'Plan Id',// do not change
                                'value' => $planData['plan_id']
                            ];
                    $count ++;
                }
                if (isset($planData['start_date']) && $planData['start_date'] != '') {
                    $additionalOptions[] = [
                                'label' => 'Start Date',// do not change
                                'value' => $planData['start_date']
                            ];
                    $count ++;
                }
                if (isset($planData['initial_fee']) && $planData['initial_fee'] != '') {
                    $additionalOptions[] = [
                                'label' => 'Initial Fee',// do not change
                                'value' => $planData['initial_fee']
                            ];
                    $count ++;
                }
                $customPrice = 0.0;
                if (isset($planData[self::SUBSCRIPTION_CHARGE]) && $planData[self::SUBSCRIPTION_CHARGE] != '') {
                    $additionalOptions[] = [
                                'label' => 'Subscription Charge',// do not change
                                'value' => $planData[self::SUBSCRIPTION_CHARGE]
                            ];
                    $customPrice = $planData[self::SUBSCRIPTION_CHARGE];
                    $count ++;
                }
                $this->addCustomAdditionalOptions($additionalOptions, $customPrice, $quoteItemId, $count);
            }
        } catch (\Exception $e) {
            $this->recurringHelper->logDataInLogger(
                'Observer_SalesQuoteAddItem execute : Notice: '.$e->getMessage()
            );
        }
    }

    /**
     * Add Custom Additional Options to Subscription Products
     *
     * @param array $additionalOptions
     * @param float $customPrice
     * @param integer $quoteItemId
     * @param integer $count
     * @return void
     */
    private function addCustomAdditionalOptions($additionalOptions, $customPrice, $quoteItemId, $count)
    {
        /** @var \Magento\Quote\Model\Quote  */
        $quote = $this->checkoutSession->getQuote();
        if ($quote && $count > 1) {
            $cartData = $quote->getAllVisibleItems();
            foreach ($cartData as $item) {
                $itemId = $item->getId();
                if ($quoteItemId == $itemId) {
                    $item->addOption(
                        [
                            'product_id' => $item->getProductId(),
                            'code' => 'custom_additional_options',
                            'value' => $this->jsonHelper->jsonEncode($additionalOptions)
                        ]
                    );
                    if ($customPrice > 0) {
                        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
                        $price = $customPrice; //set your price here
                        $item->setCustomPrice($price);
                        $item->setOriginalCustomPrice($price);
                        $item->getProduct()->setIsSuperMode(true);
                    }
                }
            }
        }
    }
}
