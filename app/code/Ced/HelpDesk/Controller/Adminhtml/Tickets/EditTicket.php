<?php
namespace Ced\HelpDesk\Controller\Adminhtml\Tickets;

class EditTicket extends \Magento\Backend\App\Action
{
	/**
	 * @var PageFactory
	 */
	protected $resultForwordFactory;
	/**
	 * TicketsInfo action
	 *
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\Magento\Backend\Model\View\Result\ForwardFactory $resultForwordFactory
			) {
				$this->resultForwordFactory = $resultForwordFactory;
				parent::__construct($context);
	}
	public function execute()
	{	
		$resultForward = $this->resultForwordFactory->create();
		return $resultForward->forward('newticket');
	}
}