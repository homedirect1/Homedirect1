<?php
namespace Ced\CsReport\Controller\Adminhtml\Sales;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
class Index extends \Magento\Backend\App\Action
{
	protected $resultPageFactory = false;
	public function __construct(
		Context $context,
		PageFactory $resultPageFactory
	) {
		//die("cn");
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}

	public function execute()
	{	
		$this->resultPage = $this->resultPageFactory->create();  
		$this->resultPage->setActiveMenu('Ced_CsMarketplace::csmarketplace');
		$this->resultPage ->getConfig()->getTitle()->set((__('Vendor Sales Report')));
		return $this->resultPage;
	}

	
	protected function _isAllowed()
	{
		return true;
	}
}