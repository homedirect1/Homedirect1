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
 * @package     Ced_HelpDesk
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\HelpDesk\Block\Adminhtml\Ticket\Edit\Tab;

/**
 * Class General
 * @package Ced\HelpDesk\Block\Adminhtml\Ticket\Edit\Tab
 */
class General extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Department\CollectionFactory
     */
    protected $departmentCollectionFactory;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Agent\CollectionFactory
     */
    protected $agentCollectionFactory;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Priority\CollectionFactory
     */
    protected $priorityCollectionFactory;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Status\CollectionFactory
     */
    protected $statusCollectionFactory;

    /**
     * General constructor.
     * @param \Ced\HelpDesk\Model\ResourceModel\Department\CollectionFactory $departmentCollectionFactory
     * @param \Ced\HelpDesk\Model\ResourceModel\Agent\CollectionFactory $agentCollectionFactory
     * @param \Ced\HelpDesk\Model\ResourceModel\Priority\CollectionFactory $priorityCollectionFactory
     * @param \Ced\HelpDesk\Model\ResourceModel\Status\CollectionFactory $statusCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Ced\HelpDesk\Model\ResourceModel\Department\CollectionFactory $departmentCollectionFactory,
        \Ced\HelpDesk\Model\ResourceModel\Agent\CollectionFactory $agentCollectionFactory,
        \Ced\HelpDesk\Model\ResourceModel\Priority\CollectionFactory $priorityCollectionFactory,
        \Ced\HelpDesk\Model\ResourceModel\Status\CollectionFactory $statusCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    )
    {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->departmentCollectionFactory = $departmentCollectionFactory;
        $this->agentCollectionFactory = $agentCollectionFactory;
        $this->priorityCollectionFactory = $priorityCollectionFactory;
        $this->statusCollectionFactory = $statusCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return \Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $agent = [];
        $priority = [];
        $status = [];
        $department = [];
        $departmentData = $this->departmentCollectionFactory->create();
        $agentData = $this->agentCollectionFactory->create();
        $priorityData = $this->priorityCollectionFactory->create();
        $statusData = $this->statusCollectionFactory->create();
        if (isset($departmentData)) {
            foreach ($departmentData as $value) {
                $department[] = ['label' => $value->getName(),
                    'value' => $value->getCode()
                ];
            }
        }
        if (isset($agentData)) {
            foreach ($agentData as $value) {
                $agent[] = ['value' => $value->getId() . '-' . $value->getUsername(),
                    'label' => $value->getUsername()
                ];
            }
        }
        if (isset($priorityData)) {
            foreach ($priorityData as $value) {
                $priority[] = [
                    'label' => $value->getTitle(),
                    'value' => $value->getCode()
                ];
            }
        }
        if (isset($statusData)) {
            foreach ($statusData as $value) {
                $status[] = [
                    'label' => $value->getTitle(),
                    'value' => $value->getCode()
                ];
            }
        }
        $model = $this->_coreRegistry->registry('ced_ticket');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General Information')]);
        $fieldset->addField(
            'customer_name',
            'text',
            [
                'name' => 'customer_name',
                'label' => __('Customer Name'),
                'title' => __('Customer Name'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'customer_email',
            'text',
            [
                'name' => 'customer_email',
                'label' => __('Customer Email'),
                'title' => __('Customer Email'),
                'class' => 'validate-email',
                'required' => true
            ]
        );
        $fieldset->addField(
            'subject',
            'text',
            [
                'name' => 'subject',
                'label' => __('Subject'),
                'title' => __('Subject'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'order',
            'text',
            [
                'name' => 'order',
                'label' => __('Order'),
                'title' => __('Order'),
            ]
        );
        $fieldset->addField(
            'department',
            'select',
            [
                'name' => 'department',
                'label' => __('Department'),
                'title' => __('Department'),
                'required' => true,
                'values' => $department
            ]
        );
        $fieldset->addField(
            'agent',
            'select',
            [
                'name' => 'agent',
                'label' => __('Agent'),
                'title' => __('Agent'),
                'required' => true,
                'values' => $agent
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'values' => $status
            ]
        );
        $fieldset->addField(
            'priority',
            'select',
            [
                'name' => 'priority',
                'label' => __('Priority'),
                'title' => __('Priority'),
                'required' => true,
                'values' => $priority
            ]
        );
        $fieldset->addField(
            'message',
            'editor',
            [
                'name' => 'message',
                'label' => __('Content'),
                'title' => __('Content'),
                'style' => 'height:10em',
                'required' => true,
                'config' => $this->_wysiwygConfig->getConfig()
            ]
        );
        if ($model = $this->_coreRegistry->registry('ced_ticket')) {
            $model['agent'] = $model['agent'] . '-' . $model['agent_name'];
            $form->setValues($model);
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
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