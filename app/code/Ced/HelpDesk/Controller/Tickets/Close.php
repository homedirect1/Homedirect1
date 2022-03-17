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

namespace Ced\HelpDesk\Controller\Tickets;

use Magento\Framework\App\Action\Context;

/**
 * Class Close
 * @package Ced\HelpDesk\Controller\Tickets
 */
class Close extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var \Ced\HelpDesk\Helper\Data
     */
    protected $helpdeskHelper;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $ticketcollectionFactory;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Department\CollectionFactory
     */
    protected $departmentcollectionFactory;

    /**
     * @var \Ced\HelpDesk\Model\AgentFactory
     */
    protected $agentFactory;

    /**
     * @var \Magento\Authorization\Model\RoleFactory
     */
    protected $roleFactory;

    /**
     * @var \Magento\User\Model\ResourceModel\User\CollectionFactory
     */
    protected $usercollectionFactory;

    /**
     * Close constructor.
     * @param \Ced\HelpDesk\Helper\Data $helpdeskHelper
     * @param \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketcollectionFactory
     * @param \Ced\HelpDesk\Model\ResourceModel\Department\CollectionFactory $departmentcollectionFactory
     * @param \Ced\HelpDesk\Model\AgentFactory $agentFactory
     * @param \Magento\Authorization\Model\RoleFactory $roleFactory
     * @param \Magento\User\Model\ResourceModel\User\CollectionFactory $usercollectionFactory
     * @param Context $context
     */
    public function __construct(
        \Ced\HelpDesk\Helper\Data $helpdeskHelper,
        \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketcollectionFactory,
        \Ced\HelpDesk\Model\ResourceModel\Department\CollectionFactory $departmentcollectionFactory,
        \Ced\HelpDesk\Model\AgentFactory $agentFactory,
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Magento\User\Model\ResourceModel\User\CollectionFactory $usercollectionFactory,
        Context $context
    )
    {
        $this->helpdeskHelper = $helpdeskHelper;
        $this->ticketcollectionFactory = $ticketcollectionFactory;
        $this->departmentcollectionFactory = $departmentcollectionFactory;
        $this->agentFactory = $agentFactory;
        $this->roleFactory = $roleFactory;
        $this->usercollectionFactory = $usercollectionFactory;
        parent::__construct($context);
    }

    /**
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $ids = [];
        $helper = $this->helpdeskHelper;
        if (!$helper->getStoreConfig('helpdesk/general/enable')) {
            $this->_redirect('cms/index/index');
            return;
        }
        $ticket_id = $this->getRequest()->getParam('id');
        $status = $this->getRequest()->getParam('status_id');
        $comments = $this->getRequest()->getParam('comments');
        if (!empty($ticket_id)) {
            $ticketModel = $this->ticketcollectionFactory->create()
                ->addFieldToFilter('ticket_id', $ticket_id)
                ->getFirstItem();
            $ticketModel->setData('status', $status);
            $ticketModel->setData('closing_message', $comments);
            $ticketModel->save();
            $customer_name = $ticketModel->getCustomerName();
            $customer_email = $ticketModel->getCustomerEmail();
            $agent_id = $ticketModel->getAgent();
            $departmentCode = $ticketModel->getDepartment();
            $agent_name = $ticketModel->getAgentName();
            $departmentHeadId = $this->departmentcollectionFactory->create()
                ->addFieldToFilter('code', $departmentCode)
                ->getFirstItem()
                ->getDepartmentHead();
            $agentModel = $this->agentFactory->create();
            $agentRole = $agentModel->load($agent_id)->getRoleName();
            if ($helper->getStoreConfig('helpdesk/general/support_email')) {
                if (!empty($departmentHeadId) && !empty($agent_id)) {
                    if ($departmentHeadId == $agent_id) {
                        if ($helper->getStoreConfig('helpdesk/email/mail_head') && $helper->getStoreConfig('helpdesk/email/mail_agent')) {
                            $agentLoad = $agentModel->load($agent_id);
                            $agnentEmail = $agentLoad->getEmail();
                            $helper->mailAgentStatus($agent_name, $agnentEmail, $customer_name, $ticket_id, $status, $comments);
                        }
                    } elseif ($agentRole != 'Administrators') {
                        if ($helper->getStoreConfig('helpdesk/email/mail_agent')) {
                            $agentModel = $this->agentFactory->create();
                            $agentLoad = $agentModel->load($agent_id);
                            $agnentEmail = $agentLoad->getEmail();
                            $helper->mailAgentStatus($agent_name, $agnentEmail, $customer_name, $ticket_id, $status, $comments);
                        }
                        if ($helper->getStoreConfig('helpdesk/email/mail_head')) {
                            $agentModel = $this->agentFactory->create();
                            $departmentHeadLoad = $agentModel->load($departmentHeadId);
                            $headName = $departmentHeadLoad->getUsername();
                            $headEmail = $departmentHeadLoad->getEmail();
                            $helper->mailAgentStatus($headName, $headEmail, $customer_name, $ticket_id, $status, $comments);
                        }
                    }
                }
                if ($helper->getStoreConfig('helpdesk/email/mail_customer')) {
                    $helper->mailCustomerStatus($customer_name, $customer_email, $ticket_id, $status);
                }
                $ids = $this->roleFactory->create()->load('Administrators', 'role_name')->getRoleUsers();
                $adminData = $this->usercollectionFactory->create()
                    ->addFieldToFilter('main_table.user_id', ['in' => $ids])
                    ->addFieldToSelect('*')
                    ->getData();
                if ($helper->getStoreConfig('helpdesk/email/mail_admin')) {
                    if (is_array($adminData) && !empty($adminData)) {
                        foreach ($adminData as $value) {
                            $helper->mailAdminStatus($value['username'], $value['email'], $customer_name, $ticket_id, $status, $comments);
                        }
                    }
                }
            } else {
                $this->messageManager->addErrorMessage(__('Unable to send mail.'));
            }
            $this->_redirect('helpdesk/tickets/index');
            return;
        }
    }
}
