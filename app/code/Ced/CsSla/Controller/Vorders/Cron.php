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

namespace Ced\CsSla\Controller\Vorders;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;

/**
 * Class Cron
 * @package Ced\CsSla\Controller\Vorders
 */
class Cron extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var \Ced\CsSla\Helper\Data
     */
    protected $slaHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Ced\CsMarketplace\Model\VordersFactory
     */
    protected $vordersFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Ced\CsAdvTransaction\Model\FeeFactory
     */
    protected $feeFactory;

    /**
     * @var \Ced\CsAdvTransaction\Model\OrderfeeFactory
     */
    protected $orderfeeFactory;

    /**
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var \Ced\CsMarketplace\Model\VproductsFactory
     */
    protected $vproductsFactory;

    /**
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    protected $itemFactory;

    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Sales\Api\CreditmemoManagementInterface
     */
    protected $creditmemoManagement;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\CreditmemoSender
     */
    protected $creditmemoSender;

    /**
     * Cron constructor.
     * @param \Ced\CsSla\Helper\Data $slaHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Ced\CsMarketplace\Model\VordersFactory $vordersFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory
     * @param \Ced\CsAdvTransaction\Model\OrderfeeFactory $orderfeeFactory
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagement
     * @param \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory
     * @param \Magento\Sales\Model\Order\ItemFactory $itemFactory
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
     * @param \Magento\Sales\Api\CreditmemoManagementInterface $creditmemoManagement
     * @param \Magento\Sales\Model\Order\Email\Sender\CreditmemoSender $creditmemoSender
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlFactory $urlFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper
     * @param \Ced\CsMarketplace\Helper\Acl $aclHelper
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendor
     */
    public function __construct(
        \Ced\CsSla\Helper\Data $slaHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Ced\CsMarketplace\Model\VordersFactory $vordersFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory,
        \Ced\CsAdvTransaction\Model\OrderfeeFactory $orderfeeFactory,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory,
        \Magento\Sales\Model\Order\ItemFactory $itemFactory,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        \Magento\Sales\Api\CreditmemoManagementInterface $creditmemoManagement,
        \Magento\Sales\Model\Order\Email\Sender\CreditmemoSender $creditmemoSender,
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Session $customerSession,
        UrlFactory $urlFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        \Ced\CsMarketplace\Helper\Acl $aclHelper,
        \Ced\CsMarketplace\Model\VendorFactory $vendor
    )
    {
        parent::__construct(
            $context,
            $resultPageFactory,
            $customerSession,
            $urlFactory,
            $registry,
            $jsonFactory,
            $csmarketplaceHelper,
            $aclHelper,
            $vendor
        );

        $this->slaHelper = $slaHelper;
        $this->dateTime = $dateTime;
        $this->vordersFactory = $vordersFactory;
        $this->scopeConfig = $scopeConfig;
        $this->orderFactory = $orderFactory;
        $this->productFactory = $productFactory;
        $this->feeFactory = $feeFactory;
        $this->orderfeeFactory = $orderfeeFactory;
        $this->orderManagement = $orderManagement;
        $this->vproductsFactory = $vproductsFactory;
        $this->itemFactory = $itemFactory;
        $this->creditmemoLoader = $creditmemoLoader;
        $this->request = $context->getRequest();
        $this->creditmemoManagement = $creditmemoManagement;
        $this->creditmemoSender = $creditmemoSender;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->max_dispatch();
    }

    public function addDispatchFee()
    {
        $slaenable = $this->slaHelper->getStoreConfig('ced_csmarketplace/general/cssla');
        if (!$slaenable) {
            return;
        }
        $addDispatchfee = $this->slaHelper->getStoreConfig('ced_csmarketplace/vsla/dispath_fee');
        $date = $this->dateTime->gmtDate();
        $days_ago = date('Y-m-d h:i:s', strtotime('-' . $addDispatchfee . ' days', strtotime($date)));

        $vorders = $this->vordersFactory->create()->getCollection()->getData();
        foreach ($vorders as $vorder) {
            $payShip = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_shipping');
            if ($payShip && !empty($vorder['code'])) {
                $count = 0;
                $totalFee = 0;
                if ($vorder['order_status'] == \Ced\CsSla\Helper\Data::STATE_CONFIRMED) {
                    $orderItems = $this->orderFactory->create()->load($vorder['order_id'], 'increment_id')->getAllItems();
                    $vendorId = $vorder['vendor_id'];
                    $incrementId = $vorder['order_id'];
                    $orderTotal = $vorder['order_total'];
                    foreach ($orderItems as $item) {
                        if ($item->getVendorId() != $vorder['vendor_id']) {
                            continue;
                        }
                        $dispatchTime = (int)$this->productFactory->create()->load($item->getProductId())->getDispatchTime();
                        if ($dispatchTime == 0) {
                            $flag = false;
                            continue;
                        } else {
                            $flag = true;
                            $date = $this->dateTime->gmtDate();
                            $days_ago = date('Y-m-d h:i:s', strtotime('-' . $dispatchTime . ' days', strtotime($date)));
                            $days_ago = 1;
                            if ($item->getCreatedAt() >= $days_ago) {
                                if ($item->getQtyOrdered() != $item->getShipped()) {
                                    $fee = $this->feeFactory->create()->getCollection()
                                        ->addFieldToFilter('status', 1)
                                        ->addFieldToFilter('field_code', ['in' => 'sla_dispatch_fee'])->getFirstItem();

                                    $currentfees = $this->orderfeeFactory->create()->getCollection()
                                        ->addFieldToFilter('vendor_id', $vendorId)
                                        ->addFieldToFilter('order_id', $incrementId)
                                        ->addFieldToFilter('fee_id', 'sla_dispatch_fee')->getFirstItem()->getData();

                                    $disatchFee = count($currentfees);
                                    $count = $count + 1;
                                    if ($disatchFee) {
                                        $orderFeeId = $currentfees['id'];

                                    }
                                }
                            }
                        }

                        if ($flag && !isset($orderFeeId)) {

                            $orderFees = $this->orderfeeFactory->create();
                            if ($fee->getType() == "fixed") {
                                $totalFee = $count * ($fee->getValue());

                                $orderFees->setData('vendor_id', $vendorId);
                                $orderFees->setData('fee_id', $fee->getFieldCode());
                                $orderFees->setData('order_id', $incrementId);
                                $orderFees->setData('status', '0');
                                $orderFees->setData('amount', $fee->getValue());
                                $orderFees->setData('type', 'fixed');
                                $orderFees->save();
                            } else {

                                $totalFee = $count * ($orderTotal * $fee->getValue() / 100);

                                $orderFees->setData('vendor_id', $vendorId);
                                $orderFees->setData('fee_id', $fee->getFieldCode());
                                $orderFees->setData('order_id', $incrementId);
                                $orderFees->setData('status', '0');
                                $orderFees->setData('amount', $orderTotal * $fee->getValue() / 100);
                                $orderFees->setData('type', 'percentage');
                                $orderFees->save();
                            }

                            $Vorders = $this->vordersFactory->create()->load($vorder['id']);
                            $VID = $Vorders->getVendorId();
                            $vPay = $Vorders->getVendorEarn();
                            if ($totalFee > 0) {
                                $vPay = $vPay - $totalFee;
                            }
                            $Vorders->setVendorEarn($vPay);
                            $Vorders->save();

                            $email = $this->orderFactory->create()->load($vorder['order_id'], 'increment_id')->getCustomerEmail();
                            $description = '';
                            $vendor = $this->vendor->create()->load($VID);

                            $this->slaHelper->sendTransactional($email, $description, $vendor->getName(), $vendor->getEmail());

                        } else {

                            $orderFees = $this->orderfeeFactory->create()->load($orderFeeId);
                            if ($fee->getType() == "fixed") {
                                $totalFee = $count * ($fee->getValue());

                                $orderFees->setData('amount', $fee->getValue());
                                $orderFees->save();
                            } else {

                                $totalFee = $count * ($orderTotal * $fee->getValue() / 100);

                                $orderFees->setData('amount', $orderTotal * $fee->getValue() / 100);
                                $orderFees->save();
                            }

                            $OFees = $this->orderfeeFactory->create()->getCollection()
                                ->addFieldToFilter('status', 0)
                                ->addFieldToFilter('order_id', $incrementId)
                                ->addFieldToFilter('vendor_id', $vendorId)
                                ->addFieldToFilter('fee_id', ['nin' => 'sla_dispatch_fee'])->getData();

                            $vPay = 0;
                            foreach ($OFees as $OFee) {
                                $vPay = $vPay + $OFee['amount'];
                            }
                            $vPay = $vPay + $vorder['shop_commission_fee'];
                            $vPay = $vorder['order_total'] - $vPay;

                            if ($totalFee > 0) {
                                $vPay = $vPay - $totalFee;
                            }

                            $Vorders = $this->vordersFactory->create()->load($vorder['id']);
                            $VID = $Vorders->getVendorId();
                            $Vorders->setVendorEarn($vPay);
                            $Vorders->save();

                            $email = $this->orderFactory->create()->load($vorder['order_id'], 'increment_id')->getCustomerEmail();
                            $description = '';
                            $vendor = $this->vendor->create()->load($VID);

                            $this->slaHelper->sendTransactional($email, $description, $vendor->getName(), $vendor->getEmail());
                        }
                    }
                }
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function autoCancel()
    {

        $slaenable = $this->scopeConfig->getValue('ced_csmarketplace/general/cssla');
        if (!$slaenable) {
            return;
        }
        $autocancel = 0;
        $date = $this->dateTime->gmtDate();
        $days_ago = date('Y-m-d h:i:s', strtotime('-' . $autocancel . ' days', strtotime($date)));

        $vorders = $this->vordersFactory->create()->getCollection()
            ->addFieldToFilter('order_status', ['null' => true])
            ->addFieldToFilter('created_at', ['lt' => $days_ago]);

        foreach ($vorders as $vorder) {
            if (!$vorder->canInvoice() && !$vorder->canShip()) {
                continue;
            } elseif ($vorder->canCancel()) {
                $vorderModel = $this->vordersFactory->create()->load($vorder['id']);
                $incrementId = $vorderModel->getOrderId();

                $order = $this->orderFactory->create()->load($incrementId, 'increment_id');

                $vOrderCollection = $this->vordersFactory->create()->getCollection()
                    ->addFieldToFilter('order_id', $incrementId)->getData();

                if (sizeof($vOrderCollection) == 1) {
                    try {
                        $this->orderManagement->cancel($order->getEntityId());
                    } catch (\Exception $e) {
                        die($e);
                    }
                    $this->orderManagement->cancel($order->getEntityId());
                    $isVendorOrder = true;
                } else {
                    $isVendorOrder = false;
                    $allItems = $order->getAllItems();
                    foreach ($allItems as $item) {
                        if (!($item->getQtyCanceled() > 0)) {
                            $vendorId = $this->vproductsFactory->create()->getVendorIdByProduct($item->getProductId());
                            if ($vendorId == $this->session->getVendorId()) {

                                $itemModel = $this->itemFactory->create()->load($item->getItemId());
                                $itemModel->setQtyCanceled($item->getQtyOrdered());
                                $itemModel->save();
                                $isVendorOrder = true;
                            }
                        }
                    }
                }
                if ($isVendorOrder) {

                    $vorderModel = $this->vordersFactory->create()->load($vorder['id']);
                    $VID = $vorderModel->getVendorId();
                    $vorderModel->setOrderStatus(\Ced\CsSla\Helper\Data::STATE_CANCELLED);
                    $vorderModel->setOrderPaymentState(3);
                    $vorderModel->save();

                    $email = $this->orderFactory->create()->load($vorder['order_id'], 'increment_id')->getCustomerEmail();
                    $description = '';
                    $vendor = $this->vendor->create()->load($VID);

                    $this->slaHelper->sendTransactionalforCancel($email, $description, $vendor->getName(), $vendor->getEmail());
                }

            } elseif ($vorder->canCreditmemo()) {

                $vorder = $this->vordersFactory->create()->load($vorder['id']);
                $vendorId = $vorder->getVendorId();
                $orderId = $vorder->getOrder()->getId();

                $this->creditmemoLoader->setOrderId($orderId);

                $order = $this->orderFactory->create()->load($orderId);
                $creditMemoItems = [];
                $items = $order->getAllItems();
                foreach ($items as $item) {
                    if ($item->getVendorId() == $vendorId) {
                        $creditMemoItems['items'][$item->getItemId()]['qty'] = round($item->getQtyOrdered());
                    }
                }

                $this->creditmemoLoader->setCreditmemoId('');
                $this->creditmemoLoader->setCreditmemo($creditMemoItems);
                $this->creditmemoLoader->setInvoiceId('');
                $creditmemo = $this->creditmemoLoader->load();
                $this->request->setParam('creditmemo', $creditMemoItems);

                try {
                    if ($creditmemo) {

                        $error = [];
                        if (!$creditmemo->isValidGrandTotal()) {
                            $error[][$id] = 'The credit memo\'s total must be positive.';
                        }

                        $data = [];
                        $data['do_offline'] = 0;
                        $data['send_email'] = 1;
                        $this->creditmemoManagement->refund($creditmemo, (bool)$data['do_offline'], !empty($data['send_email']));

                        if (!empty($data['send_email'])) {

                            $this->creditmemoSender->send($creditmemo);
                        }

                        $vorderModel = $this->vordersFactory->create()->load($id);
                        $VID = $vorderModel->getVendorId();
                        $vorderModel->setOrderStatus(\Ced\CsSla\Helper\Data::STATE_CANCELLED);
                        $vorderModel->setOrderPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUNDED);
                        $vorderModel->save();

                        $email = $this->orderFactory->create()->load($vorder['order_id'], 'increment_id')->getCustomerEmail();
                        $description = '';
                        $vendor = $this->vendor->create()->load($VID);

                        $this->slaHelper->sendTransactionalforCancel($email, $description, $vendor->getName(), $vendor->getEmail());
                    } else {
                        die();
                    }
                } catch (\Exception $e) {
                    die($e);


                }


            }
        }

    }

    /**
     * @throws \Exception
     */
    public function max_dispatch()
    {

        $slaenable = $this->scopeConfig->getValue('ced_csmarketplace/general/cssla');
        if (!$slaenable) {
            return;
        }
        $maxdispatch = $this->slaHelper->getStoreConfig('ced_csmarketplace/vsla/auto_cancel');
        $maxdispatch = 0;
        $date = $this->dateTime->gmtDate();
        $days_ago = date('Y-m-d h:i:s', strtotime('-' . $maxdispatch . ' days', strtotime($date)));

        $vorders = $this->vordersFactory->create()->getCollection()
            ->addFieldToFilter('order_status', \Ced\CsSla\Helper\Data::STATE_CONFIRMED)
            ->addFieldToFilter('created_at', ['lt' => $days_ago]);

        foreach ($vorders as $vorder) {
            if (!$vorder->canInvoice() && !$vorder->canShip()) {
                continue;
            }
            if ($vorder->canShip()) {
                if ($vorder->canCancel()) {
                    $vorderModel = $this->vordersFactory->create()->load($vorder['id']);
                    $incrementId = $vorderModel->getOrderId();
                    $order = $this->orderFactory->create()->load($incrementId, 'increment_id');

                    $vOrderCollection = $this->vordersFactory->create()->getCollection()
                        ->addFieldToFilter('order_id', $incrementId)->getData();

                    if (sizeof($vOrderCollection) == 1) {
                        $this->orderManagement->cancel($order->getEntityId());
                        $isVendorOrder = true;
                    } else {
                        $isVendorOrder = false;
                        $allItems = $order->getAllItems();
                        foreach ($allItems as $item) {
                            if (!($item->getQtyCanceled() > 0)) {
                                $vendorId = $this->vproductsFactory->create()->getVendorIdByProduct($item->getProductId());
                                if ($vendorId == $this->session->getVendorId()) {

                                    $itemModel = $this->itemFactory->create()->load($item->getItemId());
                                    $itemModel->setQtyCanceled($item->getQtyOrdered());
                                    $itemModel->save();
                                    $isVendorOrder = true;
                                }
                            }
                        }
                    }
                    if ($isVendorOrder) {

                        $vorderModel = $this->vordersFactory->create()->load($vorder['id']);
                        $VID = $vorderModel->getVendorId();
                        $vorderModel->setOrderStatus(\Ced\CsSla\Helper\Data::STATE_CANCELLED);
                        $vorderModel->setOrderPaymentState(3);
                        $vorderModel->save();

                        $email = $this->orderFactory->create()->load($vorder['order_id'], 'increment_id')->getCustomerEmail();
                        $description = '';
                        $vendor = $this->vendor->create()->load($VID);

                        $this->slaHelper->sendTransactionalforCancel($email, $description, $vendor->getName(), $vendor->getEmail());


                    }

                } elseif ($vorder->canCreditmemo()) {

                    $vorder = $this->vordersFactory->create()->load($vorder['id']);
                    $vendorId = $vorder->getVendorId();
                    $orderId = $vorder->getOrder()->getId();

                    $this->creditmemoLoader->setOrderId($orderId);

                    $order = $this->orderFactory->create()->load($orderId);
                    $creditMemoItems = [];
                    $items = $order->getAllItems();
                    foreach ($items as $item) {
                        if ($item->getVendorId() == $vendorId) {
                            $creditMemoItems['items'][$item->getItemId()]['qty'] = round($item->getQtyOrdered());
                        }
                    }

                    $this->creditmemoLoader->setCreditmemoId('');
                    $this->creditmemoLoader->setCreditmemo($creditMemoItems);
                    $this->creditmemoLoader->setInvoiceId('');
                    $creditmemo = $this->creditmemoLoader->load();
                    $this->request->setParam('creditmemo', $creditMemoItems);

                    try {
                        if ($creditmemo) {

                            $error = [];
                            if (!$creditmemo->isValidGrandTotal()) {
                                $error[][$id] = 'The credit memo\'s total must be positive.';

                            }

                            $data = [];
                            $data['do_offline'] = 0;
                            $data['send_email'] = 1;
                            $this->creditmemoManagement->refund($creditmemo, (bool)$data['do_offline'], !empty($data['send_email']));


                            if (!empty($data['send_email'])) {

                                $this->creditmemoSender->send($creditmemo);
                            }

                            $vorderModel = $this->vordersFactory->create()->load($id);
                            $VID = $vorderModel->getVendorId();
                            $vorderModel->setOrderStatus(\Ced\CsSla\Helper\Data::STATE_CANCELLED);
                            $vorderModel->setOrderPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUNDED);
                            $vorderModel->save();

                            $email = $this->orderFactory->create()->load($vorder['order_id'], 'increment_id')->getCustomerEmail();
                            $description = '';
                            $vendor = $this->vendor->create()->load($VID);

                            $this->slaHelper->sendTransactionalforCancel($email, $description, $vendor->getName(), $vendor->getEmail());

                        } else {
                            die();
                        }
                    } catch (\Exception $e) {
                        die($e);
                    }
                }
            }
        }
    }
}




 
 