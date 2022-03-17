<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Block\Adminhtml\Plans\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Widget\Form\Generic;
use Webkul\Recurring\Model\Plans\Source\Status as EnabledDisabled;

class Properties extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('recurring_data');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('plan_');
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Plans Information'), 'class' => 'fieldset-wide']
            );
        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'id']);
        }
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'options' => $this->toOptionArray()
            ]
        );
        $fieldset->addField(
            'customer_start_date',
            'select',
            [
                'label' => __('Can Customer Define the Start Date'),
                'title' => __('Can Customer Define the Start Date'),
                'name' => 'customer_start_date',
                'required' => true,
                'options' => $this->toOptionArray()
            ]
        );
        
        $fieldset->addField(
            'initial_fee_allowed',
            'select',
            [
                'label' => __('Initial Fee Allowed'),
                'title' => __('Initial Fee Allowed'),
                'name' => 'initial_fee_allowed',
                'required' => true,
                'options' => $this->toOptionArray()
            ]
        );
        $fieldset->addField(
            'website',
            'select',
            [
                'label' => __('Associated to Website'),
                'title' => __('Associated to Website'),
                'name' => 'website',
                'required' => true,
                'options' => $this->getWebsites()
            ]
        );
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
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

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Supplier Data');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Supplier Data');
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
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            EnabledDisabled::ENABLED => __("Enabled"),
            EnabledDisabled::DISABLED => __("Disabled")
        ];
    }
    
    /**
     * Get websites
     *
     * @return array
     */
    public function getWebsites()
    {
        $options = [];
        $allWebsites = $this->systemStore->getWebsiteValuesForForm();
        foreach ($allWebsites as $website) {
            $options[$website['value']] = $website['label'];
        }
        return $options;
    }
}
