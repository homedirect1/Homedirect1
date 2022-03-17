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
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Response\RedirectInterface;


class View extends Action
{

    protected $resultPageFactory;

    protected $_coreRegistry = null;

    protected $_scopeConfig;

    private $redirect;


    /**
     * TODO
     *
     * @param PageFactory $resultPageFactory
     * @param ObjectManagerInterface $objectManager
     * @param Registry $registry
     * @param ScopeConfigInterface $scopeConfig
     * @param RedirectInterface $redirect
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        PageFactory                         $resultPageFactory,
        Registry                            $registry,
        ScopeConfigInterface                $scopeConfig,
        RedirectInterface                   $redirect
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry     = $registry;
        $this->_scopeConfig      = $scopeConfig;
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
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Request Quantity'));

        $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
        if ($block) {
            $block->setRefererUrl($this->redirect->getRedirectUrl());
        }

        return $resultPage;

    }//end execute()


}//end class
