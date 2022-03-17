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

namespace Ced\CsAdvTransaction\Block\Payments;

use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class View
 * @package Ced\CsAdvTransaction\Block\Payments
 */
class View extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{

    /**
     * @var \Ced\CsMarketplace\Helper\Acl
     */
    public $_acl;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Data\Form
     */
    protected $form;

    /**
     * @var \Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid\Renderer\Vendorname
     */
    protected $vendorname;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    public $priceCurrency;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $timezone;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \Ced\CsMarketplace\Model\VpaymentFactory
     */
    public $vpaymentFactory;

    /**
     * @var \Ced\CsAdvTransaction\Model\FeeFactory
     */
    public $feeFactory;

    /**
     * View constructor.
     * @param \Ced\CsMarketplace\Helper\Acl $acl
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\Form $form
     * @param \Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid\Renderer\Vendorname $vendorname
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Stdlib\DateTime\Timezone $timezone
     * @param \Ced\CsMarketplace\Model\VpaymentFactory $vpaymentFactory
     * @param \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param Context $context
     * @param \Ced\CsMarketplace\Model\Session $customerSession
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        \Ced\CsMarketplace\Helper\Acl $acl,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\Form $form,
        \Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid\Renderer\Vendorname $vendorname,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Stdlib\DateTime\Timezone $timezone,
        \Ced\CsMarketplace\Model\VpaymentFactory $vpaymentFactory,
        \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        Context $context,
        \Ced\CsMarketplace\Model\Session $customerSession,
        UrlFactory $urlFactory
    )
    {
        try {
            $this->registry = $registry;
            $this->_acl = $acl;
            $this->form = $form;
            $this->vendorname = $vendorname;
            $this->priceCurrency = $priceCurrency;
            $this->timezone = $timezone;
            $this->storeManager = $context->getStoreManager();
            $this->vpaymentFactory = $vpaymentFactory;
            $this->feeFactory = $feeFactory;

            parent::__construct($vendorFactory, $customerFactory, $context, $customerSession, $urlFactory);

        } catch (\Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    /**
     * Get Details of the payment
     *
     */
    public function getVpayment()
    {
        $payment = $this->registry->registry('current_vpayment');
        return $payment;
    }

    /**
     * @return Ced_CsMarketplace_Block_Adminhtml_Vpayments_Details_Form
     */
    protected function _prepareForm()
    {
        list($model, $fieldsets) = $this->loadFields();
        $form = $this->form;

        foreach ($fieldsets as $key => $data) {
            $fieldset = $form->addFieldset($key, array('legend' => $data['legend']));
            foreach ($data['fields'] as $id => $info) {
                if ($info['type'] == 'link') {
                    $fieldset->addField($id, $info['type'], array(
                        'name' => $id,
                        'label' => $info['label'],
                        'title' => $info['label'],
                        'href' => $info['href'],
                        'value' => isset($info['value']) ? $info['value'] : $model->getData($id),
                        'text' => isset($info['text']) ? $info['text'] : $model->getData($id),
                        'after_element_html' => isset($info['after_element_html']) ? $info['after_element_html'] : '',
                    ));
                } else {
                    $fieldset->addField($id, $info['type'], array(
                        'name' => $id,
                        'label' => $info['label'],
                        'title' => $info['label'],
                        'value' => isset($info['value']) ? $info['value'] : $model->getData($id),
                        'text' => isset($info['text']) ? $info['text'] : $model->getData($id),
                        'after_element_html' => isset($info['after_element_html']) ? $info['after_element_html'] : '',

                    ));
                }
            }
        }
        $this->setForm($form);
        return $this;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function loadFields()
    {
        $model = $this->getVpayment();
        $renderOrderDesc = $this->getLayout()->createBlock('Ced\CsMarketplace\Block\Adminhtml\Vpayments\Grid\Renderer\Orderdesc');

        $renderName = $this->vendorname;
        if ($model->getBaseCurrency() != $model->getCurrency()) {
            $fieldsets = array(
                'beneficiary_details' => array(
                    'fields' => array(
                        'vendor_id' => array('label' => __('Vendor Name'), 'text' => $renderName->render($model), 'type' => 'note'),
                        'payment_code' => array('label' => __('Payment Method'), 'type' => 'label', 'value' => $model->getData('payment_code')),
                        'payment_detail' => array('label' => __('Beneficiary Details'), 'type' => 'note', 'text' => $model->getData('payment_detail')),
                    ),
                    'legend' => __('Beneficiary Details')
                ),


                'payment_details' => array(
                    'fields' => array(
                        'transaction_id' => array('label' => __('Transaction ID#'), 'type' => 'label', 'value' => $model->getData('transaction_id')),
                        'created_at' => array(
                            'label' => __('Transaction Date'),
                            'value' => $model->getData('created_at'),
                            'type' => 'label',
                        ),
                        'payment_method' => array(
                            'label' => __('Transaction Mode'),
                            'value' => $this->_acl->getDefaultPaymentTypeLabel($model->getData('payment_method')),
                            'type' => 'label',
                        ),
                        'transaction_type' => array(
                            'label' => __('Transaction Type'),
                            'value' => ($model->getData('transaction_type') == 0) ? __('Credit Type') : __('Debit Type'),
                            'type' => 'label',
                        ),
                        'total_shipping_amount' => array(
                            'label' => __('Total Shipping Amount'),
                            'value' => $this->priceCurrency->format($model->getData('total_shipping_amount'), false, 2, null, $model->getCurrency()),
                            'type' => 'label',
                        ),
                        'amount' => array(
                            'label' => __('Amount'),
                            'value' => $this->priceCurrency->format($model->getData('amount'), false, 2, null, $model->getCurrency()),
                            'type' => 'label',
                        ),
                        'base_amount' => array(
                            'label' => '&nbsp;',
                            'value' => '[' . $this->priceCurrency->format($model->getData('base_amount'), false, 2, null, $model->getCurrency()) . ']',
                            'type' => 'label',
                        ),
                        'fee' => array(
                            'label' => __('Adjustment Amount'),
                            'value' => $this->priceCurrency->format($model->getData('fee'), false, 2, null, $model->getCurrency()),
                            'type' => 'label',
                        ),
                        'base_fee' => array(
                            'label' => '&nbsp;',
                            'value' => '[' . $this->priceCurrency->format($model->getData('base_fee'), false, 2, null, $model->getCurrency()) . ']',
                            'type' => 'label',
                        ),
                        'net_amount' => array(
                            'label' => __('Net Amount'),
                            'value' => $this->priceCurrency->format($model->getData('net_amount'), false, 2, null, $model->getCurrency()),
                            'type' => 'label',
                        ),
                        'base_net_amount' => array(
                            'label' => '&nbsp;',
                            'value' => '[' . $this->priceCurrency->format($model->getData('base_net_amount'), false, 2, null, $model->getCurrency()) . ']',
                            'type' => 'label',
                        ),
                        'notes' => array(
                            'label' => __('Notes'),
                            'value' => $model->getData('notes'),
                            'type' => 'label',
                        ),
                    ),
                    'legend' => __('Transaction Details')
                ),
            );
        } else {
            $fieldsets = array(
                'beneficiary_details' => array(
                    'fields' => array(
                        'vendor_id' => array('label' => __('Vendor Name'), 'text' => $renderName->render($model), 'type' => 'note'),
                        'payment_code' => array('label' => __('Payment Method'), 'type' => 'label', 'value' => $model->getData('payment_code')),
                        'payment_detail' => array('label' => __('Beneficiary Details'), 'type' => 'note', 'text' => $model->getData('payment_detail')),
                    ),
                    'legend' => __('Beneficiary Details')
                ),


                'payment_details' => array(
                    'fields' => array(
                        'transaction_id' => array('label' => __('Transaction ID#'), 'type' => 'label', 'value' => $model->getData('transaction_id')),
                        'created_at' => array(
                            'label' => __('Transaction Date'),
                            'value' => $model->getData('created_at'),
                            'type' => 'label',
                        ),
                        'payment_method' => array(
                            'label' => __('Transaction Mode'),
                            'value' => $this->_acl->getDefaultPaymentTypeLabel($model->getData('payment_method')),
                            'type' => 'label',
                        ),
                        'transaction_type' => array(
                            'label' => __('Transaction Type'),
                            'value' => ($model->getData('transaction_type') == 0) ? __('Credit Type') : __('Debit Type'),
                            'type' => 'label',
                        ),
                        'total_shipping_amount' => array(
                            'label' => __('Total Shipping Amount'),
                            'value' => $this->priceCurrency->format($model->getData('total_shipping_amount'), false, 2, null, $model->getCurrency()),
                            'type' => 'label',
                        ),
                        'amount' => array(
                            'label' => __('Amount'),
                            'value' => $this->priceCurrency->format($model->getData('amount'), false, 2, null, $model->getCurrency()),
                            'type' => 'label',
                        ),
                        'fee' => array(
                            'label' => __('Adjustment Amount'),
                            'value' => $this->priceCurrency->format($model->getData('fee'), false, 2, null, $model->getCurrency()),
                            'type' => 'label',
                        ),
                        'net_amount' => array(
                            'label' => __('Net Amount'),
                            'value' => $this->priceCurrency->format($model->getData('net_amount'), false, 2, null, $model->getCurrency()),
                            'type' => 'label',
                        ),
                        'notes' => array(
                            'label' => __('Notes'),
                            'value' => $model->getData('notes'),
                            'type' => 'label',
                        ),
                    ),
                    'legend' => __('Transaction Details')
                ),
            );
        }

        return array($model, $fieldsets);
    }

    /**
     * Preparing global layout
     * You can redefine this method in child classes for changin layout
     * @return Ced_CsMarketplace_Block_Vendor_Abstract
     */
    protected function _prepareLayout()
    {
        \Magento\Framework\Data\Form::setElementRenderer(
            $this->getLayout()->createBlock('Ced\CsMarketplace\Block\Widget\Form\Renderer\Element')
        );
        \Magento\Framework\Data\Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('Ced\CsMarketplace\Block\Widget\Form\Renderer\Fieldset')
        );
        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('Ced\CsMarketplace\Block\Vpayments\View\Element')
        );

        return parent::_prepareLayout();
    }

    /**
     * Get form object
     *
     * @return Varien_Data_Form
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Get form object
     *
     * @return Varien_Data_Form
     * @see getForm()
     * @deprecated deprecated since version 1.2
     */
    public function getFormObject()
    {
        return $this->getForm();
    }

    /**
     * Get form HTML
     *
     * @return string
     */
    public function getFormHtml()
    {
        if (is_object($this->getForm())) {
            return $this->getForm()->getHtml();
        }
        return '';
    }

    /**
     * Set form object
     *
     * @param Varien_Data_Form $form
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    public function setForm(\Magento\Framework\Data\Form $form)
    {
        $this->_form = $form;
        $this->_form->setParent($this);
        $this->_form->setBaseUrl($this->getUrl());
        return $this;
    }

    /**
     * This method is called before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _beforeToHtml()
    {
        $this->_prepareForm();
        $this->_initFormValues();
        return parent::_beforeToHtml();
    }

    /**
     * Initialize form fields values
     * Method will be called after prepareForm and can be used for field values initialization
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _initFormValues()
    {
        return $this;
    }

    /**
     * Set Fieldset to Form
     *
     * @param array $attributes attributes that are to be added
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array $exclude attributes that should be skipped
     */
    protected function _setFieldset($attributes, $fieldset, $exclude = array())
    {
        $this->_addElementTypes($fieldset);
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if (!$attribute || ($attribute->hasIsVisible() && !$attribute->getIsVisible())) {
                continue;
            }
            if (($inputType = $attribute->getFrontend()->getInputType())
                && !in_array($attribute->getAttributeCode(), $exclude)
                && ('media_image' != $inputType)
            ) {

                $fieldType = $inputType;
                $rendererClass = $attribute->getFrontend()->getInputRendererClass();
                if (!empty($rendererClass)) {
                    $fieldType = $inputType . '_' . $attribute->getAttributeCode();
                    $fieldset->addType($fieldType, $rendererClass);
                }

                $element = $fieldset->addField($attribute->getAttributeCode(), $fieldType,
                    array(
                        'name' => $attribute->getAttributeCode(),
                        'label' => $attribute->getFrontend()->getLabel(),
                        'class' => $attribute->getFrontend()->getClass(),
                        'required' => $attribute->getIsRequired(),
                        'note' => $attribute->getNote(),
                    )
                )
                    ->setEntityAttribute($attribute);

                $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));

                if ($inputType == 'select') {
                    $element->setValues($attribute->getSource()->getAllOptions(true, true));
                } else if ($inputType == 'multiselect') {
                    $element->setValues($attribute->getSource()->getAllOptions(false, true));
                    $element->setCanBeEmpty(true);
                } else if ($inputType == 'date') {
                    $element->setImage($this->getSkinUrl('images/calendar.gif'));
                    $element->setFormat($this->timezone->getDateFormatWithLongYear());
                } else if ($inputType == 'datetime') {
                    $element->setImage($this->getSkinUrl('images/calendar.gif'));
                    $element->setTime(true);
                    $element->setStyle('width:50%;');
                    $element->setFormat(
                        $this->timezone->getDateTimeFormat(\Magento\Framework\Stdlib\DateTime\Timezone::FORMAT_TYPE_SHORT)
                    );
                } else if ($inputType == 'multiline') {
                    $element->setLineCount($attribute->getMultilineCount());
                }
            }
        }
    }

    /**
     * Add new element type
     *
     * @param Varien_Data_Form_Abstract $baseElement
     */
    protected function _addElementTypes(Varien_Data_Form_Abstract $baseElement)
    {
        $types = $this->_getAdditionalElementTypes();
        foreach ($types as $code => $className) {
            $baseElement->addType($code, $className);
        }
    }

    /**
     * Retrieve predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array();
    }

    /**
     * Enter description here...
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getAdditionalElementHtml($element)
    {
        return '';
    }

    /**
     * back Link url
     *
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index', array('_secure' => true, '_nosid' => true));
    }

}
