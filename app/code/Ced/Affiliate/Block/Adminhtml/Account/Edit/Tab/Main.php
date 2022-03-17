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
 * Class Main
 * @package Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Ced\Affiliate\Block\Widget\Dob
     */
    protected $dob;

    /**
     * @var \Ced\Affiliate\Block\Widget\Taxvat
     */
    protected $taxvat;

    /**
     * @var \Ced\Affiliate\Block\Widget\Gender
     */
    protected $gender;

    /**
     * @var \Ced\Affiliate\Model\WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * Main constructor.
     * @param \Ced\Affiliate\Block\Widget\Dob $dob
     * @param \Ced\Affiliate\Block\Widget\Taxvat $taxvat
     * @param \Ced\Affiliate\Block\Widget\Gender $gender
     * @param \Ced\Affiliate\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Block\Widget\Dob $dob,
        \Ced\Affiliate\Block\Widget\Taxvat $taxvat,
        \Ced\Affiliate\Block\Widget\Gender $gender,
        \Ced\Affiliate\Model\WebsiteFactory $websiteFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    )
    {
        $this->dob = $dob;
        $this->taxvat = $taxvat;
        $this->gender = $gender;
        $this->websiteFactory = $websiteFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this|\Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {

        parent::_prepareForm();
        $podata = $this->_coreRegistry->registry('current_account');
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Affiliate Account Information')]);

        if ($this->getRequest()->getParam('id')) {
            $fieldset->addField(
                'customer_name',
                'text',
                [
                    'name' => 'affiliate_name',
                    'label' => __('Affiliate Name'),
                    'title' => __('Affiliate Name'),
                    'required' => true,
                    'class' => '',
                ]
            );
            $fieldset->addField(
                'customer_email',
                'link',
                [
                    'name' => 'affiliate_email',
                    'href' => $this->getUrl('customer/index/edit', array('id' => $podata->getCustomerId())),
                    'target' => '_blank',
                    'label' => __('Affiliate Email'),
                    'title' => __('Affiliate Email'),
                    'class' => '',
                ]
            );
            $fieldset->addField(
                'identity',
                'select',
                [
                    'select' => 'identity',
                    'values' => ['driving' => 'Driving License', 'pan' => 'Pan Card', 'passport' => 'Passport', 'other' => 'Other'],
                    'label' => __('Identity Information Type'),
                    'title' => __('Identity Information Type'),
                    'class' => '',
                ]
            );

            $fieldset->addField(
                'identityfile',
                'link',
                [
                    'name' => 'identityfile',
                    'href' => $this->getIdentityHref(),
                    'target' => '_blank',
                    'label' => __('Identity Information Document'),
                    'title' => __('Identity Information Document'),
                    'class' => '',
                ]
            );


            $fieldset->addField(
                'addressfile',
                'link',
                [
                    'name' => 'addressfile',
                    'href' => $this->getAddressHref(),
                    'target' => '_blank',
                    'label' => __('Address Information Document'),
                    'title' => __('Address Information Document'),
                    'class' => '',
                ]
            );


            $fieldset->addField(
                'companyfile',
                'link',
                [
                    'name' => 'companyfile',
                    'href' => $this->getCompanyHref(),
                    'target' => '_blank',
                    'label' => __('Companyfile Information Document'),
                    'title' => __('Companyfile Information Document'),
                    'class' => '',
                ]
            );
        } else {


            $fieldset->addField(
                'firstname',
                'text',
                [
                    'name' => 'customer[firstname]',
                    'label' => __('First Name'),
                    'title' => __('First Name'),
                    'required' => true,
                    'class' => '',

                ]
            );

            $fieldset->addField(
                'lastname',
                'text',
                [
                    'name' => 'customer[lastname]',
                    'label' => __('Last Name'),
                    'title' => __('Last Name'),
                    'required' => true,
                    'class' => '',

                ]
            );


            $fieldset->addField(
                'email',
                'text',
                [
                    'name' => 'customer[email]',
                    'label' => __('Email'),
                    'title' => __('Email'),
                    'class' => '',
                    'required' => true,
                ]
            );


            $_dob = $this->dob;
            if ($_dob->isEnabled()):
                $fieldset->addField(
                    'dob',
                    'date',
                    [
                        'name' => 'customer[dob]',
                        'label' => __('Date Of Birth'),
                        'date_format' => 'MM/dd/yyyy',
                        'required' => $_dob->isRequired(),
                    ]
                );
            endif;

            $_taxvat = $this->taxvat;
            if ($_taxvat->isEnabled()):
                $fieldset->addField(
                    'taxvat',
                    'text',
                    [
                        'name' => 'customer[taxvat]',
                        'label' => __('Tax/Vat'),
                        'title' => __('Tax/Vat'),
                        'required' => $_taxvat->isRequired(),
                        'class' => '',

                    ]
                );
            endif;

            $_gender = $this->gender;
            if ($_gender->isEnabled()):
                $fieldset->addField(
                    'gender',
                    'select',
                    [
                        'name' => 'customer[gender]',
                        'label' => __('Gender'),
                        'title' => __('Gender'),
                        'required' => $_gender->isRequired(),
                        'values' => ['1' => "Male", '2' => 'Female', '3' => 'Not Specified'],
                        'class' => '',

                    ]
                );
            endif;

            $fieldset->addField(
                'password',
                'password',
                [
                    'name' => 'customer[password]',
                    'label' => __('Password'),
                    'title' => __('Password'),
                    'class' => '',
                    'required' => true,
                ]
            );

            $fieldset->addField(
                'password_confirmation',
                'password',
                [
                    'name' => 'customer[password_confirmation]',
                    'label' => __('Confirm Password'),
                    'title' => __('Confirm Password'),
                    'class' => '',
                    'required' => true,
                ]
            );

            $fieldset->addField(
                'website_id',
                'select',
                [
                    'name' => 'customer[website_id]',
                    'label' => __('Associate To Website'),
                    'title' => __('Associate To Website'),
                    'values' => $this->websiteFactory->create()->toOptionArray(),
                    'class' => '',
                    'required' => true,
                ]
            );

            $fieldset->addField(
                'identity',
                'select',
                [
                    'select' => 'identity',
                    'values' => ['driving' => 'Driving License', 'pan' => 'Pan Card', 'passport' => 'Passport', 'other' => 'Other'],
                    'label' => __('Identity Information Type'),
                    'title' => __('Identity Information Type'),
                    'class' => '',
                    'required' => true
                ]
            );


            $fieldset->addField(
                'identityfile',
                'file',
                [
                    'name' => 'identityfile',
                    'label' => __('Identity Information Document'),
                    'title' => __('Identity Information Document'),
                    'class' => '',
                    'required' => true
                ]
            );


            $fieldset->addField(
                'addressfile',
                'file',
                [
                    'name' => 'addressfile',
                    'label' => __('Address Information Document'),
                    'title' => __('Address Information Document'),
                    'class' => '',
                    'required' => true
                ]
            );


            $fieldset->addField(
                'companyfile',
                'file',
                [
                    'name' => 'companyfile',
                    'label' => __('Companyfile Information Document'),
                    'title' => __('Companyfile Information Document'),
                    'class' => '',
                ]
            );

        }


        $fieldset->addField(
            'referral_website',
            'text',
            [
                'name' => 'referral_website',
                'label' => __('Referral Website'),
                'title' => __('Referral Website'),
                'class' => '',
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'affiliate_status',
                'label' => __('Affiliate Status'),
                'title' => __('Affiliate Status'),
                'values' => ['1' => 'Approved', '2' => 'Disapproved', '0' => 'Pending'],
                'class' => '',
            ]
        );

        $form->setValues($podata->getData());
        $this->setForm($form);
        return $this;

    }

    /**
     * @return string
     */
    public function getIdentityHref()
    {
        return $this->getFileSrc() . $this->_coreRegistry->registry('current_account')->getIdentityfile();
    }

    /**
     * @return string
     */
    public function getAddressHref()
    {
        return $this->getFileSrc() . $this->_coreRegistry->registry('current_account')->getAddressfile();
    }

    /**
     * @return string
     */
    public function getCompanyHref()
    {
        return $this->getFileSrc() . $this->_coreRegistry->registry('current_account')->getCompanyfile();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public Function getFileSrc()
    {
        $url = $this->_storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'affiliate/document/' . $this->_coreRegistry->registry('current_account')->getCustomerId() . '/';
        return $url;
    }
}
