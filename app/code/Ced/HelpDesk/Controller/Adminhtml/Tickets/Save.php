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

namespace Ced\HelpDesk\Controller\Adminhtml\Tickets;

/**
 * Class Save
 * @package Ced\HelpDesk\Controller\Adminhtml\Tickets
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwordFactory;

    /**
     * @var \Ced\HelpDesk\Helper\Data
     */
    protected $helpdeskHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Ced\HelpDesk\Model\TicketFactory
     */
    protected $ticketFactory;

    /**
     * @var \Ced\HelpDesk\Model\MessageFactory
     */
    protected $messageFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authsession;

    /**
     * @var \Ced\HelpDesk\Model\DepartmentFactory
     */
    protected $departmentFactory;

    /**
     * @var \Ced\HelpDesk\Model\AgentFactory
     */
    protected $agentFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * Save constructor.
     * @param \Ced\HelpDesk\Helper\Data $helpdeskHelper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Ced\HelpDesk\Model\TicketFactory $ticketFactory
     * @param \Ced\HelpDesk\Model\MessageFactory $messageFactory
     * @param \Magento\Backend\Model\Auth\Session $authsession
     * @param \Ced\HelpDesk\Model\DepartmentFactory $departmentFactory
     * @param \Ced\HelpDesk\Model\AgentFactory $agentFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwordFactory
     */
    public function __construct(
        \Ced\HelpDesk\Helper\Data $helpdeskHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Ced\HelpDesk\Model\TicketFactory $ticketFactory,
        \Ced\HelpDesk\Model\MessageFactory $messageFactory,
        \Magento\Backend\Model\Auth\Session $authsession,
        \Ced\HelpDesk\Model\DepartmentFactory $departmentFactory,
        \Ced\HelpDesk\Model\AgentFactory $agentFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwordFactory
    )
    {
        $this->resultForwordFactory = $resultForwordFactory;
        $this->helpdeskHelper = $helpdeskHelper;
        $this->timezone = $timezone;
        $this->ticketFactory = $ticketFactory;
        $this->messageFactory = $messageFactory;
        $this->authsession = $authsession;
        $this->departmentFactory = $departmentFactory;
        $this->agentFactory = $agentFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Save Ticket Information
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $back = $this->getRequest()->getParam('back');
        $helper = $this->helpdeskHelper;
        $date = $this->timezone->date()->format('Y-m-d H:i:s');
        if (isset($data['agent'])) {
            $a = explode('-', $data['agent']);
            $data['agent'] = $a[0];
            $data['agent_name'] = $a[1];
        }
        $ticketModel = $this->ticketFactory->create();
        if (!empty($data['id'])) {
            $ticketModel->load($data['id']);
            $ticketModel->setData($data);
            $ticketModel->save();
            $this->messageManager->addSuccessMessage(
                __('Save Ticket Successfully...')
            );
        } else {
            $customerId = $this->getCustomerId($data['customer_email']);
            if (!$customerId) {
                $customerId = 'guest';
            }
        }
        if (isset($customerId) && !empty($customerId)) {
            $ticketModel = $this->ticketFactory->create();
            $ticketCount = $ticketModel->getCollection()->count();
            if ($ticketCount > 0) {
                $ticketId = $ticketModel->getCollection()->getLastItem()->getTicketId();
                $ticketId = $ticketId + 1;
            } else {
                $ticketId = 100000001;
            }
            $ticketModel->setTicketId($ticketId);
            $ticketModel->setCustomerId($customerId);
            $ticketModel->setCustomerName($data['customer_name']);
            $ticketModel->setCustomerEmail($data['customer_email']);
            $ticketModel->setSubject($data['subject']);
            $ticketModel->setOrder($data['order']);
            $ticketModel->setDepartment($data['department']);
            $ticketModel->setAgent($data['agent']);
            $ticketModel->setAgentName($data['agent_name']);
            $ticketModel->setStatus($data['status']);
            $ticketModel->setPriority($data['priority']);
            if (!empty($data['order'] && isset($data['order']))) {
                $ticketModel->setOrder($data['order']);
            } else {
                $ticketModel->setOrder('N/A');
            }
            $ticketModel->setStoreView('1');
            $ticketModel->setNumMsg('1');
            $ticketModel->setLock('0');
            $ticketModel->setCreatedTime($date);
            $ticketModel->setMessage($data['message']);
            $ticketModel->save();
            $messageModel = $this->messageFactory->create();
            $messageModel->setMessage($data['message']);
            $username = $this->authsession
                ->getUser()
                ->getUsername();
            $messageModel->setFrom($username);
            $messageModel->setTo($ticketModel->getCustomerEmail());
            $messageModel->setTicketId($ticketModel->getTicketId());
            $messageModel->setCreated($date);
            $messageModel->setType('reply');
            $messageModel->save();
            $data['id'] = $ticketModel->getId();
            if ($helper->getStoreConfig('helpdesk/general/support_email')) {
                if ($helper->getStoreConfig('helpdesk/email/mail_customer')) {
                    $helper->sendCustomerEmail($ticketModel->getTicketId(), $data['customer_name'], $data['customer_email']);
                }
                $departmentHeadId = $this->departmentFactory->create()->load($data['department'], 'code')->getDepartmentHead();
                $agentId = $data['agent'];
                if ($departmentHeadId == $agentId) {
                    if (($helper->getStoreConfig('helpdesk/email/mail_agent')) || ($helper->getStoreConfig('helpdesk/email/mail_head'))) {
                        $agentModel = $this->agentFactory->create()->load($agentId);
                        $agentEmail = $agentModel->getEmail();
                        $agent_name = $agentModel->getUsername();
                        $a = $helper->mailAgentCreateByAdmin($ticketId, $data['customer_name'], $agentEmail, $agent_name);
                    }
                } else {
                    if ($helper->getStoreConfig('helpdesk/email/mail_agent')) {
                        $agentModel = $this->agentFactory->create()->load($agentId);
                        $agentEmail = $agentModel->getEmail();
                        $agent_name = $agentModel->getUsername();
                        $b = $helper->mailAgentCreateByAdmin($ticketId, $data['customer_name'], $agentEmail, $agent_name);
                    }
                    if ($helper->getStoreConfig('helpdesk/email/mail_head')) {
                        $agentModel = $this->agentFactory->create()->load($departmentHeadId);
                        $headEmail = $agentModel->getEmail();
                        $head_name = $agentModel->getUsername();
                        $c = $helper->mailAgentCreateByAdmin($ticketId, $data['customer_name'], $headEmail, $head_name);
                    }
                }
            } else {
                $this->messageManager->addErrorMessage(__('Unable to send mail.'));
            }
            $this->messageManager->addSuccessMessage(
                __('Save Ticket Successfully...')
            );
        } else {
            $this->messageManager->addErrorMessage(
                __('Customer with this email does not exist...')
            );
            return $this->_redirect('*/*/newticket');
        }
        (isset($back) && $back == 'edit' && isset($data['id'])) ? $this->_redirect('*/*/newticket/id/' . $data['id'])
            : $this->_redirect('helpdesk/tickets/ticketsinfo');
    }

    /**
     * Get Customer Id
     * @param $customerEmail
     * @return mixed
     */
    public function getCustomerId($customerEmail)
    {
        $customerId = $this->customerCollectionFactory->create()
            ->addFieldToFilter('email', $customerEmail)
            ->getFirstItem()
            ->getId();
        return $customerId;
    }

}
