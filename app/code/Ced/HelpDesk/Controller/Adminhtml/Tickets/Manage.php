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
 * Class Manage
 * @package Ced\HelpDesk\Controller\Adminhtml\Tickets
 */
class Manage extends \Magento\Backend\App\Action
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
     * @var \Ced\HelpDesk\Model\ResourceModel\Message\CollectionFactory
     */
    protected $messageCollectionFactory;

    /**
     * Manage constructor.
     * @param \Ced\HelpDesk\Model\TicketFactory $ticketFactory
     * @param \Ced\HelpDesk\Model\ResourceModel\Message\CollectionFactory $messageCollectionFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Ced\HelpDesk\Model\TicketFactory $ticketFactory,
        \Ced\HelpDesk\Model\ResourceModel\Message\CollectionFactory $messageCollectionFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->ticketFactory = $ticketFactory;
        $this->messageCollectionFactory = $messageCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if (isset($id) && $id != null) {
            $ticketModel = $this->ticketFactory->create()
                ->load($id);
            $ticketId = $ticketModel->getTicketId();
            $data = $ticketModel->getData();
            $message = $this->messageCollectionFactory->create()
                ->addFieldToFilter('ticket_id', $ticketId)
                ->getData();
            $this->registry->register('ced_ticket_data', $data);
            $this->registry->register('ced_message', $message);
            $title = $data['customer_name'] . '-' . $data['ticket_id'];
        }
        $resultRedirect = $this->resultPageFactory->create();
        $resultRedirect->getConfig()->getTitle()->set($title);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        return $resultRedirect;
    }
}