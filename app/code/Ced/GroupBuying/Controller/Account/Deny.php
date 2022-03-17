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

use Ced\GroupBuying\Model\GroupLogFactory;
use Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory;
use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;



class Deny extends Action
{
    protected $resultPageFactory;

    protected $_objectManager;

    protected $_coreRegistry = null;

    protected $_scopeConfig;
    private $request;
    private $groupLog;
    private $customerFactory;
    private $_customerSession;
    private $_giftCollectionFactory;


    /**
     * TODO
     *
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param CollectionFactory $giftCollectionFactory
     * @param GroupLogFactory $groupLogFactory
     * @param CustomerFactory $customerFactory
     * @param SessionFactory $customerSession
     * @param ManagerInterface $messageManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Http $request
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        PageFactory                         $resultPageFactory,
        Registry                            $registry,
        CollectionFactory                   $giftCollectionFactory,
        GroupLogFactory                     $groupLogFactory,
        CustomerFactory                     $customerFactory,
        SessionFactory                      $customerSession,
        ScopeConfigInterface                $scopeConfig,
        Http                                $request,
        ResultFactory        $resultFactory
    ) {
        $this->resultPageFactory      = $resultPageFactory;
        $this->_coreRegistry          = $registry;
        $this->_giftCollectionFactory = $giftCollectionFactory;
        $this->_customerSession       = $customerSession;
        $this->_scopeConfig           = $scopeConfig;

        $this->customerFactory = $customerFactory;
        $this->groupLog        = $groupLogFactory;
        $this->request = $request;
        $this->resultFactory = $resultFactory;
        parent::__construct($context);


    }//end __construct()


    /**
     * TODO
     *
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $param      = $this->request->getParam('gid');
        $customerId = $this->_customerSession->create()->getCustomer()->getId();
        $customer   = $this->customerFactory->create()->load($customerId);
        $data       = $this->_giftCollectionFactory->create()->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'guest_email',
            $customer['email']
        )->addFieldToFilter(
            'groupgift_id',
            $param
        )->getFirstItem();

        try {
            $data->setData('request_approval', 1);
            $data->save();
            $groupLog  = $this->groupLog->create();
            $guestName = $data->getGuestName();
            $groupLog->setGroupId($param);
            $groupLog->setMemberName($guestName);
            $groupLog->setLog($guestName.' denied the group invitation!');
            $groupLog->save();
        } catch (\Exception $e) {
            $this->_messageManager->addErrorMessage($e->getMessage());
        }
        $this->messageManager->addNoticeMessage(__('Request Denied Successfully'));
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $redirect->setPath('groupbuying/account/request');

    }//end execute()


}//end class
