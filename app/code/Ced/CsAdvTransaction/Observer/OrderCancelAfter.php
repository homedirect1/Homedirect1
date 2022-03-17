<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsAdvTransaction
 * @author     CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class OrderCancelAfter
 * @package Ced\CsAdvTransaction\Observer
 */
Class OrderCancelAfter implements ObserverInterface
{
    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $csmarketplaceHelper;

    /**
     * @var \Ced\CsMarketplace\Model\VordersFactory
     */
    protected $vordersFactory;

    /**
     * @var \Ced\CsMarketplace\Helper\Mail
     */
    protected $mailHelper;

    /**
     * @var \Ced\CsAdvTransaction\Model\OrderfeeFactory
     */
    protected $orderfeeFactory;

    /**
     * @var \Ced\CsAdvTransaction\Model\FeeFactory
     */
    protected $feeFactory;

    /**
     * OrderCancelAfter constructor.
     * @param \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper
     * @param \Ced\CsMarketplace\Model\VordersFactory $vordersFactory
     * @param \Ced\CsMarketplace\Helper\Mail $mailHelper
     * @param \Ced\CsAdvTransaction\Model\OrderfeeFactory $orderfeeFactory
     * @param \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory
     */
    public function __construct(
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        \Ced\CsMarketplace\Model\VordersFactory $vordersFactory,
        \Ced\CsMarketplace\Helper\Mail $mailHelper,
        \Ced\CsAdvTransaction\Model\OrderfeeFactory $orderfeeFactory,
        \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory
    )
    {
        $this->csmarketplaceHelper = $csmarketplaceHelper;
        $this->vordersFactory = $vordersFactory;
        $this->mailHelper = $mailHelper;
        $this->orderfeeFactory = $orderfeeFactory;
        $this->feeFactory = $feeFactory;
    }

    /**
     * Cancel the asscociated vendor order
     *
     * @param Varien_Object $observer
     * @return Ced_CsMarketplace_Model_Observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $order = $observer->getEvent()->getOrder();
        $this->csmarketplaceHelper->logProcessedData($order->getData('increment_id'), \Ced\CsMarketplace\Helper\Data::SALES_ORDER_CANCELED);
        try {
            $vorders = $this->vordersFactory->create()
                ->getCollection()
                ->addFieldToFilter('order_id', array('eq' => $order->getIncrementId()));

            if (count($vorders) > 0) {
                foreach ($vorders as $vorder) {
                    if ($vorder->canCancel()) {

                        $vorder->setOrderPaymentState(\Magento\Sales\Model\Order\Invoice::STATE_CANCELED);
                        $vorder->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_CANCELED);
                        $vorder->save();
                        $this->cancelCase($vorder->getId(), $vorder->getOrderId(), $vorder->getVendorId());
                    } else if ($vorder->canMakeRefund()) {
                        $vorder->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUND);
                        $vorder->save();
                    }
                    $this->csmarketplaceHelper->logProcessedData($vorder->getData(), \Ced\CsMarketplace\Helper\Data::VORDER_CANCELED);
                    $this->mailHelper->sendOrderEmail($order, \Ced\CsMarketplace\Model\Vorders::ORDER_CANCEL_STATUS, $vorder->getVendorId(), $vorder);

                }

            }
            return $this;


        } catch (Exception $e) {
            $this->csmarketplaceHelper->logException($e);
        }
    }


    /**
     * advanced transaction code
     */
    public function cancelCase($id, $incrementId, $vendorId)
    {
        $vorders = $this->vordersFactory->create()->load($id);

        $orderTotal = $vorders['order_total'];
        $orderFee = $this->orderfeeFactory->create()->getCollection()
            ->addfieldToFilter('vendor_id', $vendorId)
            ->addfieldToFilter('order_id', $incrementId);

        foreach ($orderFee as $fees) {
            $this->orderfeeFactory->create()->load($fees->getId())->delete();
        }

        $fees = $this->feeFactory->create()->getCollection()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('order_state', 3);

        $totalFee = 0;
        foreach ($fees as $fee) {
            $orderFees = $this->orderfeeFactory->create();
            if ($fee->getType() == "fixed") {
                $totalFee = $totalFee + $fee->getValue();

                $orderFees->setData('vendor_id', $vendorId);
                $orderFees->setData('fee_id', $fee->getFieldCode());
                $orderFees->setData('order_id', $incrementId);
                $orderFees->setData('status', '0');
                $orderFees->setData('amount', $fee->getValue());
                $orderFees->setData('type', 'fixed');
                $orderFees->save();
            } else {

                $totalFee = $totalFee + $orderTotal * $fee->getValue() / 100;

                $orderFees->setData('vendor_id', $vendorId);
                $orderFees->setData('fee_id', $fee->getFieldCode());
                $orderFees->setData('order_id', $incrementId);
                $orderFees->setData('status', '0');
                $orderFees->setData('amount', $orderTotal * $fee->getValue() / 100);
                $orderFees->setData('type', 'percentage');
                $orderFees->save();
            }

        }
        $vPay = 0;
        $vorders = $this->vordersFactory->create()->load($id);
        if ($totalFee > 0) {
            $vPay = '-' . $totalFee;
            $vorders->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUND);
        }

        $vorders->setVendorEarn($vPay);
        $vorders->save();
    }

}


