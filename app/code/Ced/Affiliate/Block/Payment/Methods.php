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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Block\Payment;

use Magento\Framework\View\Element\Template\Context;

/**
 * Class Methods
 * @package Ced\Affiliate\Block\Payment
 */
class Methods extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Data\Form
     */
    protected $form;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * @var \Ced\Affiliate\Model\PaymentsettingsFactory
     */
    protected $paymentsettingsFactory;

    /**
     * @var \Ced\Affiliate\Model\Source\Config\Paymentmethods
     */
    protected $paymentmethods;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * Methods constructor.
     * @param Context $context
     * @param \Magento\Framework\Data\Form $form
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Ced\Affiliate\Model\PaymentsettingsFactory $paymentsettingsFactory
     * @param \Ced\Affiliate\Model\Source\Config\Paymentmethods $paymentmethods
     * @param \Magento\Customer\Model\Session $session
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Data\Form $form,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Ced\Affiliate\Model\PaymentsettingsFactory $paymentsettingsFactory,
        \Ced\Affiliate\Model\Source\Config\Paymentmethods $paymentmethods,
        \Magento\Customer\Model\Session $session
    )
    {
        $this->form = $form;
        $this->affiliateHelper = $affiliateHelper;
        $this->paymentsettingsFactory = $paymentsettingsFactory;
        $this->paymentmethods = $paymentmethods;
        $this->session = $session;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Element\Template
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {

        \Magento\Framework\Data\Form::setElementRenderer(
            $this->getLayout()->createBlock('Ced\Affiliate\Block\Widget\Form\Renderer\Element')
        );
        \Magento\Framework\Data\Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('Ced\Affiliate\Block\Widget\Form\Renderer\Fieldset')
        );
        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('Ced\Affiliate\Block\Widget\Form\Renderer\Fieldset\Element')
        );

        return parent::_prepareLayout();
    }

    /**
     * Get form object
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Get form object
     *
     * @deprecated deprecated since version 1.2
     * @see getForm()
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
     */
    public function setForm(\Magento\Framework\Data\Form $form)
    {
        $this->_form = $form;
        $this->_form->setParent($this);
        $this->_form->setBaseUrl($this->getBaseUrl());
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $methods = $this->getPaymentMethods();
        $form = $this->form;
        $form->setAction($this->getUrl('*/*/save', array('section' => \Ced\Affiliate\Model\Paymentsettings::PAYMENT_SECTION)))
            ->setId('form-validate')
            ->setMethod('POST')
            ->setEnctype('multipart/form-data')
            ->setUseContainer(true);
        if (count($methods) > 0) {
            $cnt = 1;
            foreach ($methods as $code => $method) {

                $fields = $method->getFields();
                if (count($fields) > 0) {
                    $fieldset = $form->addFieldset('affiliate_' . $code, array('legend' => $method->getLabel('label')));
                    foreach ($fields as $id => $field) {
                        $key = strtolower(\Ced\Affiliate\Model\Paymentsettings::PAYMENT_SECTION . '/' . $method->getCode() . '/' . $id);
                        $value = '';
                        $customer_id = $this->affiliateHelper->getTableKey('customer_id');
                        $key_tmp = $this->affiliateHelper->getTableKey('key');
                        $setting = $this->paymentsettingsFactory->create()
                            ->loadByField(array($key_tmp, $customer_id), array($key, (int)$this->getCustomerId()));
                        if ($setting) $value = $setting->getValue();
                        $fieldset->addField($method->getCode() . $method->getCodeSeparator() . $id, isset($field['type']) ? $field['type'] : 'text', array(
                            'label' => $method->getLabel($id),
                            'value' => $value,
                            'name' => 'groups[' . $method->getCode() . '][' . $id . ']',
                            isset($field['class']) ? 'class' : '' => isset($field['class']) ? $field['class'] : '',
                            isset($field['required']) ? 'required' : '' => isset($field['required']) ? $field['required'] : '',
                            isset($field['onchange']) ? 'onchange' : '' => isset($field['onchange']) ? $field['onchange'] : '',
                            isset($field['onclick']) ? 'onclick' : '' => isset($field['onclick']) ? $field['onclick'] : '',
                            isset($field['href']) ? 'href' : '' => isset($field['href']) ? $field['href'] : '',
                            isset($field['target']) ? 'target' : '' => isset($field['target']) ? $field['target'] : '',
                            isset($field['values']) ? 'values' : '' => isset($field['values']) ? $field['values'] : '',
                            isset($field['after_element_html']) ? 'after_element_html' : '' => isset($field['after_element_html']) ? '<div><small>' . $field['after_element_html'] . '</small></div>' : '',
                        ));
                    }
                    $cnt++;
                }
            }
        }

        $this->setForm($form);

        return $this;
    }

    /**
     * @return \Magento\Framework\View\Element\Template
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
     */
    protected function _initFormValues()
    {
        return $this;
    }


    /**
     * @return array
     */
    public function getPaymentMethods()
    {
        $availableMethods = $this->paymentmethods->toOptionArray();
        $methods = array();
        if (count($availableMethods) > 0) {
            foreach ($availableMethods as $method) {
                if (isset($method['value'])) {
                    $object = \Magento\Framework\App\ObjectManager::getInstance()
                        ->get('Ced\Affiliate\Model\Customer\Payment\Methods\\' . ucfirst($method['value']));
                    if (is_object($object)) {
                        $methods[$method['value']] = $object;
                    }
                }
            }
        }
        return $methods;
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->session->getCustomer()->getId();
    }
}
