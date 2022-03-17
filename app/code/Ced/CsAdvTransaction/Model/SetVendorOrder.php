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

namespace Ced\CsAdvTransaction\Model;

use Magento\UrlRewrite\Model\UrlRewriteFactory;

/**
 * Class SetVendorOrder
 * @package Ced\CsAdvTransaction\Model
 */
class SetVendorOrder extends \Ced\CsMarketplace\Model\AbstractModel
{

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Ced\CsMarketplace\Model\VordersFactory
     */
    protected $vordersFactory;

    /**
     * @var ResourceModel\Fee\CollectionFactory
     */
    protected $feeCollection;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @var OrderfeeFactory
     */
    protected $orderfeeFactory;

    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $csmarketplaceHelper;

    /**
     * @var \Ced\CsMarketplace\Helper\Mail
     */
    protected $mailHelper;

    /**
     * SetVendorOrder constructor.
     * @param \Ced\CsMarketplace\Model\VordersFactory $vordersFactory
     * @param ResourceModel\Fee\CollectionFactory $feeCollection
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param OrderfeeFactory $orderfeeFactory
     * @param \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper
     * @param \Ced\CsMarketplace\Helper\Mail $mailHelper
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product\Url $url
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Ced\CsMarketplace\Model\VordersFactory $vordersFactory,
        \Ced\CsAdvTransaction\Model\ResourceModel\Fee\CollectionFactory $feeCollection,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Ced\CsAdvTransaction\Model\OrderfeeFactory $orderfeeFactory,
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        \Ced\CsMarketplace\Helper\Mail $mailHelper,
        UrlRewriteFactory $urlRewriteFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\Url $url,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_eventManager = $context->getEventDispatcher();
        $this->vordersFactory = $vordersFactory;
        $this->directoryHelper = $directoryHelper;
        $this->feeCollection = $feeCollection;
        $this->orderfeeFactory = $orderfeeFactory;
        $this->csmarketplaceHelper = $csmarketplaceHelper;
        $this->mailHelper = $mailHelper;
        parent::__construct($urlRewriteFactory, $storeManager, $url, $context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @param $order
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setVendorOrder($order)
    {
        try {
            $vorder = $this->vordersFactory->create()->getCollection()
                ->addFieldToFilter('order_id', $order->getIncrementId())->getFirstItem();

            if ($vorder->getId()) {
                return $this;
            }
            $baseToGlobalRate = $order->getBaseToGlobalRate() ? $order->getBaseToGlobalRate() : 1;
            $vendorsBaseOrder = [];
            $vendorQty = [];

            foreach ($order->getAllItems() as $key => $item) {
                $vendor_id = $item->getVendorId();
                if ($vendor_id) {
                    if ($item->getHasChildren() && $item->getProductType() != 'configurable') {
                        continue;
                    }

                    $price = $item->getBaseRowTotal()
                        + $item->getBaseTaxAmount()
                        + $item->getBaseHiddenTaxAmount()
                        + $item->getBaseWeeeTaxAppliedRowAmount()
                        - $item->getBaseDiscountAmount();

                    $vendorsBaseOrder[$vendor_id]['order_total'] = isset($vendorsBaseOrder[$vendor_id]['order_total']) ? ($vendorsBaseOrder[$vendor_id]['order_total'] + $price) : $price;
                    $vendorsBaseOrder[$vendor_id]['item_commission'][$item->getQuoteItemId()] = $price;
                    $vendorsBaseOrder[$vendor_id]['order_items'][] = $item;
                    $vendorQty[$vendor_id] = isset($vendorQty[$vendor_id]) ? $vendorQty[$vendor_id] + $item->getQty() : $item->getQtyOrdered();
                    $logData = $item->getData();
                    unset($logData['product']);
                }
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Error Occured While Placing The Order'));
        }


        foreach ($vendorsBaseOrder as $vendorId => $baseOrderTotal) {

            try {
                $qty = isset($vendorQty[$vendorId]) ? $vendorQty[$vendorId] : 0;
                $vorder = $this->vordersFactory->create();
                $vorder->setVendorId($vendorId);
                $vorder->setCurrentOrder($order);
                $vorder->setOrderId($order->getIncrementId());
                $vorder->setCurrency($order->getOrderCurrencyCode());
                $vorder->setOrderTotal($this->directoryHelper
                    ->currencyConvert($baseOrderTotal['order_total'], $order->getBaseCurrencyCode(), $order->getOrderCurrencyCode()));
                $vorder->setBaseCurrency($order->getBaseCurrencyCode());
                $vorder->setBaseOrderTotal($baseOrderTotal['order_total']);
                $vorder->setBaseToGlobalRate($baseToGlobalRate);
                $vorder->setProductQty($qty);
                $billingaddress = $order->getBillingAddress()->getData();
                if (isset ($billingaddress ['middlename'])) {
                    $billing_name = $billingaddress ['firstname'] . " " . $billingaddress ['middlename'] . " " . $billingaddress ['lastname'];
                } else {
                    $billing_name = $billingaddress ['firstname'] . " " . $billingaddress ['lastname'];
                }
                $vorder->setBillingName($billing_name);
                $vorder->setBillingCountryCode($order->getBillingAddress()->getData('country_id'));
                if ($order->getShippingAddress()) {
                    $vorder->setShippingCountryCode($order->getShippingAddress()->getData('country_id'));
                }
                $vorder->setItemCommission($baseOrderTotal['item_commission']);

                $vorder->collectCommission();
                $this->_eventManager->dispatch(
                    'ced_csmarketplace_vorder_shipping_save_before',
                    ['vorder' => $vorder]
                );

                /**
                 * AdvTransaction Code starts
                 */
                $totalFee = 0;
                $fees = $this->feeCollection->create()->addFieldToFilter('status', 1)->addFieldToFilter('order_state', 1);
                $orderTotal = $this->directoryHelper
                    ->currencyConvert($baseOrderTotal['order_total'], $order->getBaseCurrencyCode(), $order->getGlobalCurrencyCode());
                foreach ($fees as $fee) {
                    $orderFees = $this->orderfeeFactory->create();
                    if ($fee->getType() == "fixed") {
                        $totalFee = $totalFee + $fee->getValue();

                        $orderFees->setData('vendor_id', $vendorId);
                        $orderFees->setData('order_id', $vorder->getOrderId());
                        $orderFees->setData('fee_id', $fee->getFieldCode());
                        $orderFees->setData('order_id', $vorder->getOrderId());
                        $orderFees->setData('status', '0');
                        $orderFees->setData('amount', $fee->getValue());
                        $orderFees->setData('type', 'fixed');
                        $orderFees->save();
                    } else {

                        $totalFee = $totalFee + $orderTotal * ($fee->getValue() / 100);

                        $orderFees->setData('vendor_id', $vendorId);
                        $orderFees->setData('order_id', $vorder->getOrderId());
                        $orderFees->setData('fee_id', $fee->getFieldCode());
                        $orderFees->setData('order_id', $vorder->getOrderId());
                        $orderFees->setData('status', '0');
                        $orderFees->setData('amount', $orderTotal * ($fee->getValue() / 100));
                        $orderFees->setData('type', 'percentage');
                        $orderFees->save();
                    }

                }

                $totalDeduction = $totalFee + $vorder->getShopCommissionFee();

                $netVendorPay = $orderTotal - $totalDeduction;
                $vorder->setVendorEarn($netVendorPay);


                /**
                 * Advanced Transaction code ends
                 */
                $vorder->save();
                $helper = $this->csmarketplaceHelper;
                $notificationData = ['vendor_id' => $vendorId, 'reference_id' => $vorder->getId(), 'title' => 'New Order ' . $vorder->getOrderId(), 'action' => $helper->getUrl('csmarketplace/vorders/view', ['order_id' => $vorder->getId()])];
                $helper->setNotification($notificationData);
                $this->mailHelper
                    ->sendOrderEmail($order, \Ced\CsMarketplace\Model\Vorders::ORDER_NEW_STATUS, $vendorId, $vorder);

            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Error Occured While Placing The Order'));
            }
        }
        return $this;

    }

    /**
     * @param $order
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function creditMemoOrder($order)
    {
        try {
            if ($order->getState() == \Magento\Sales\Model\Order::STATE_CLOSED || ((float)$order->getBaseTotalRefunded() && (float)$order->getBaseTotalRefunded() >= (float)$order->getBaseTotalPaid())) {
                $vorders = $this->vordersFactory->create()->getCollection()
                    ->addFieldToFilter('order_id', ['eq' => $order->getIncrementId()]);
                if (count($vorders) > 0) {
                    foreach ($vorders as $vorder) {
                        if ($vorder->canCancel()) {
                            $vorder->setOrderPaymentState(\Magento\Sales\Model\Order\Invoice::STATE_CANCELED);
                            $vorder->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_CANCELED);
                            $vorder->save();
                        } elseif ($vorder->canMakeRefund()) {
                            $vorder->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUND);
                            $vorder->save();
                        }
                    }
                }
            }
            return $this;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Error Occured While Placing The Order'));
        }
    }

}

