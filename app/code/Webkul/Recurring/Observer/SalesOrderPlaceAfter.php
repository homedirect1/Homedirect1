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
use Magento\Framework\DB\Transaction;
use Magento\Quote\Model\QuoteRepository;

class SalesOrderPlaceAfter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var Magento\Sales\Model\OrderFactory;
     */
    protected $orderModel;

    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Webkul\Recurring\Helper\Data
     */
    private $helper;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param OrderFactory $orderModel
     * @param \Webkul\Recurring\Helper\Data $helper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Checkout\Model\Session $checkoutSession,
        OrderFactory $orderModel,
        \Webkul\Recurring\Helper\Data $helper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        QuoteRepository $quoteRepository
    ) {
        $this->date             = $date;
        $this->jsonHelper       = $jsonHelper;
        $this->helper           = $helper;
        $this->checkoutSession  = $checkoutSession;
        $this->orderModel       = $orderModel;
        $this->quoteRepository  = $quoteRepository;
    }

    /**
     * Observer action for Sales order place after.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $quoteId = $this->checkoutSession->getLastQuoteId();
            $quote = $this->quoteRepository->get($quoteId);
            $initialFee = '';
            foreach ($quote->getAllItems() as $item) {
                if ($additionalOptionsQuote = $item->getOptionByCode('custom_additional_options')) {
                    $allOptions = $this->jsonHelper->jsonDecode(
                        $additionalOptionsQuote->getValue()
                    );
                    foreach ($allOptions as $key => $option) {
                        if ($option['label'] == 'Initial Fee') {
                            $initialFee = ((float)$initialFee) + $option['value'];
                        }
                    }
                }
            }
            if ($initialFee != "") {
                $orderList = $observer->getOrders();
                $orderList = $orderList ? $orderList : [$observer->getOrder()];
                foreach ($orderList as $order) {
                    $orderId = $order->getId();
                    $order = $this->orderModel->create()->load($orderId);
                    $order->setInitialFee($initialFee)->save();
                    if ($order->getPayment()->getMethodInstance()->getCode() == 'recurringstripe') {
                        $order->setTotalPaid($order->getGrandTotal())
                            ->setBaseTotalPaid($order->getBaseGrandTotal())
                            ->save();
                    }
                }
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "Observer_orderplace execute : ".$e->getMessage()
            );
        }
    }
}
