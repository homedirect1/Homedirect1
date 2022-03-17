<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_CsAdvTransaction
  * @author   	 CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsAdvTransaction\Controller\Pay;

class View extends \Ced\CsMarketplace\Controller\Vendor
{

    public function execute()
    {
    	
    	$vid = $this->getRequest()->getParam('vendor_id');
    	if(!isset($vid))
    	{
    		$this->_redirect('csmarketplace/vpayments/index');
    	}
    	
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('View Transaction Details'));
        return $resultPage;
    }
}
