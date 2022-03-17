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
 * Class ChangeOrderPaymentState
 * @package Ced\CsAdvTransaction\Observer
 */
class ChangeOrderPaymentState implements ObserverInterface
{

    /**
     * @var
     */
    protected $request;

    /**
     * @var \Ced\CsMarketplace\Model\VordersFactory
     */
    protected $vordersFactory;

    /**
     * @var \Ced\CsOrder\Helper\Data
     */
    protected $csorderHelper;

    /**
     * @var \Ced\CsMarketplace\Model\VproductsFactory
     */
    protected $vproductsFactory;

    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $csmarketplaceHelper;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    protected $itemFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * ChangeOrderPaymentState constructor.
     * @param \Ced\CsMarketplace\Model\VordersFactory $vordersFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Ced\CsOrder\Helper\Data $csorderHelper
     * @param \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory
     * @param \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\Order\ItemFactory $itemFactory
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        \Ced\CsMarketplace\Model\VordersFactory $vordersFactory,
        \Magento\Framework\App\Request\Http $request,
        \Ced\CsOrder\Helper\Data $csorderHelper,
        \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory,
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\ItemFactory $itemFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    )
    {
        $this->vordersFactory = $vordersFactory;
        $this->_request = $request;
        $this->csorderHelper = $csorderHelper;
        $this->vproductsFactory = $vproductsFactory;
        $this->csmarketplaceHelper = $csmarketplaceHelper;
        $this->orderFactory = $orderFactory;
        $this->itemFactory = $itemFactory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     *Set vendor naem and url to product incart
     *
     * @param $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if ($this->csorderHelper->isActive()) {

            $invoice = $observer->getDataObject();
            foreach ($invoice->getAllItems() as $item) {
                $product_id = $item->getProductId();
                $vendors[] = $this->vproductsFactory->create()->getVendorIdByProduct($product_id);
            }
            if (isset($vendors)) {
                $vendors = array_unique($vendors);
            }


            $order = $invoice->getOrder();

            $this->csmarketplaceHelper->logProcessedData($order->getData('increment_id'), \Ced\CsMarketplace\Helper\Data::SALES_ORDER_PAYMENT_STATE_CHANGED);

            $vorders = $this->vordersFactory->create()
                ->getCollection()
                ->addFieldToFilter('order_id', array('eq' => $order->getIncrementId()));

            $invoiced_item = $this->_request->getPost('invoice');

            $origOrderId = $this->orderFactory->create()->load($order->getIncrementId(), 'increment_id')->getId();
            $orderItem = $this->itemFactory->create()->getCollection()->addFieldToFilter('order_id', $origOrderId)->addFieldToFilter('vendor_id', $vendorId);
            foreach ($orderItem as $item) {

                if ($item->getQtyToInvoice() > 0 && !$item->getLockedDoInvoice()) {
                    $canInvoice = true;
                }
                if ($item->getQtyToShip() > 0 && !$item->getIsVirtual() && !$item->getLockedDoShip()) {
                    $canShip = true;
                }


            }
            if (count($allItems) == count($orderItem)) {
                $canShip = false;
            }


            if (count($vorders) > 0) {
                foreach ($vorders as $vorder) {
                    try {

                        $qtyOrdered = 0;
                        $qtyInvoiced = 0;
                        $invoiced = 0;

                        $vendorId = $vorder->getData('vendor_id');

                        $vorderItems = $this->itemFactory->create()->getCollection()->addFieldToSelect('*')
                            ->addFieldToFilter('vendor_id', $vendorId)
                            ->addFieldToFilter('order_id', $order->getId());

                        foreach ($vorderItems as $item) {
                            if (isset($invoiced_item)) {
                                foreach ($invoiced_item as $item_id) {
                                    if (isset($item_id[$item->getItemId()])) {
                                        $invoiced = $item_id[$item->getItemId()] + (int)$item->getData('qty_invoiced');
                                    }
                                }
                            }


                            if ($invoiced == 0) {
                                $invoiced = (int)$item->getData('qty_invoiced');
                            }

                            $qtyOrdered += (int)$item->getQtyOrdered();
                            $qtyInvoiced += (int)$invoiced;

                        }

                        $resource = $this->resourceConnection;
                        $connection = $resource->getConnection();
                        $tableName = $resource->getTableName('ced_csmarketplace_vendor_sales_order'); //gives table name with prefix

                        if ($qtyOrdered > $qtyInvoiced) {

                            if ($qtyInvoiced != 0) {
                                $sql = "Update " . $tableName . " Set order_payment_state = " . \Ced\CsOrder\Model\Invoice::STATE_PARTIALLY_PAID . " where order_id = {$vorder->getOrderId()} and vendor_id = {$vorder->getVendorId()}";
                            } else {
                                $sql = "Update " . $tableName . " Set order_payment_state = " . \Ced\CsOrder\Model\Invoice::ORDER_NEW_STATUS . " where order_id = {$vorder->getOrderId()} and vendor_id = {$vorder->getVendorId()}";
                            }
                            $connection->query($sql);
                            //$vorder->setOrderPaymentState(\Ced\CsOrder\Model\Invoice::STATE_PARTIALLY_PAID);
                        } else {

                            if ($vorder->getCode() == NULL) {
                                $Vostatus = "complete";
                            } else {
                                $Vostatus = "invoiced";
                            }

                            $vorders = $this->vordersFactory->create()->load($vorder->getId());
                            $vorders->setOrderPaymentState(\Magento\Sales\Model\Order\Invoice::STATE_PAID);
                            $vorders->setVorderStatus($Vostatus);
                            $vorders->save();

                        }


                        $this->csmarketplaceHelper->logProcessedData($vorder->getData(), \Ced\CsMarketplace\Helper\Data::VORDER_PAYMENT_STATE_CHANGED);

                    } catch (Exception $e) {
                        $this->csmarketplaceHelper->logException($e);
                        echo $e->getMessage();
                        die;
                    }
                }

            }
            return $this;
        } else {
            $invoice = $observer->getDataObject();
            $order = $invoice->getOrder();
            if ($order->getBaseTotalDue() == 0) {
                $vorders = $this->vordersFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('order_id', array('eq' => $order->getIncrementId()));
                if (count($vorders) > 0) {
                    foreach ($vorders as $vorder) {
                        try {
                            if ($vorder->getCode() == NULL) {
                                $Vostatus = "complete";
                            } else {
                                $Vostatus = "invoiced";
                            }
                            $vorder->setOrderPaymentState(\Magento\Sales\Model\Order\Invoice::STATE_PAID);
                            //$vorder->setVorderStatus($Vostatus);
                            $vorder->save();
                        } catch (Exception $e) {
                            echo "exception: " . $e->getMessage();
                            die;
                        }
                    }
                }
            }
            return $this;
        }
    }
}  
