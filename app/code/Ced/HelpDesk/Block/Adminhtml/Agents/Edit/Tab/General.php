<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_HelpDesk
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\HelpDesk\Block\Adminhtml\Agents\Edit\Tab;

class General extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Backend\Block\Template\Context
     */
    public $context;
    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;
    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    public $formFactory;
    /*
     *@param  \Magento\Backend\Block\Template\Context $context
     *@param \Magento\Framework\Registry $registry
     *@param \Magento\Framework\Data\FormFactory $formFactory
     *array $data
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /*
     *prepare agent form
     * */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('ced_agent');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General Information')]);
        if ($model = $this->_coreRegistry->registry('ced_agent')) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
            $passwordLabel = __('Change Password') ;
            $isRequired = false;
            $name = "change_password";
        }else{
            $passwordLabel =  __('Password');
            $isRequired = true;
            $name = "password";
        }
        $fieldset->addField(
            'role_name',
            'text',
            [
                'name' => 'role_name',
                'label' => __('Role'),
                'title' => __('Role'),
                'value' => __('Agent'),
                'readonly' => true
            ]
        );
        $fieldset->addField(
            'firstname',
            'text',
            [
                'name' => 'firstname',
                'label' => __('First Name'),
                'title' => __('First Name'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'lastname',
            'text',
            [
                'name' => 'lastname',
                'label' => __('Last Name'),
                'title' => __('Last Name'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'username',
            'text',
            [
                'name' => 'username',
                'label' => __('User Name'),
                'title' => __('User Name'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email Id'),
                'title' => __('Email Id'),
                'required' => true,
                'class' => 'validate-email'
            ]
        );
        $fieldset->addField(
            $name,
            'password',
            [
                'name' =>  $name,
                'label' => $passwordLabel,
                'title' => $passwordLabel,
                'class' => 'input-text validate-admin-password',
                'required' => $isRequired
            ]
        );
        $fieldset->addField(
            'active',
            'select',
            [
                'name' => 'active',
                'label' => __('Active'),
                'title' => __('Active'),
                'values' => [['label'=>'No','value'=>'0'],['label'=>'Yes','value'=>'1']],
                'required' => true
            ]
        );
        if ($model = $this->_coreRegistry->registry('ced_agent')) {
            $form->setValues($model);
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }
 
    /**
     * Prepare label for tab
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('General');
    }
 
    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General');
    }
 
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
 
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}