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

class Plan extends Generic implements TabInterface
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
        
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Subscription Type Information'), 'class' => 'fieldset-wide']
        );
        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'id']);
        }
        
        $fieldset->addField('plan_ids', 'hidden', ['name' => 'plan_ids', 'id' => 'plan_ids']);
        $fieldset->addField('plan_ids_old', 'hidden', ['name' => 'plan_ids_old']);

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Subscription Type Name'),
                'title' => __('Subscription Type Name'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Subscription Type Description'),
                'title' => __('Subscription Type Description'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'type',
            'select',
            [
                'name' => 'type',
                'label' => __('Duration type'),
                'title' => __('Duration type'),
                'required' => true,
                'options' => $this->getDurationTypes()
            ]
        );
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
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => true,
                'class' => 'validate-greater-than-zero'
            ]
        );
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
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
     * Get Duration Types
     *
     * @return array
     */
    public function getDurationTypes()
    {
        $options = [];
        $termData = $this->_coreRegistry->registry('terms_data');
        
        if (!(bool)empty($termData)) {
            foreach ($termData as $term) {
                $options [$term['entity_id']] =  $term['title'];
            }
        }
        return $options;
    }
}
