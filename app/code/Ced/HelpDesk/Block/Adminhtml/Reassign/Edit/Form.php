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

namespace Ced\HelpDesk\Block\Adminhtml\Reassign\Edit;

use Ced\HelpDesk\Model;

/**
 * Class Form
 * @package Ced\HelpDesk\Block\Adminhtml\Reassign\Edit
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $backendSession;

    /**
     * @var \Magento\Authorization\Model\RoleFactory
     */
    protected $roleFactory;

    /**
     * @var Model\DepartmentFactory
     */
    protected $departmentFactory;

    /**
     * @var Model\ResourceModel\Agent\CollectionFactory
     */
    protected $agentCollectionFactory;

    /**
     * @var Model\AgentFactory
     */
    protected $agentFactory;

    /**
     * @var Model\ResourceModel\Priority\CollectionFactory
     */
    protected $priorityCollectionFactory;

    /**
     * Form constructor.
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     * @param \Magento\Authorization\Model\RoleFactory $roleFactory
     * @param Model\DepartmentFactory $departmentFactory
     * @param Model\ResourceModel\Agent\CollectionFactory $agentCollectionFactory
     * @param Model\AgentFactory $agentFactory
     * @param Model\ResourceModel\Priority\CollectionFactory $priorityCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Ced\HelpDesk\Model\DepartmentFactory $departmentFactory,
        Model\ResourceModel\Agent\CollectionFactory $agentCollectionFactory,
        Model\AgentFactory $agentFactory,
        \Ced\HelpDesk\Model\ResourceModel\Priority\CollectionFactory $priorityCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    )
    {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->backendSession = $backendSession;
        $this->roleFactory = $roleFactory;
        $this->departmentFactory = $departmentFactory;
        $this->agentCollectionFactory = $agentCollectionFactory;
        $this->agentFactory = $agentFactory;
        $this->priorityCollectionFactory = $priorityCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('assign_form');
        $this->setTitle(__('Assign Ticket'));
    }

    /**
     * @return \Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $priority = [];
        $model = $this->_coreRegistry->registry('ced_ticket_assign');
        if ($model['id'] != null) {

            $form = $this->_formFactory->create(
                ['data' => ['id' => 'edit_form', 'action' => $this->getUrl('*/*/saveassign'), 'method' => 'post']]
            );

        } else {

            $form = $this->_formFactory->create(
                ['data' => ['id' => 'edit_form', 'action' => $this->getUrl('*/*/saveassign'), 'method' => 'post']]
            );

        }
        $agent = [];
        $agentIds = [];
        $userRoles = [];
        $user = $this->backendSession->getUser();
        $currentUser = $user->getData('username');
        $currentUserId = $user->getData('user_id');
        $userRoles = $this->roleFactory->create()->load('Administrators', 'role_name')->getRoleUsers();
        if (isset($model['department']) && !empty($model['department'])) {
            $departmentModel = $this->departmentFactory->create()->load($model['department'], 'code');
            $agentIds = explode(',', $departmentModel->getAgent());
            $head = explode(',', $departmentModel->getDepartmentHead());
            $ids = array_unique(array_merge($agentIds, $head));
            if ($model['department'] == 'admin') {
                $agentModel = $this->agentCollectionFactory->create();
                foreach ($agentModel as $value) {
                    if ($value->getUsername() != $currentUser) {
                        $agent[] = ['value' => $value->getId() . '-' . $value->getUsername(),
                            'label' => $value->getUsername()
                        ];
                    }
                }
            } else {
                $agentModel = $this->agentCollectionFactory->create()->addFieldToFilter('id', ['in' => $ids]);
                foreach ($agentModel as $value) {
                    if ($value->getUsername() != $currentUser) {
                        $agent[] = ['value' => $value->getId() . '-' . $value->getUsername(),
                            'label' => $value->getUsername()
                        ];
                    }
                }
            }
        }
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Assign Ticket')]);
        if ($model['id'] != null) {
            $agentData = implode('-', [$model['agent'], $model['agent_name']]);
            $fieldset->addField(
                'agent',
                'select',
                [
                    'name' => 'agent',
                    'label' => __('Agent'),
                    'title' => __('Agent'),
                    'required' => true,
                    'values' => $agent,
                    'value' => $agentData
                ]
            );
        }
        $headUserId = $this->agentFactory->create()->load($head)->getUserId();
        if ($model['id'] != null && (in_array($currentUserId, $userRoles) || ($currentUserId == $headUserId))) {
            $priorityModel = $this->priorityCollectionFactory->create();
            foreach ($priorityModel as $item) {
                $priority[] = ['label' => $item->getTitle(), 'value' => $item->getCode()];
            }
            $fieldset->addField(
                'priority',
                'select',
                [
                    'name' => 'priority',
                    'label' => __('Priority'),
                    'title' => __('Priority'),
                    'required' => true,
                    'values' => $priority,
                    'value' => $model['priority']
                ]
            );
        }
        $fieldset->addField(
            'reassign_description',
            'editor',
            [
                'name' => 'reassign_description',
                'label' => __('Description'),
                'title' => __('Description'),
                'style' => 'height:10em',
                'required' => true,
                'config' => $this->_wysiwygConfig->getConfig()
            ]
        );
        if ($model['id'] != null) {
            $fieldset->addField(
                'id',
                'hidden',
                [
                    'name' => 'id',
                    'value' => $model['id'],
                ]
            );
            $fieldset->addField(
                'ticket_id',
                'hidden',
                [
                    'name' => 'ticket_id',
                    'value' => $model['ticket_id'],
                ]
            );
            $fieldset->addField(
                'from',
                'hidden',
                [
                    'name' => 'from',
                    'value' => $currentUser,
                ]
            );
        }
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}