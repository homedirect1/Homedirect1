<?php
namespace Ced\CsReport\Controller\Adminhtml\Payment;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
class Index extends \Magento\Backend\App\Action{
	
	protected $resultPageFactory = false;
	public function __construct(
			Context $context,
			PageFactory $resultPageFactory
	) {
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}
	
	public function execute()
	{
		$this->resultPage = $this->resultPageFactory->create();
		$this->resultPage->setActiveMenu('Ced_CsMarketplace::csmarketplace');
		$this->resultPage ->getConfig()->getTitle()->set((__('Vendor Payment Report')));
		return $this->resultPage;
	}
	
	/*
	 * Check permission via ACL resource
	*/
	protected function _isAllowed()
	{
		return true;
	}
	
	
}