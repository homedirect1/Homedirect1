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
namespace Ced\HelpDesk\Block\Adminhtml\Dept\Edit\Tab;

class General extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /*
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     * */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /*
     * prepare form
     * @return $this
     * */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('ced_department');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General Information')]);
        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Department Name'),
                'title' => __('Department Name'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'code',
            'text',
            [
                'name' => 'code',
                'label' => __('Department Code'),
                'title' => __('Department Code'),
                'required' => true
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
        $fieldset->addField(
            'dept_signature',
            'editor',
            [
                'name' => 'dept_signature',
                'label' => __('Signature'),
                'title' => __('Signature'),
                'style' => 'height:5em',
                'required' => true,
                'config' => $this->_wysiwygConfig->getConfig()
            ]
        );
        if ($model = $this->_coreRegistry->registry('ced_department')) {
        	$fieldset->addField('id', 'hidden', ['name' => 'id']);
        	$form->setValues($model);
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }
 
    /**
     * Prepare label for tab
     *
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