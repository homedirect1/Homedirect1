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

use Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\ResultFactory;
use Ced\GroupBuying\Api\MainRepositoryInterface;
use Ced\GroupBuying\Helper\Data;

class Post extends Action
{

    protected $_coreRegistry = null;

    protected $urlBuilder;
    private $request;
    private $_giftCollectionFactory;
    private $_customerSession;
    private $customerFactory;
    private $mainGroup;
    private $groupBuyingHelper;


    /**
     * TODO
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param Registry $registry
     * @param Session $customerSession
     * @param CollectionFactory $giftCollectionFactory
     * @param CustomerFactory $customerFactory
     * @param ManagerInterface $messageManager
     * @param UrlInterface $urlBuilder
     * @param ResultFactory $resultFactory
     * @param Http $request
     * @param MainRepositoryInterface $mainGroup
     * @param Data $groupBuyingHelper
     */
    public function __construct(
        Context         $context,
        ObjectManagerInterface $objectManager,
        Registry $registry,
        Session $customerSession,
        CollectionFactory $giftCollectionFactory,
        CustomerFactory $customerFactory,
        ManagerInterface $messageManager,
        UrlInterface $urlBuilder,
        ResultFactory        $resultFactory,
        Http $request,
        MainRepositoryInterface $mainGroup,
        Data $groupBuyingHelper
    ) {
        $this->_objectManager         = $objectManager;
        $this->_coreRegistry          = $registry;
        $this->_customerSession       = $customerSession;
        $this->_giftCollectionFactory = $giftCollectionFactory;
        $this->messageManager        = $messageManager;
        $this->urlBuilder             = $urlBuilder;

        $this->customerFactory = $customerFactory;

        $this->resultFactory          = $resultFactory;
        $this->request = $request;
        $this->mainGroup = $mainGroup;
        $this->groupBuyingHelper = $groupBuyingHelper;
        parent::__construct($context);

    }//end __construct()


    /**
     * TODO
     *
     * @return ResponseInterface|ResultInterface|null
     */
    public function execute()
    {
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $gift_id    = $this->request->getParam('gift_id');
        $postValues = $this->request->getPostValue();
        $customerId = $this->_customerSession->getCustomerId();
        $customer   = $this->customerFactory->create()->load($customerId);
        $productId = (int)$this->mainGroup->getById($gift_id)->getData("original_product_id");
        $gift       = $this->_giftCollectionFactory->create()->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'guest_email',
            $customer['email']
        )->addFieldToFilter(
            'groupgift_id',
            $gift_id
        )->getFirstItem();
        
        try {

            $gift->setData('quantity', $postValues['qty']);

            $gift->save();

            $totalQuantityPurchased = (int)$this->groupBuyingHelper->getTotalQuantityPurchasedByGroup($gift_id)["total_purchase_quantity"];
            $minPurchase = (int)$this->groupBuyingHelper->getProductTierPrice($productId);

            if($minPurchase <= $totalQuantityPurchased){
                $gift->setData('request_approval', 4);                
            }else{
                $gift->setData('request_approval', 3);
            }
            $gift->save();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->messageManager->addSuccessMessage(__('Request Updated Successfully'));
        return $redirect->setPath('groupbuying/account/request');

    }//end execute()


}//end class
