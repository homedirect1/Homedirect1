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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Controller\Account;

class View extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $_custmerSesion;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
    	\Magento\Framework\App\Action\Context $context,
    	\Magento\Customer\Model\Session $session,
    		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    	$this->_scopeConfig = $scopeConfig;
        $this->resultPageFactory = $resultPageFactory;
        $this->_custmerSesion = $session;
        parent::__construct($context);
    }
    public function execute()
    {
    	
    	$check = $this->_scopeConfig->getValue('affiliate/general/activation',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    	 
    	if(!$check){
    		$resultRedirect = $this->resultRedirectFactory->create();
    		$resultRedirect->setPath('customer/account/index');
    		return $resultRedirect;
    	}
    	
    	if ($this->_custmerSesion->isLoggedIn()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/account/index');
            return $resultRedirect;
        }
    	     
    	$resultPage = $this->resultPageFactory->create();
    	$resultPage->getConfig()->getTitle()->prepend(__('Affiliate Program'));
    	$resultPage->getConfig()->getTitle()->prepend(__('Affiliate Program'));
    	return $resultPage;
    }
}
