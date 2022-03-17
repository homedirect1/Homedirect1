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
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;


class ListGroups extends Action
{
    protected $resultPageFactory;

    protected $session;

    protected $_coreRegistry = null;

    protected $_scopeConfig;

    private $redirect;


    /**
     * TODO
     *
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param ObjectManagerInterface $objectManager
     * @param Registry $registry
     * @param ScopeConfigInterface $scopeConfig
     * @param ResultFactory $resultFactory
     * @param RedirectInterface $redirect
     */
    public function __construct(
        Context $context,
        Session                             $customerSession,
        PageFactory                         $resultPageFactory,
        ObjectManagerInterface              $objectManager,
        Registry                            $registry,
        ScopeConfigInterface                $scopeConfig,
        ResultFactory                       $resultFactory,
        RedirectInterface                   $redirect
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_objectManager    = $objectManager;
        $this->_coreRegistry     = $registry;
        $this->session           = $customerSession;
        $this->_scopeConfig      = $scopeConfig;
        $this->resultFactory          = $resultFactory;
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
        /*
            @var \Magento\Framework\View\Result\Page $resultPage
        */
        if (empty($this->session->getCustomerId())) {
            $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $redirect->setPath('customer/account/login');
        }

        $resultPage = $this->resultPageFactory->create();
        $block      = $resultPage->getLayout()->getBlock('customer.account.link.back');
        if ($block) {
            $block->setRefererUrl($this->redirect->getRedirectUrl());
        }

        return $resultPage;

    }//end execute()


}//end class
