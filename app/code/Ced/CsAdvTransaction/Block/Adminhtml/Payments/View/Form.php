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

namespace Ced\CsAdvTransaction\Block\Adminhtml\Payments\View;

/**
 * Class Form
 * @package Ced\CsAdvTransaction\Block\Adminhtml\Payments\View
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var null
     */
    protected $_availableMethods = null;

    /**
     * @var \Ced\CsMarketplace\Model\Vendor
     */
    protected $_vendor;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vorders\CollectionFactory
     */
    protected $vordersCollection;

    /**
     * @var \Ced\CsAdvTransaction\Helper\Data
     */
    protected $advHelper;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Ced\CsAdvTransaction\Model\ResourceModel\Orderfee\CollectionFactory
     */
    protected $orderfeeCollection;

    /**
     * @var \Ced\CsAdvTransaction\Model\ResourceModel\Fee\CollectionFactory
     */
    protected $feeCollection;

    /**
     * @var \Ced\CsMarketplace\Model\VpaymentFactory
     */
    protected $vpaymentFactory;

    /**
     * Form constructor.
     * @param \Ced\CsMarketplace\Model\Vendor $vendor
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vorders\CollectionFactory $vordersCollection
     * @param \Ced\CsAdvTransaction\Helper\Data $advHelper
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Ced\CsAdvTransaction\Model\ResourceModel\Orderfee\CollectionFactory $orderfeeCollection
     * @param \Ced\CsAdvTransaction\Model\ResourceModel\Fee\CollectionFactory $feeCollection
     * @param \Ced\CsMarketplace\Model\VpaymentFactory $vpaymentFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Ced\CsMarketplace\Model\Vendor $vendor,
        \Ced\CsMarketplace\Model\ResourceModel\Vorders\CollectionFactory $vordersCollection,
        \Ced\CsAdvTransaction\Helper\Data $advHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Ced\CsAdvTransaction\Model\ResourceModel\Orderfee\CollectionFactory $orderfeeCollection,
        \Ced\CsAdvTransaction\Model\ResourceModel\Fee\CollectionFactory $feeCollection,
        \Ced\CsMarketplace\Model\VpaymentFactory $vpaymentFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    )
    {
        $this->_vendor = $vendor;
        $this->storeManager = $context->getStoreManager();
        $this->scopeConfig = $context->getScopeConfig();
        $this->vordersCollection = $vordersCollection;
        $this->advHelper = $advHelper;
        $this->priceCurrency = $priceCurrency;
        $this->orderfeeCollection = $orderfeeCollection;
        $this->feeCollection = $feeCollection;
        $this->vpaymentFactory = $vpaymentFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('payview_form');
        $this->setTitle(__('Request Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $val = [];
        $currencyCode = $this->storeManager->getStore(null)->getBaseCurrencyCode();
        $params = $this->getRequest()->getParams();
        $orderId = $params['order_id'];
        $vendorId = $params['vendor_id'];
        If (isset($params['vpayment_id'])) {
            $vpaymentId = $params['vpayment_id'];

        }

        $orders = $this->vordersCollection->create()
            ->addFieldToFilter('vendor_id', $vendorId)
            ->addFieldToFilter('order_id', $orderId)->getFirstItem()->getData();

        $postPaiddetails = $this->advHelper->getPostPaidAmount($orders);
        $orIds = [];
        $payShip = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_shipping');
        if ($payShip) {
            $postPaiddetails = $this->advHelper->getPostPaidAmount($orders);
            $ship = $postPaiddetails['ship_amount'];
            $val['shipping_amount'] = $this->priceCurrency->format($ship, false, 2, null, $currencyCode);

        }

        $payMode = $this->advHelper->getOrderPaymentType($orders['order_id']);
        $val['pay_mode'] = $payMode;
        $PaytoVendor = 0;
        $i = 0;
        $PaytoVendor = 0;
        $shippingAmount = 0;
        $type = 0;
        $description = [];
        $orIds[] = $params['order_id'];
        $val['commission_fee'] = $this->priceCurrency->format($orders['shop_commission_fee'], false, 2, null, $currencyCode);
        if (!isset($vpaymentId)) {
            $orderFees = $this->orderfeeCollection->create()
                ->addFieldToFilter('order_id', $orderId)
                ->addFieldToFilter('status', 0)
                ->addFieldToFilter('vendor_id', $vendorId);

            foreach ($orderFees as $Ofees) {
                $val[$Ofees['fee_id']] = $this->priceCurrency->format($Ofees['amount'], false, 2, null, $currencyCode);
                $description[$i][$Ofees['fee_id']] = $Ofees['amount'];
                $description[$i]['order_id'] = $Ofees['order_id'];
            }

            if ($orders['payment_state'] == 1) {
                if ($payMode == "PostPaid") {

                    $postPaiddetails = $this->advHelper->getPostPaidAmount($orders);
                    $postPaidtoVendor = $postPaiddetails['post_paid'];
                    $vendorEarn = $orders['vendor_earn'];
                    $payShip = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_shipping');
                    $shipamount = 0;
                    if ($payShip) {

                        $shipamount = $postPaiddetails['ship_amount'];
                        if ($postPaiddetails['ship_type'] == 'admin') {
                            $shipamount = 0;
                        }
                        $vendorPay = $vendorEarn - $postPaidtoVendor + $shipamount;
                    } else {

                        $shipamount = $postPaiddetails['ship_amount'];
                        $vendorPay = $vendorEarn - $postPaidtoVendor;
                    }
                    $shippingAmount = $postPaiddetails['ship_amount'];
                } else {

                    $PaidtoVendor = 0;

                    $vendorEarn = $orders['vendor_earn'];
                    $payShip = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_shipping');
                    $vendorPay = $vendorEarn - $PaidtoVendor;
                    $shipping = $this->advHelper->getPostPaidAmount($orders);
                    if ($payShip) {
                        $ship = $shipping['ship_amount'];
                        if ($shipping['ship_type'] == 'admin') {
                            $ship = 0;
                        }
                        $vendorPay = $vendorPay + $ship;

                    }
                    $shippingAmount = $shipping['ship_amount'];
                }


            } elseif ($orders['payment_state'] == 3) {
                $postPaidtoVendor = 0;
                $vendorEarn = $orders['vendor_earn'];
                $vendorPay = $vendorEarn - $postPaidtoVendor;

            }
            if ($orders['payment_state'] == 4) {
                $PaidtoVendor = 0;
                $vendorEarn = $orders['vendor_earn'];
                $vendorPay = $vendorEarn - $PaidtoVendor;

            }
            $serviceTax = $this->advHelper->getServiceTax($orIds, $vendorId);
            $vendorPay = $vendorPay - $serviceTax;
            $PaytoVendor = $PaytoVendor + $vendorPay;
            $val['service_tax'] = $this->priceCurrency->format($serviceTax, false, 2, null, $currencyCode);


        } else {
            $row = $this->vpaymentFactory->create()->load($vpaymentId)->getData();
            $description = json_decode($row['amount_desc'], true);
        }
        $grandTotal = $orders['order_total'] + $postPaiddetails['ship_amount'];
        $shipFee = $postPaiddetails['ship_amount'];
        $val['grand_total'] = $this->priceCurrency->format($grandTotal, false, 2, null, $currencyCode);
        $val['shipping_fee'] = $this->priceCurrency->format($shipFee, false, 2, null, $currencyCode);

        $keys = [];

        foreach ($description as $k => $v) {
            foreach ($v as $k1 => $v1) {
                if ($v['order_id'] == $orderId) {
                    if (isset($v['vendor_payment'])) {
                        $PaytoVendor = $v['vendor_payment'];
                    }
                    $keys[] = $k1;
                    $val[$k1] = $this->priceCurrency->format($v1, false, 2, null, $currencyCode);
                }
            }
        }

        $keys = array_unique($keys);

        $fees = $this->feeCollection->create()->addFieldToFilter('field_code', ['in' => $keys])->addFieldToFilter('status', 1);

        $val['total_amount'] = $this->priceCurrency->format($PaytoVendor, false, 2, null, $currencyCode);

        $vendor = $this->_vendor->getCollection()->toOptionArray($vendorId);
        $ascn = isset($vendor[$vendorId]) ? $vendor[$vendorId] : '';
        $base_amount = '';
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'view_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setHtmlIdPrefix('block_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('order Details'), 'class' => 'fieldset-wide']
        );


        $fieldset->addField('vendor_name', 'label', array(
            'label' => __('Vendor'),
            'after_element_html' => '<a target="_blank" href="' . $this->getUrl('csmarketplace/adminhtml_vendor/edit/', array('vendor_id' => $vendorId, '_secure' => true)) . '" title="' . $ascn . '">' . $ascn . '</a>',
        ));


        $fieldset->addField('order_id', 'label', array(
            'label' => __('Order Id'),

        ));

        $fieldset->addField('pay_mode', 'label', array(
            'label' => __('Payment Mode'),

        ));

        $fieldset->addField('grand_total', 'label', array(
            'label' => __('Grand total'),
            'after_element_html' => '<div>(Product Prices+Shipping Charges)</div>'

        ));

        $fieldset->addField('shipping_fee', 'label', array(
            'label' => __('Shipping Fee of order')


        ));

        $fieldset1 = $form->addFieldset(
            'base_fieldset1',
            ['legend' => __('fee Details'), 'class' => 'fieldset-wide']
        );
        if ($orders['payment_state'] != 4 && $orders['order_payment_state'] != 4) {
            $fieldset1->addField('commission_fee', 'label', array(
                'label' => __('Commission Fee'),

            ));
        }

        if ($postPaiddetails['ship_type'] == 'admin' || ($postPaiddetails['ship_type'] == 'vendor' && !$payShip)) {

            $val['shipping_amount'] = $this->priceCurrency->format($postPaiddetails['ship_amount'], false, 2, null, $currencyCode);
            If (!isset($params['vpayment_id'])) {
                if ($orders['payment_state'] != 4) {

                    if (($val['pay_mode'] == 'PostPaid' && $orders['payment_state'] != 3) || $val['pay_mode'] == 'PrePaid') {
                        $fieldset1->addField('shipping_amount', 'label', array(
                            'label' => __('Shipping Amount'),

                        ));
                    }

                }

            }
        }

        If (isset($params['vpayment_id']) && isset($val['ship_amount'])) {
            $fieldset1->addField('shipping_amount', 'label', array(
                'label' => __('Shipping Amount'),

            ));
        }


        foreach ($fees as $key => $value) {

            $fieldset1->addField($value['field_code'], 'label', array(
                'label' => $value['field_label'],

            ));
        }


        $fieldset1->addField('service_tax', 'label', array(
            'label' => "Total Taxes",

        ));

        $fieldset2 = $form->addFieldset(
            'base_fieldset2',
            ['legend' => __('Amount'), 'class' => 'fieldset-wide']
        );
        $valTotal = $val['total_amount'];
        $Totallabel = __('Vendor Pay Amount');
        if ($valTotal[0] == '-') {
            $Totallabel = __('Admin Payable Amount');
        }

        if (!isset($params['vpayment_id'])) {
            $fieldset2->addField('total_amount', 'label', array(
                'label' => $Totallabel,
                'width' => '200px',

            ));
        } else {
            $fieldset2->addField('total_amount', 'label', array(
                'label' => __('Paid Amount'),

            ));
        }

        $val['order_id'] = $orderId;

        $form->setValues($val);
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
