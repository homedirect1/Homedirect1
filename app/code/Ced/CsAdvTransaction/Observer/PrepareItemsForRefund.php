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
 * @package     Ced_CsTransaction
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class PrepareItemsForRefund
 * @package Ced\CsAdvTransaction\Observer
 */
class PrepareItemsForRefund implements ObserverInterface
{
    /**
     * @var \Ced\CsMarketplace\Model\Vorders
     */
    protected $_vorders;

    /**
     * @var \Ced\CsOrder\Helper\Data
     */
    protected $_csorderHelper;

    /**
     * @var \Ced\CsTransaction\Model\Items
     */
    protected $_vtorders;

    /**
     * @var \Ced\CsTransaction\Helper\Data
     */
    protected $helper;

    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $_csMarketplaceHelper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    protected $itemFactory;

    /**
     * @var \Ced\CsMarketplace\Model\VpaymentFactory
     */
    protected $vpaymentFactory;

    /**
     * @var \Ced\CsMarketplace\Model\Vpayment\RequestedFactory
     */
    protected $requestedFactory;

    /**
     * PrepareItemsForRefund constructor.
     * @param \Ced\CsOrder\Helper\Data $csorderHelper
     * @param \Ced\CsTransaction\Model\Items $vtorders
     * @param \Ced\CsTransaction\Helper\Data $helper
     * @param \Ced\CsMarketplace\Helper\Data $helperData
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Sales\Model\Order\ItemFactory $itemFactory
     * @param \Ced\CsMarketplace\Model\VpaymentFactory $vpaymentFactory
     * @param \Ced\CsMarketplace\Model\Vpayment\RequestedFactory $requestedFactory
     */
    public function __construct(
        \Ced\CsOrder\Helper\Data $csorderHelper,
        \Ced\CsTransaction\Model\Items $vtorders,
        \Ced\CsMarketplace\Model\Vorders $_vorders,
        \Ced\CsTransaction\Helper\Data $helper,
        \Ced\CsMarketplace\Helper\Data $helperData,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Sales\Model\Order\ItemFactory $itemFactory,
        \Ced\CsMarketplace\Model\VpaymentFactory $vpaymentFactory,
        \Ced\CsMarketplace\Model\Vpayment\RequestedFactory $requestedFactory
    )
    {
        $this->_csorderHelper = $csorderHelper;
        $this->_vtorders = $vtorders;
        $this->_vorders = $_vorders;
        $this->helper = $helper;
        $this->_csMarketplaceHelper = $helperData;
        $this->_request = $request;
        $this->itemFactory = $itemFactory;
        $this->vpaymentFactory = $vpaymentFactory;
        $this->requestedFactory = $requestedFactory;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if ($this->_csorderHelper->isActive()) {

            $creditmemo = $observer->getCreditmemo();
            $creditmemoVendor = array();
            $vendorId = false;
            $credit_memo_item = $this->_request->getParam('creditmemo');

            $creditMemoItems = array();
            foreach ($creditmemo->getAllItems() as $item) {
                try {
                    if ($item->getParentItem()) continue;
                    $vorderItem = $this->_vtorders
                        ->getCollection()
                        ->addFieldToFilter('order_item_id', array('eq' => $item->getOrderItemId()))
                        ->getFirstItem();


                    //commented for the non refunded item is forcing refund after one payment transaction
                    //$vorderItem->setQtyPendingToRefund($vorderItem->getQtyPendingToRefund()+$item->getQty());

                    $qrpBefore = $vorderItem->getQtyReadyToPay();

                    if ($item->getQty() - $qrpBefore >= 0)
                        $vorderItem->setQtyPendingToRefund($vorderItem->getQtyPendingToRefund() + $item->getQty() - $qrpBefore);


                    $qtyReadyToPay = $vorderItem->getQtyReadyToPay() - $item->getQty();
                    $vorderItem->setQtyReadyToPay($qtyReadyToPay < 0 ? 0 : $qtyReadyToPay);
                    $vorderItem->setTotalCreditmemoAmount($vorderItem->getTotalCreditmemoAmount() + $item->getRowTotal());
                    $vorderItem->save();
                    $vendorId = $vorderItem->getVendorId();
                    $vorderItem->setQtyForRefund($vorderItem);

                    //multi credit memo refund
                    //$vorderItem->getQtyReadyToRefund($vorderItem->getQtyReadyToRefund()+$item->getQty());

                    //item collection
                    $creditMemoItems[$item->getOrderItemId()] = $item->getQty();


                } catch (Exception $e) {

                    $this->_csMarketplaceHelper->logException($e);
                }
            }

            $order = $creditmemo->getOrder();
            $vorders = $this->_vorders
                ->getCollection()
                ->addFieldToFilter('order_id', array('eq' => $order->getIncrementId()));


            if (count($vorders) > 0) {
                foreach ($vorders as $vorder) {
                    try {
                        $qtyOrdered = 0;
                        $qtyRefunded = 0;
                        $refunded = 0;

                        $vendorId = $vorder->getData('vendor_id');
                        $vorderItems = $this->itemFactory->create()->getCollection()->addFieldToSelect('*')
                            ->addFieldToFilter('vendor_id', $vendorId)
                            ->addFieldToFilter('order_id', $order->getId());


                        foreach ($vorderItems as $item) {
                            foreach ($credit_memo_item as $item_id) {

                                if (isset($item_id[$item->getItemId()])) {
                                    $refunded = $item_id[$item->getItemId()];
                                    if (isset($item_id[$item->getItemId()]['qty'])) {
                                        $refunded = $item_id[$item->getItemId()]['qty'];
                                    }

                                }
                            }
                            if ($refunded == 0) {
                                $refunded = (int)$item->getData('qty_refunded');
                            }
                            $qtyOrdered += (int)$item->getQtyOrdered();
                            $qtyRefunded += (int)$refunded;

                        }


                        if ($qtyOrdered == $qtyRefunded) {

                            if ($vorder->getPaymentState() == 2) {
                                $vorder->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUND);
                            } else {

                                if ($vorder->getVendorEarn() < 0) {
                                    $vorder->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUND);
                                } elseif ($vorder->getVendorEarn() == 0) {
                                    $vorder->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_CANCELED);
                                }

                            }

                            $vendorpay = $this->vpaymentFactory->create()->getCollection()
                                ->addFieldToFilter('vendor_id', $vendorId)
                                ->addFieldToFilter('transaction_type', \Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_CREDIT)->getData();

                            foreach ($vendorpay as $key => $value) {
                                $amountDesc = json_decode($value['amount_desc'], true);
                                foreach ($amountDesc as $key => $vitem) {
                                    if ($key == $order->getId()) {
                                        $vorder->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUND);
                                    }
                                }
                            }
                            $vorder->save();
                            $requestedobj = $this->requestedFactory->create()->getCollection()
                                ->addFieldToFilter('vendor_id', $vendorId)
                                ->addFieldToFilter('order_id', $order->getIncrementId())->getData();

                            if (sizeof($requestedobj) > 0) {
                                foreach ($requestedobj as $key => $value) {
                                    $statuschange = $this->requestedFactory->create()->load($value['entity_id']);
                                    $statuschange->setData('status', \Ced\CsMarketplace\Model\Vpayment\Requested::PAYMENT_STATUS_CANCELED);
                                    $statuschange->save();
                                }
                            }
                        }

                    } catch (Exception $e) {
                        $this->_csMarketplaceHelper->logException($e);
                        echo $e->getMessage();
                        die;
                    }

                    //update requested amount
                    $vendorId = $vorder->getVendorId();
                    $vOrderId = $vorder->getId();

                    $amount = $this->helper->getTotalEarn($vOrderId, $vendorId);
                    $model = $this->requestedFactory->create()->loadByField(array('vendor_id', 'order_id'), array($vendorId, $vOrderId));
                    if ($model->getId()) {
                        $data = array('amount' => $amount, 'status' => \Ced\CsMarketplace\Model\Vpayment\Requested::PAYMENT_STATUS_REQUESTED, 'created_at' => date('Y-m-d H:i:s'));
                        $model->addData($data)->save();
                    }
                }
            }
        }
    }
}  