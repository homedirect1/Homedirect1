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

namespace Ced\CsAdvTransaction\Block\Adminhtml\Vpayments\Edit;

/**
 * Class Form
 * @package Ced\CsAdvTransaction\Block\Adminhtml\Vpayments\Edit
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
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryHelper;

    /**
     * @var \Ced\CsAdvTransaction\Helper\Data
     */
    protected $advHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\Response\Http
     */
    protected $httpRequest;

    /**
     * Form constructor.
     * @param \Ced\CsMarketplace\Model\Vendor $vendor
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Ced\CsAdvTransaction\Helper\Data $advHelper
     * @param \Magento\Framework\App\Response\Http $httpRequest
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Ced\CsMarketplace\Model\Vendor $vendor,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Ced\CsAdvTransaction\Helper\Data $advHelper,
        \Magento\Framework\App\Response\Http $httpRequest,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    )
    {
        $this->_vendor = $vendor;
        $this->_directoryHelper = $directoryHelper;
        $this->advHelper = $advHelper;
        $this->scopeConfig = $context->getScopeConfig();
        $this->httpRequest = $httpRequest;
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
        $this->setId('paydata_form');
        $this->setTitle(__('Request Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $vendorId = $this->getRequest()->getParam('vendor_id', 0);

        $postData = $this->getRequest()->getPostValue();

        $eligibleOrders = unserialize($postData['eligible_orders']);

        $PaytoVendor = 0;
        try {
            foreach ($eligibleOrders as $orders) {
                $orIds = [];
                $orIds[] = $orders['order_id'];
                if ($orders['payment_state'] == 1) {
                    if ($this->advHelper->getOrderPaymentType($orders['order_id']) == "PostPaid") {
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
                    } else {
                        $PaidtoVendor = 0;

                        $vendorEarn = $orders['vendor_earn'];
                        $payShip = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_shipping');
                        $vendorPay = $vendorEarn - $PaidtoVendor;

                        if ($payShip) {
                            $vendorPay = $vendorPay + $orders['shipping_amount'];
                        }
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
                $PaytoVendor = $PaytoVendor - $serviceTax;
                $PaytoVendor = $PaytoVendor + $vendorPay;

            }
        } catch (\Exception $e) {
            die($e);
        }


        $value = (float)$postData['total'];
        $flag = true;
        if (abs(($value - $PaytoVendor) / $PaytoVendor) < 0.00001) {
            $flag = false;
        }

        if ($flag) {
            $this->httpRequest->setRedirect($this->getUrl('csadvtransaction/pay/order', ['vendor_id' => $vendorId]));
        }

        $type = 0;
        $vendor = $this->_vendor->getCollection()->toOptionArray($vendorId);
        $ascn = isset($vendor[$vendorId]) ? $vendor[$vendorId] : '';
        $base_amount = '';
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setHtmlIdPrefix('block_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Transaction Information'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField('currency', 'hidden', array(
            'name' => 'currency',
            'value' => $this->_directoryHelper->getBaseCurrencyCode(),
        ));
        $fieldset->addField('vendor_id', 'hidden', array(
            'name' => 'vendor_id',
            'value' => $vendorId,
        ));
        $fieldset->addField('vendor_name', 'label', array(
            'label' => __('Vendor'),
            'after_element_html' => '<a target="_blank" href="' . $this->getUrl('csmarketplace/adminhtml_vendor/edit/', array('vendor_id' => $vendorId, '_secure' => true)) . '" title="' . $ascn . '">' . $ascn . '</a>',
        ));

        $fieldset->addField('test', 'hidden', array(
            'name' => 'test',
            'label' => __('Test'),
            'after_element_html' => '<script type="text/javascript">
                                            require(["jquery"], function($){
                                                 $("#payment_code").change(function () {
                                                     var payment_code = $("#payment_code").val();
        
                                                   $("#test").val(payment_code)  ;
                                                     });
                                            });
                                      </script>',
        ));
        $fieldset->addField('base_amount', 'text', array(
            'label' => __('Amount'),
            'class' => 'required-entry validate-greater-than-zero',
            'required' => true,
            'name' => 'base_amount',
            'value' => $PaytoVendor,
            'readonly' => 'readonly',
            'after_element_html' => '<b>[' . $this->_directoryHelper->getBaseCurrencyCode() . ']</b><small><i>' . __('Readonly field') . '</i>.</small>',
        ));
        $fieldset->addField('base_amount_plus_adjustment', 'hidden', array(
            'class' => 'required-entry validate-greater-than-zero',
            'required' => true,
            'name' => 'base_amount_plus_adjustment',
            'value' => $PaytoVendor,
            'after_element_html' => '<script type="text/javascript">
                                            require(["jquery"], function($){
        		
        		                                 $("#block_base_amount_plus_adjustment").val($("#block_base_amount").val())  ;
                                                 $("#block_base_fee").change(function () {
        		            
        		                                 var base_amount = parseInt(document.getElementById("block_base_amount").value);
                                                 var base_fee = parseInt(document.getElementById("block_base_fee").value);
                                                 var amount = base_amount+base_fee;
        										 $("#block_base_amount_plus_adjustment").val(amount);
                                                     });
                                            });
                                      </script>',
        ));

        $fieldset->addField(
            'payment_method',
            'select',
            [
                'label' => __('Payment Method'),
                'title' => __('Payment Method'),
                'name' => 'payment_method',
                'required' => true,
                'options' => [
                    'none' => __(' '),
                    'other' => __('Other')

                ],
                'after_element_html' => '<script>require(["jquery"], function ($) {
									     $(document).ready(function ()  
                                      {
         		 						var val = ($("#block_payment_method option:selected").val()); 
          		                    	 if(val == "other")
         							 {
											  $("#block_payment_code_other").show();
         		                         
									 }
                                    
									 $("#block_payment_method").change(function () { 
										   var value = ($("#block_payment_method option:selected").val()); 
          	
										 if(value =="other")
         							 {
          	
          		                          $("#block_payment_code_other").show();
          	                          
									 }
				                   
					         	
								  }); 
          							
          		   });
									  });</script>'
            ]

        );


        $fieldset->addField('payment_code_other', 'text', array(
            'label' => '',
            'style' => 'display: none;',
            'disbaled' => 'true',
            //'required'  => true,
            'name' => 'payment_code',
        ));

        $fieldset->addField('base_fee', 'text', array(
            'label' => __('Adjustment Amount'),
            'class' => 'validate-number',
            'required' => false,
            'name' => 'base_fee',
            'after_element_html' => '<b>[' . $this->_directoryHelper->getBaseCurrencyCode() . ']</b><small>' . __('Enter adjustment amount in +/- (if any)') . '</small>',
        ));


        $fieldset->addField('textarea', 'textarea', array(
            'label' => __('Notes'),
            'required' => false,
            'name' => 'notes',
        ));

        $fieldset->addField('eligible_orders', 'hidden', array(
            'label' => __(''),
            //	'style'   => 'display: none;',
            'required' => false,
            'name' => 'eligible_orders',
        ));


        $val = [];
        $val['base_amount'] = $PaytoVendor;
        $val['vendor_id'] = $vendorId;
        $val['eligible_orders'] = $postData['eligible_orders'];
        $val['currency'] = $this->_directoryHelper->getBaseCurrencyCode();
        $form->setValues($val);
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }


}
