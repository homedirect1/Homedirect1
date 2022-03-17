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
 * Class NewTicket
 * @package Ced\HelpDesk\Controller\Adminhtml\Tickets
 */
class NewTicket extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * @var \Ced\HelpDesk\Model\TicketFactory
     */
    protected $ticketFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $session;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Agent\CollectionFactory
     */
    protected $agentCollectionFactory;

    /**
     * NewTicket constructor.
     * @param \Ced\HelpDesk\Model\TicketFactory $ticketFactory
     * @param \Magento\Backend\Model\Auth\Session $session
     * @param \Ced\HelpDesk\Model\ResourceModel\Agent\CollectionFactory $agentCollectionFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Ced\HelpDesk\Model\TicketFactory $ticketFactory,
        \Magento\Backend\Model\Auth\Session $session,
        \Ced\HelpDesk\Model\ResourceModel\Agent\CollectionFactory $agentCollectionFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->ticketFactory = $ticketFactory;
        $this->session = $session;
        $this->agentCollectionFactory = $agentCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $role = $this->getUserData();
        if ($role == 'Agent') {
            return $this->_redirect('helpdesk/tickets/ticketsinfo');
        } else {
            $id = $this->getRequest()->getParam('id');
            if (isset($id) && $id != null) {
                $data = $this->ticketFactory->create()
                    ->load($id)
                    ->getData();
                $this->registry->register('ced_ticket', $data);
            }
            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            return $this->resultPageFactory->create();
        }
    }

    /**
     * @return mixed
     */
    public function getUserData()
    {
        $user_id = $this->session
            ->getUser()
            ->getData('user_id');
        $data = $this->agentCollectionFactory->create()
            ->addFieldToFilter('user_id', $user_id)
            ->getFirstItem()
            ->getRoleName();
        return $data;
    }
}