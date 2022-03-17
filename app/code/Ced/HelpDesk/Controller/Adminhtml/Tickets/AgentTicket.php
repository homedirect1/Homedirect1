<?php
namespace Ced\HelpDesk\Controller\Adminhtml\Tickets;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
class AgentTicket extends \Magento\Backend\App\Action
{
	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;
	/**
	 * @param \Magento\Backend\App\Action\Context
     * @param \Magento\Framework\View\Result\PageFactory
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory
			) {
				$this->resultPageFactory = $resultPageFactory;
				parent::__construct($context);
	}
	/*
	 * Create Page
	 * */
	public function execute()
	{
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		return $this->resultPageFactory->create();
	}
}