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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\HelpDesk\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class ViewTicket
 * @package Ced\HelpDesk\Controller\Index
 */
class ViewTicket extends \Magento\Framework\App\Action\Action
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
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * ViewTicket constructor.
     * @param \Ced\HelpDesk\Helper\Data $helpdeskHelper
     * @param \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketcollectionFactory
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Ced\HelpDesk\Helper\Data $helpdeskHelper,
        \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketcollectionFactory,
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->helpdeskHelper = $helpdeskHelper;
        $this->ticketcollectionFactory = $ticketcollectionFactory;
        parent::__construct($context);
    }

    /**
     * Create Page
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
        if (!$this->helpdeskHelper->getStoreConfig('helpdesk/general/enable')) {
            $this->_redirect('cms/index/index');
            return;
        }
        $email = $this->getRequest()->getParam('email');
        $id = $this->getRequest()->getParam('id');

        if (isset($id) && isset($email)) {
            $ticket = $this->ticketcollectionFactory->create()
                ->addFieldtoFilter('ticket_id', $id)->addFieldtoFilter('customer_email', $email);
        }
        if (!isset($ticket) || count($ticket->getData()) == 0) {
            $message = __('Please Check your Details. No Tickets matching with given Email id and Ticket id');
            $this->messageManager->addErrorMessage($message);
            $this->_redirect('helpdesk/index/view');
        } else {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('View Your Ticket'));
            return $resultPage;
        }
    }
}
