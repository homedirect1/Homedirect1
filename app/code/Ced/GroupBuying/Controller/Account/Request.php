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
 * @package     Ced_GroupBuying
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GroupBuying\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Response\RedirectInterface;


class Request extends Action
{

    protected $session;

    protected $_coreRegistry = null;

    protected $_scopeConfig;

    private $resultPageFactory;

    private $redirect;


    /**
     * TODO
     *
     * @param Context $context
     * @param Session $customerSession
     * @param ObjectManagerInterface $objectManager
     * @param Registry $registry
     * @param ScopeConfigInterface $scopeConfig
     * @param ResultFactory $resultFactory
     * @param PageFactory $resultPageFactory
     * @param RedirectInterface $redirect
     */
    public function __construct(
        Context         $context,
        Session $customerSession,
        ObjectManagerInterface $objectManager,
        Registry $registry,
        ScopeConfigInterface $scopeConfig,
        ResultFactory        $resultFactory,
        PageFactory $resultPageFactory,
        RedirectInterface $redirect
    ) {
        $this->_objectManager    = $objectManager;
        $this->_coreRegistry     = $registry;
        $this->session           = $customerSession;
        $this->_scopeConfig      = $scopeConfig;
        $this->resultFactory     = $resultFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->redirect = $redirect;
        parent::__construct($context);


    }//end __construct()


    /**
     * TODO
     *
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($this->session->isLoggedIn() === false) {
            return $redirect->setPath('customer/account/login');
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('My Group Buying'));
        $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
        if ($block) {
            $block->setRefererUrl($this->redirect->getRedirectUrl());
        }

        return $resultPage;

    }//end execute()


}//end class
