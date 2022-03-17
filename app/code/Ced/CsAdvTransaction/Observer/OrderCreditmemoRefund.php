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
 * Class OrderCreditmemoRefund
 * @package Ced\CsAdvTransaction\Observer
 */
Class OrderCreditmemoRefund implements ObserverInterface
{

    /**
     * @var \Ced\CsMarketplace\Model\VordersFactory
     */
    protected $vordersFactory;

    /**
     * @var \Ced\CsAdvTransaction\Helper\Data
     */
    protected $advHelper;

    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $csmarketplaceHelper;

    /**
     * @var \Ced\CsAdvTransaction\Model\OrderfeeFactory
     */
    protected $orderfeeFactory;

    /**
     * @var \Ced\CsAdvTransaction\Model\FeeFactory
     */
    protected $feeFactory;

    /**
     * OrderCreditmemoRefund constructor.
     * @param \Ced\CsMarketplace\Model\VordersFactory $vordersFactory
     * @param \Ced\CsAdvTransaction\Helper\Data $advHelper
     * @param \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper
     * @param \Ced\CsAdvTransaction\Model\OrderfeeFactory $orderfeeFactory
     * @param \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory
     */
    public function __construct(
        \Ced\CsMarketplace\Model\VordersFactory $vordersFactory,
        \Ced\CsAdvTransaction\Helper\Data $advHelper,
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        \Ced\CsAdvTransaction\Model\OrderfeeFactory $orderfeeFactory,
        \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory
    )
    {
        $this->vordersFactory = $vordersFactory;
        $this->advHelper = $advHelper;
        $this->csmarketplaceHelper = $csmarketplaceHelper;
        $this->orderfeeFactory = $orderfeeFactory;
        $this->feeFactory = $feeFactory;
    }

    /**
     * Refund the asscociated vendor order
     *
     * @param Varien_Object $observer
     * @return Ced_CsMarketplace_Model_Observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $order = $observer->getDataObject();
        try {
            if ($order->getState() == \Magento\Sales\Model\Order::STATE_CLOSED) {
                $vorders = $this->vordersFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('order_id', array('eq' => $order->getIncrementId()));
                if (count($vorders) > 0) {
                    foreach ($vorders as $vorder) {
                        if ($vorder->canCancel()) {
                            $vorder->setOrderPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUNDED);
                            $vorder->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_CANCELED);
                            if ($this->advHelper->getOrderPaymentType($vorder->getOrderId()) == "PostPaid") {
                                $vorder->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUND);
                            }

                            $vorder->save();
                            $this->refundCancelCase($vorder->getId(), $vorder->getOrderId(), $vorder->getVendorId());
                        } else if ($vorder->canMakeRefund()) {
                            $vorder->setOrderPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUNDED);
                            $vorder->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUND);
                            $vorder->save();

                            $this->refundCase($vorder->getId(), $vorder->getOrderId(), $vorder->getVendorId());
                        }
                    }

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
    public function refundCase($id, $incrementId, $vendorId)
    {
        $vorders = $this->vordersFactory->create()->load($id);

        $orderTotal = $vorders['order_total'];
        $orderFee = $this->orderfeeFactory->create()->getCollection()
            ->addfieldToFilter('vendor_id', $vendorId)
            ->addfieldToFilter('order_id', $incrementId);

        foreach ($orderFee as $fees) {
            $this->orderfeeFactory->create()->load($fees->getId())->setStatus(1)->save();
        }

        $fees = $this->feeFactory->create()->getCollection()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('order_state', 4);

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
        $orderIds = [];
        $orderIds[] = $incrementId;
        if ($totalFee > 0) {
            $vPay = $vorders->getVendorEarn() + $totalFee;
            $vPay = '-' . $vPay;
        } else {
            $vPay = $vorders->getVendorEarn();
            $vPay = '-' . $vPay;
        }
        $vorders = $this->vordersFactory->create()->load($id);
        $vorders->setVendorEarn($vPay);
        $vorders->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUND);
        $vorders->save();

    }

    /**
     * @param $id
     * @param $incrementId
     * @param $vendorId
     */
    public function refundCancelCase($id, $incrementId, $vendorId)
    {
        $vorders = $this->vordersFactory->create()->load($id);

        $postPaidAmount = $this->advHelper->getPostPaidAmount($vorders);

        $orderTotal = $vorders['order_total'];
        $orderFee = $this->orderfeeFactory->create()->getCollection()
            ->addfieldToFilter('vendor_id', $vendorId)
            ->addfieldToFilter('order_id', $incrementId);

        foreach ($orderFee as $fees) {
            $this->orderfeeFactory->create()->load($fees->getId())->setStatus(1)->save();
        }

        $fees = $this->feeFactory->create()->getCollection()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('order_state', 4);

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
        if ($totalFee > 0) {
            $vPay = $postPaidAmount['post_paid'] + $totalFee;
            $vPay = '-' . $vPay;
        } else {
            $vPay = '-' . $postPaidAmount['post_paid'];
        }
        $vorders = $this->vordersFactory->create()->load($id);
        $vorders->setVendorEarn($vPay);
        $vorders->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUND);
        $vorders->save();

    }
}    
