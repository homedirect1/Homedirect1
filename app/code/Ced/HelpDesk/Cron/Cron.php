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

namespace Ced\HelpDesk\Cron;

/**
 * Class Cron
 * @package Ced\HelpDesk\Cron
 */
class Cron
{
    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    public $_file;

    /**
     * @var \Ced\HelpDesk\Helper\Data
     */
    protected $helpdeskHelper;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $ticketcollectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Message\CollectionFactory
     */
    protected $messagecollectionFactory;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * @var \Ced\HelpDesk\Model\AgentFactory
     */
    protected $agentFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Ced\HelpDesk\Model\MessageFactory
     */
    protected $messageFactory;

    /**
     * Cron constructor.
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketcollectionFactory
     * @param \Ced\HelpDesk\Helper\Data $helpdeskHelper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Ced\HelpDesk\Model\ResourceModel\Message\CollectionFactory $messagecollectionFactory
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Ced\HelpDesk\Model\AgentFactory $agentFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Ced\HelpDesk\Model\MessageFactory $messageFactory
     */
    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $file,
        \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketcollectionFactory,
        \Ced\HelpDesk\Helper\Data $helpdeskHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Ced\HelpDesk\Model\ResourceModel\Message\CollectionFactory $messagecollectionFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Ced\HelpDesk\Model\AgentFactory $agentFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Ced\HelpDesk\Model\MessageFactory $messageFactory
    )
    {
        $this->_file = $file;
        $this->helpdeskHelper = $helpdeskHelper;
        $this->ticketcollectionFactory = $ticketcollectionFactory;
        $this->timezone = $timezone;
        $this->messagecollectionFactory = $messagecollectionFactory;
        $this->userFactory = $userFactory;
        $this->agentFactory = $agentFactory;
        $this->filesystem = $filesystem;
        $this->messageFactory = $messageFactory;
    }

    /*
     * Delete Tickets
     */
    public function deleteTicket()
    {
        $helper = $this->helpdeskHelper;
        $enable = $helper->getStoreConfig('helpdesk/general/enable');
        if (!$enable) {
            return;
        }
        $detete_ticket = $helper->getStoreConfig('helpdesk/general/delete_ticket');
        $timeDelete = $helper->getStoreConfig('helpdesk/general/auto_delete');
        if ($detete_ticket) {
            if ($timeDelete) {
                $ticketModel = $this->ticketcollectionFactory->create();
                $count = $ticketModel->count();
                if ($count > 0) {
                    $deteledTickets = [];
                    foreach ($ticketModel as $tickets) {
                        $created_time = $tickets->getCreatedTime();

                        $created_time = date_create($created_time);
                        $ticket_id = $tickets->getTicketId();
                        $customerId = $tickets->getCustomerId();
                        $date = $this->timezone->date()->format('Y-m-d H:i:s');

                        $date = date_create($date);
                        $difference = date_diff($date, $created_time);
                        $daysdiff = $difference->d;
                        if ($daysdiff >= $timeDelete) {

                            $deteledTickets[] = $ticket_id;
                            $tickets->delete();
                            $this->unlinkUrl($ticket_id, $customerId);
                            $msgModel = $this->messagecollectionFactory->create()->addFieldToFilter('ticket_id', $ticket_id);
                            foreach ($msgModel as $msg) {
                                $msg->delete();
                            }
                        }
                    }
                    $message = '';
                    for ($i = 0; $i < count($deteledTickets); $i++) {
                        $message .= $deteledTickets[$i] . ',';
                    }
                    $message = substr($message, 0, -1);
                    $admin_data = $this->userFactory->create()->load(1);
                    $adminMail = $admin_data->getEmail();
                    $adminName = $admin_data->getUsername();
                    if ($helper->getStoreConfig('helpdesk/general/support_email')) {
                        $mail = $helper->expireTicketDelete($message, $adminMail, $adminName);
                    }
                }
            }
        }
    }

    /*
     *
     * Close Tickets
     */
    public function closeTicket()
    {
        $helper = $this->helpdeskHelper;
        $enable = $helper->getStoreConfig('helpdesk/general/enable');
        if (!$enable) {
            return;
        }
        $close_ticket = $helper->getStoreConfig('helpdesk/general/close_ticket');
        $timeClose = $helper->getStoreConfig('helpdesk/general/auto_close');
        if ($timeClose) {
            $ticketModel = $this->ticketcollectionFactory->create();
            $count = $ticketModel->count();
            if ($count > 0) {
                $closedTickets = [];
                foreach ($ticketModel as $tickets) {
                    $created_time = $tickets->getCreatedTime();
                    $created_time = date_create($created_time);
                    $ticket_id = $tickets->getTicketId();
                    $date = $date = $this->timezone->date()->format('Y-m-d H:i:s');
                    $date = date_create($date);
                    $difference = date_diff($date, $created_time);
                    $daysdiff = $difference->d;

                    if ($daysdiff >= $timeClose) {
                        $closedTickets[] = $tickets->getTicketId();
                        $tickets->setData('status', 'Closed')->save();
                    }
                }

                $message = '';
                for ($i = 0; $i < count($closedTickets); $i++) {
                    $message .= $closedTickets[$i] . ',';
                }

                $message = substr($message, 0, -1);
                $admin_data = $this->userFactory->create()->load(1);
                $adminMail = $admin_data->getEmail();
                $adminName = $admin_data->getUsername();
                $type = 'closed';
                if ($helper->getStoreConfig('helpdesk/general/support_email')) {
                    $mail = $helper->expireTicketDelete($message, $adminMail, $adminName, $type);
                }
            }
        }
    }

    /*
     * Notify Staff to reply at a particular time Interval
     */
    public function notifyStaff()
    {
        $helper = $this->helpdeskHelper;
        $enable = $helper->getStoreConfig('helpdesk/general/enable');
        if (!$enable) {
            return;
        }
        $notify_staff = $helper->getStoreConfig('helpdesk/general/notify_staff');
        $notify_time = $helper->getStoreConfig('helpdesk/general/notify_time');
        if ($notify_staff) {
            if ($notify_time) {
                $ticketModel = $this->ticketcollectionFactory->create();
                $count = $ticketModel->count();
                if ($count > 0) {
                    $agentNotifyData = [];
                    foreach ($ticketModel as $tickets) {
                        $created_time = $tickets->getCreatedTime();
                        $created_time = date_create($created_time);
                        $ticket_id = $tickets->getTicketId();
                        $date = $this->timezone->date()->format('Y-m-d H:i:s');
                        $a = $date;
                        $date = date_create($date);
                        $difference = date_diff($date, $created_time);
                        $dayDiff = $difference->d;
                        $hoursdiff = $difference->h;
                        if (($hoursdiff >= $notify_time || $dayDiff > 0) && ($tickets->getStatus() != 'Closed' || $tickets->getStatus() != 'Resolved')) {
                            $agentEmail = $this->agentFactory->create()->load($tickets->getAgent())->getEmail();
                            $agentName = $tickets->getAgentName();
                            $agentNotifyData[] = ['ticket_id' => $ticket_id, 'agent_name' => $agentName, 'agent_email' => $agentEmail];
                        }
                    }
                    if ($helper->getStoreConfig('helpdesk/general/support_email')) {
                        if (!empty($agentNotifyData)) {
                            $helperObject = $this->helpdeskHelper;
                            foreach ($agentNotifyData as $value) {
                                $helperObject->notifyAgentMail($value['ticket_id'], $value['agent_name'], $value['agent_email']);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Unlink Url
     * @param $ticketId
     * @param $customerId
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function unlinkUrl($ticketId, $customerId)
    {
        $path = $this->filesystem
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $abs_path = $path->getAbsolutePath('images/helpdesk/' . $customerId . '/' . $ticketId . '/');
        $mesModel = $this->messageFactory->create();
        $mesCollection = $mesModel->getCollection()->addFieldToFilter('ticket_id', $ticketId);
        foreach ($mesCollection->getItems() as $message) {
            $attach = $message->getAttachment();
            $allAttach = explode(',', $attach);
            if (!empty($allAttach) && is_array($allAttach)) {
                foreach ($allAttach as $value) {
                    if ($this->_file->isExists($abs_path . $value)) {
                        $this->_file->deleteFile($abs_path . $value);
                    }
                }
            }
        }
    }

}
