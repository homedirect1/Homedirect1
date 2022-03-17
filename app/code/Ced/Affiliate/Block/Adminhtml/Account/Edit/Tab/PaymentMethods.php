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

namespace Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab;

/**
 * Class PaymentMethods
 * @package Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab
 */
class PaymentMethods extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Ced\Affiliate\Model\PaymentMethodsFactory
     */
    protected $paymentMethodsFactory;

    /**
     * @var \Ced\Affiliate\Model\PaymentsettingsFactory
     */
    protected $paymentsettingsFactory;

    /**
     * PaymentMethods constructor.
     * @param \Ced\Affiliate\Model\PaymentMethodsFactory $paymentMethodsFactory
     * @param \Ced\Affiliate\Model\PaymentsettingsFactory $paymentsettingsFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\PaymentMethodsFactory $paymentMethodsFactory,
        \Ced\Affiliate\Model\PaymentsettingsFactory $paymentsettingsFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    )
    {
        $this->paymentMethodsFactory = $paymentMethodsFactory;
        $this->paymentsettingsFactory = $paymentsettingsFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this|\Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $affiliateAccount = $this->_coreRegistry->registry('current_account');
        if ($affiliateAccount) {
            $methods = $this->paymentMethodsFactory->create()->getPaymentMethods();
            $form = $this->_formFactory->create();
            if (count($methods) > 0) {
                $cnt = 1;
                foreach ($methods as $code => $method) {
                    $fields = $method->getFields();
                    if (count($fields) > 0) {
                        $fieldset = $form->addFieldset('affiliate_' . $code,
                            array('legend' => $method->getLabel('label')));
                        foreach ($fields as $id => $field) {
                            $key = strtolower(\Ced\Affiliate\Model\Paymentsettings::PAYMENT_SECTION . '/' . $method->getCode() . '/' . $id);
                            $value = '';
                            if ((int)$affiliateAccount->getCustomerId()) {
                                $setting = $this->paymentsettingsFactory->create()
                                    ->loadByField(array('key', 'customer_id'), array($key, (int)$affiliateAccount
                                        ->getCustomerId()));
                                if ($setting) $value = $setting->getValue();
                            }
                            $fieldset
                                ->addField($method->getCode() . $method->getCodeSeparator() . $id, 'label',
                                    array(
                                'label' => $method->getLabel($id),
                                'value' => isset($field['values']) ? $this->getLabelByValue($value, $field['values']) : $value,
                                'name' => 'groups[' . $method->getCode() . '][' . $id . ']',
                                isset($field['class']) ? 'class' : '' => isset($field['class']) ? $field['class'] : '',
                                isset($field['required']) ? 'required' : '' => isset($field['required']) ? $field['required'] : '',
                                isset($field['onchange']) ? 'onchange' : '' => isset($field['onchange']) ? $field['onchange'] : '',
                                isset($field['onclick']) ? 'onclick' : '' => isset($field['onclick']) ? $field['onclick'] : '',
                                isset($field['href']) ? 'href' : '' => isset($field['href']) ? $field['href'] : '',
                                isset($field['target']) ? 'target' : '' => isset($field['target']) ? $field['target'] : '',
                                isset($field['values']) ? 'values' : '' => isset($field['values']) ? $field['values'] : '',
                            ));
                        }
                        $cnt++;
                    }
                }
            }
            $this->setForm($form);
        }
        return $this;
    }

    /**
     * retrieve label from value
     * @param array
     * @return string
     */
    protected function getLabelByValue($value = '', $values = array())
    {
        foreach ($values as $key => $option) {


            if (is_array($option)) {
                if (isset($option['value']) && $option['value'] == $value && $option['label']) {
                    return $option['label'];
                    break;
                }
            } else {
                if ($key == $value && $option->getText()) {
                    return $option->getText();
                    break;
                }
            }
        }
        return $value;
    }
}