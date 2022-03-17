<?php

namespace Ced\CsReport\Controller\Adminhtml\Payment;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class Vpaymentsgrid extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor
{
    
	/**
	 * Grid action
	 *
	 * @return void
	 */
	public function execute()
	{
		$this->_view->loadLayout(false);
		$this->_view->renderLayout();
	}
}