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

namespace Ced\GroupBuying\Block\Account;

use Ced\GroupBuying\Model\MainFactory;
use Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Theme\Block\Html\Pager;
use Magento\Framework\Controller\ResultFactory;


/**
 * Sales order history block
 */
class ListGroups extends Template
{

    /**
     * @var string
     */
    protected $_template = 'groupbuy/account/groupList.phtml';

    /**
     * @var \Ced\Groupgift\Model\ResourceModel\Main\CollectionFactory
     */
    protected $_giftCollectionFactory;

    /**
     * @var SessionFactory
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $gifts;

    public $_objectManager;

    private \Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory $groupCollectionFactory;

    private MainFactory $mainFactory;

    private ResultFactory $resultFactory;


    /**
     * @param Context                                                      $context
     * @param CollectionFactory                                            $giftCollectionFactory
     * @param \Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory $groupCollectionFactory
     * @param MainFactory                                                  $mainFactory
     * @param SessionFactory                                               $customerSession
     * @param ObjectManagerInterface                                       $objectManager
     * @param array                                                        $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $giftCollectionFactory,
        \Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory $groupCollectionFactory,
        MainFactory $mainFactory,
        SessionFactory $customerSession,
        ObjectManagerInterface $objectManager,
        ResultFactory        $resultFactory,
        array $data=[]
    ) {
        $this->_giftCollectionFactory = $giftCollectionFactory;
        $this->_customerSession       = $customerSession;
        $this->_objectManager         = $objectManager;

        $this->resultFactory          = $resultFactory;
        $this->mainFactory            = $mainFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        parent::__construct($context, $data);

    }//end __construct()


    /**
     * Set title
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Group Buying'));

    }//end _construct()


    /**
     * Returns false if customer is not logged in or returns
     *
     * @return CollectionFactory|ResultFactory
     */
    public function getOrders()
    {
        try{
            $customer = $this->_customerSession->create();
            if ($this->gifts === null && $customer->isLoggedIn() === true) {
                $this->gifts = $this->_giftCollectionFactory->create()->addFieldToSelect(
                    '*'
                )->addFieldToFilter(
                    'owner_customer_id',
                    $customer->getId()
                )->setOrder(
                    'group_id',
                    'asc'
                );
            }
        }catch(\Exception $ex){
            return $this->gifts;
        }

        return $this->gifts;

    }//end getOrders()


    /**
     * Prepare layout
     *
     * @return $this
     *
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getOrders()) {
            $pager = $this->getLayout()->createBlock(
                Pager::class,
                'sales.order.history.pager'
            )->setLimit(10)->setCollection(
                $this->getOrders()
            );

            $this->setChild('pager', $pager);
            $this->getOrders()->load();
        }

        return $this;

    }//end _prepareLayout()


    /**
     * Get page html
     *
     * @return string
     */
    public function getPagerHtml(): string
    {
        return $this->getChildHtml('pager');

    }//end getPagerHtml()


    /**
     * Get group view controller path
     *
     * @param object $gift Group object.
     *
     * @return string
     */
    public function getViewUrl(object $gift): string
    {
        return $this->getUrl('*/account/grpview', ['gid' => $gift->getId()]);

    }//end getViewUrl()


    /**
     * Get edit controller path
     *
     * @param object $gift Group object.
     *
     * @return string
     */
    public function getEditUrl(object $gift): string
    {
        return $this->getUrl('*/*/view', ['gift_id' => $gift->getId()]);

    }//end getEditUrl()


    /**
     * Get previous page URL
     *
     * @return string
     */
    public function getBackUrl(): string
    {
        return $this->getUrl('customer/account/');

    }//end getBackUrl()


    /**
     * Checks if group is approved or not
     *
     * @param integer $groupID Group ID.
     *
     * @return string
     */
    public function getGroupApprovalStatus(int $groupID): string
    {
        return $this->mainFactory->create()->load($groupID)->getIsApprove();

    }//end getGroupApprovalStatus()


    /**
     * Returns total members in a group
     *
     * @param integer $groupId Group ID.
     *
     * @return integer
     */
    public function getTotalMembers(int $groupId): int
    {
        $groupCollectionFactory = $this->groupCollectionFactory->create();
        return $groupCollectionFactory->addFieldToFilter('groupgift_id', $groupId)->addFieldToFilter(
            ['request_approval', 'request_approval', 'request_approval', 'request_approval'], 
            [
                /* Requirement fulfilled */['eq' =>4], 
                /* Group joined but didn't buy */['eq' =>2],
                /* Group joined but didn't buy */['eq' =>3],  
                /* Group join pending */ ['eq' => 0]
            ]
        )->count();

    }//end getTotalMembers()


}//end class
