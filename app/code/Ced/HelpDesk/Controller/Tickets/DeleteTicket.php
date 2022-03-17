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
 * Class DeleteTicket
 * @package Ced\HelpDesk\Controller\Tickets
 */
class DeleteTicket extends \Magento\Framework\App\Action\Action
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
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * DeleteTicket constructor.
     * @param \Ced\HelpDesk\Helper\Data $helpdeskHelper
     * @param \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketcollectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param Context $context
     */
    public function __construct(
        \Ced\HelpDesk\Helper\Data $helpdeskHelper,
        \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketcollectionFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\User\Model\UserFactory $userFactory,
        Context $context
    )
    {
        $this->helpdeskHelper = $helpdeskHelper;
        $this->ticketcollectionFactory = $ticketcollectionFactory;
        $this->timezone = $timezone;
        $this->userFactory = $userFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
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
                    $timeClose = 2;
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

}