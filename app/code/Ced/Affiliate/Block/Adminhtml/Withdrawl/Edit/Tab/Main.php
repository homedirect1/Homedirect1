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

namespace Ced\Affiliate\Block\Adminhtml\Withdrawl\Edit\Tab;

/**
 * Class Main
 * @package Ced\Affiliate\Block\Adminhtml\Withdrawl\Edit\Tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Ced\Affiliate\Model\PaymentMethodsFactory
     */
    protected $paymentMethodsFactory;

    /**
     * Main constructor.
     * @param \Ced\Affiliate\Model\PaymentMethodsFactory $paymentMethodsFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\PaymentMethodsFactory $paymentMethodsFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    )
    {
        $this->paymentMethodsFactory = $paymentMethodsFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this|\Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {

        parent::_prepareForm();
        $podata = $this->_coreRegistry->registry('withdrawl_request');
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Withdrawal Request Information')]);

        $fieldset->addField(
            'customer_email',
            'text',
            [
                'name' => 'customer_email',
                'label' => __('Customer Email'),
                'title' => __('Customer Email'),
                'required' => true,
                'class' => '',
                'readonly' => true,
            ]
        );

        $fieldset->addField(
            'request_amount',
            'text',
            [
                'name' => 'request_amount',
                'label' => __('Amount Requested'),
                'title' => __('Amount Requested'),
                'class' => '',
                'readonly' => true,
            ]
        );

        $fieldset->addField(
            'payment_mode',
            'select',
            [
                'name' => 'payment_mode',
                'label' => __('Payment Method'),
                'title' => __('Payment Method'),
                'values' => $this->paymentMethodsFactory->create()->getPaymentMethodsArray($podata->getCustomerId()),
                'class' => '',
                'required' => true
            ]
        );

        $fieldset->addField(
            'service_tax',
            'text',
            [
                'name' => 'service_tax',
                'label' => __('Service Tax'),
                'title' => __('Service Tax'),
                'class' => '',
                'required' => true
            ]
        );


        $fieldset->addField(
            'payable_amount',
            'text',
            [
                'name' => 'amount_paid',
                'label' => __('Payable Amount'),
                'title' => __('Payable Amount'),
                'class' => '',
                'required' => true

            ]
        );

        $fieldset->addField(
            'transaction_id',
            'text',
            [
                'name' => 'transaction_id',
                'label' => __('Enter Transaction Id'),
                'title' => __('Enter Transaction Id'),
                'class' => '',
                'required' => true
            ]
        );


        $fieldset->addField(
            'note',
            'textarea',
            [
                'name' => 'note',
                'label' => __('Note'),
                'title' => __('Note'),
                'class' => '',
                'required' => true

            ]
        );

        $form->setValues($podata->getData());
        $this->setForm($form);
        return $this;
    }

}
