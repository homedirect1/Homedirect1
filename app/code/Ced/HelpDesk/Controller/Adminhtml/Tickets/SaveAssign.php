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

use Magento\Backend\App\Action;

/**
 * Class SaveAssign
 * @package Ced\HelpDesk\Controller\Adminhtml\Tickets
 */
class SaveAssign extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\HelpDesk\Model\TicketFactory
     */
    protected $ticketFactory;

    /**
     * @var \Ced\HelpDesk\Model\AgentFactory
     */
    protected $agentFactory;

    /**
     * @var \Ced\HelpDesk\Model\MessageFactory
     */
    protected $messageFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Ced\HelpDesk\Helper\Data
     */
    protected $helpdeskHelper;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $state;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * SaveAssign constructor.
     * @param \Ced\HelpDesk\Model\TicketFactory $ticketFactory
     * @param \Ced\HelpDesk\Model\AgentFactory $agentFactory
     * @param \Ced\HelpDesk\Model\MessageFactory $messageFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Ced\HelpDesk\Helper\Data $helpdeskHelper
     * @param \Magento\Framework\Translate\Inline\StateInterface $state
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param Action\Context $context
     */
    public function __construct(
        \Ced\HelpDesk\Model\TicketFactory $ticketFactory,
        \Ced\HelpDesk\Model\AgentFactory $agentFactory,
        \Ced\HelpDesk\Model\MessageFactory $messageFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Ced\HelpDesk\Helper\Data $helpdeskHelper,
        \Magento\Framework\Translate\Inline\StateInterface $state,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        Action\Context $context
    )
    {
        $this->ticketFactory = $ticketFactory;
        $this->agentFactory = $agentFactory;
        $this->messageFactory = $messageFactory;
        $this->timezone = $timezone;
        $this->helpdeskHelper = $helpdeskHelper;
        $this->state = $state;
        $this->transportBuilder = $transportBuilder;
        parent::__construct($context);
    }

    /**
     * Save Assign Data
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $agentData = [];
        $data = $this->getRequest()->getPostValue();
        $ticketModel = $this->ticketFactory->create();
        $agentModel = $this->agentFactory->create();
        $messageModel = $this->messageFactory->create();
        $date = $this->timezone->date()->format('Y-m-d H:i:s');
        if (isset($data['agent'])) {
            $agentData = explode('-', $data['agent']);
            $data['agent_id'] = $agentData[0];
            $data['agent_name'] = $agentData[1];
        }
        if (!empty($data['id'])) {
            $ticketModel->load($data['id']);
            $ticketModel->setAgent($data['agent_id']);
            $ticketModel->setAgentName($data['agent_name']);
            if (isset($data['priority']) && !empty($data['priority'])) {
                $ticketModel->setPriority($data['priority']);
            }
            $ticketModel->save();
        }
        if (!empty($data['ticket_id'])) {
            try {
                $messageModel->setMessage($data['reassign_description']);
                $messageModel->setFrom($data['from']);
                $messageModel->setTo($data['agent_name']);
                $messageModel->setTicketId($data['ticket_id']);
                $messageModel->setCreated($date);
                $messageModel->setType('re_assign');
                $messageModel->save();
                $agentEmail = $agentModel->load($data['agent_id'])
                    ->getEmail();
                $data['reassign_description'] = strip_tags($data['reassign_description']);
                $helper = $this->helpdeskHelper;
                if ($helper->getStoreConfig('helpdesk/general/support_email')) {
                    if ($helper->getStoreConfig('helpdesk/email/mail_agent')) {
                        $this->mailAgentAssign($agentEmail, $data['agent_name'], $data['ticket_id'], $data['from'], $data['reassign_description']);
                    }
                } else {
                    $this->messageManager->addErrorMessage(__('Unable to send mail.'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        $this->messageManager->addSuccessMessage(
            __('Ticket Assign Successfully...')
        );
        $this->_redirect('helpdesk/tickets/ticketsinfo');
    }

    /**
     * Send email to Assignee agent
     * @param $agent_email
     * @param $agent_name
     * @param $ticketId
     * @param $assigner
     * @param $description
     */
    public function mailAgentAssign($agent_email, $agent_name, $ticketId, $assigner, $description)
    {
        if (!empty($agent_email) && !empty($agent_name)) {
            $senderName = "Support System";
            $senderEmail = $this->helpdeskHelper
                ->getStoreConfig('helpdesk/general/support_email');
            $this->state->suspend();
            try {
                $error = false;
                $sender = [
                    'name' => $senderName,
                    'email' => $senderEmail,
                ];
                $transport = $this->transportBuilder;
                $transport->setTemplateIdentifier('send_agent_email_assign_template')// this code we have mentioned in the email_templates.xml
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ])
                    ->setTemplateVars(['agent_name' => $agent_name,
                        'ticket_description' => $description,
                        'assigner' => $assigner,
                        'ticket_id' => $ticketId
                    ])
                    ->setFrom($sender)
                    ->addTo($agent_email);
                $a = $transport->getTransport();
                $a->sendMessage();
                $this->state->resume();
            } catch (\Exception $e) {
            }
            return;
        }
    }
}
