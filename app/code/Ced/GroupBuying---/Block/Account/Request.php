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

use Ced\GroupBuying\Model\Guest;
use Ced\GroupBuying\Model\Main;
use Ced\GroupBuying\Model\MainFactory;
use Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Theme\Block\Html\Pager;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Sales order history block
 */
class Request extends Template
{

    /**
     * @var string
     */
    protected $_template = 'groupbuy/account/request.phtml';

    /**
     * @var \Ced\Groupgift\Model\ResourceModel\Main\CollectionFactory
     */
    protected $_giftCollectionFactory;

    /**
     * @var SessionFactory
     */
    protected $_customerSession;

    /**
     * @var Collection
     */
    protected $gifts;

    protected $last;

    public $_objectManager;

    private $customerFactory, $mainFactory;

    /**
     * Product Repository variable
     *
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;


    /**
     * @param Context                                                     $context
     * @param CollectionFactory                                           $giftCollectionFactory
     * @param \Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory $lastCollectionFactory
     * @param MainFactory                                                 $mainFactory
     * @param SessionFactory                                              $customerSession
     * @param ObjectManagerInterface                                      $objectManager
     * @param CustomerFactory                                             $customerFactory
     * @param array                                                       $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $giftCollectionFactory,
        \Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory $lastCollectionFactory,
        MainFactory $mainFactory,
        SessionFactory $customerSession,
        ObjectManagerInterface $objectManager,
        CustomerFactory $customerFactory,
        ProductRepositoryInterface $productRepository,
        array $data=[]
    ) {
        $this->_giftCollectionFactory = $giftCollectionFactory;
        $this->_lastCollectionFactory = $lastCollectionFactory;
        $this->_customerSession       = $customerSession;
        $this->_objectManager         = $objectManager;

        $this->customerFactory = $customerFactory;
        $this->mainFactory     = $mainFactory;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);

    }//end __construct()


    /**
     * Set page title
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Group Request'));

    }//end _construct()


    /**
     * Get Orders
     *
     * @return object
     */
    public function getOrders()
    {
        $customer = $this->_customerSession->create();
        try{
            if ($this->gifts === null && $customer->isLoggedIn() === true) {
                $customer    = $this->customerFactory->create()->load($customer->getId());
                $this->gifts = $this->_giftCollectionFactory->create()->addFieldToSelect(
                    '*'
                )->addFieldToFilter(
                    'guest_email',
                    $customer['email']
                )->setOrder(
                    'id',
                    'asc'
                );
            }
        }catch (\Exception $ex){
            return $this->gifts;
        }
        return $this->gifts;

    }//end getOrders()


    /**
     * Get last orders
     *
     * @param Main $gift
     *
     * @return \Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory
     */
    public function getlastOrders(Guest $gift)
    {
        $customer = $this->_customerSession->create();

        if ($this->last === null && $customer->isLoggedIn() === true) {
            $customer   = $this->customerFactory->create()->load($customer->getId());
            $this->last = $this->_lastCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'group_id',
                $gift['groupgift_id']
            );
        }
        return $this->last;

    }//end getlastOrders()


    /**
     * Get last one order
     *
     * @param integer $id Group ID.
     *
     * @return Main
     */
    public function getlastoneOrders(int $id)
    {
        $sessionCustomer = $this->_customerSession->create();

        if ($this->last === null && $sessionCustomer->isLoggedIn() === true) {
            $customer = $this->customerFactory->create()->load($sessionCustomer->getId());
            return $this->mainFactory->create()->load($id);
        }
        return $this->last->getFirstItem();

    }//end getlastoneOrders()


    /**
     * Prepare Layout
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
     * Pager Html
     *
     * @return string
     */
    public function getPagerHtml(): string
    {
        return $this->getChildHtml('pager');

    }//end getPagerHtml()


    /**
     * Get deny controller url
     *
     * @param object $gift
     *
     * @return string
     */
    public function getViewUrl(object $gift): string
    {
        return $this->getUrl('*/*/deny', ['gid' => $gift['groupgift_id']]);

    }//end getViewUrl()


    /**
     * Get approve controller url
     *
     * @param object $gift
     *
     * @return string
     */
    public function getEditUrl($gift)
    {
        return $this->getUrl('*/*/approve', ['gift_id' => $gift['groupgift_id']]);

    }//end getEditUrl()


    /**
     * Get purchase url
     *
     * @param object $gift
     *
     * @return string
     */
    public function getPurchaseUrl(object $gift)
    {
        return $this->getUrl('*/*/view', ['gift_id' => $gift['groupgift_id']]);

    }//end getPurchaseUrl()


    /**
     * Get previous page url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');

    }//end getBackUrl()


    /**
     * Get order controller url
     *
     * @param object $gift
     *
     * @return string
     */
    public function getPaid(object $gift): string
    {
        return $this->getUrl('*/*/order', ['gift_id' => $gift['groupgift_id']]);

    }//end getPaid()


    /**
     * No idea
     *
     * @param object $gift
     *
     * @return integer
     */
    public function customized(object $gift): int
    {
        $group = $this->_giftCollectionFactory->create()->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'groupgift_id',
            $gift['groupgift_id']
        );

        foreach ($group->getData() as $key => $value) {
            if (($value['quantity'] == 0) && ($value['request_approval'] != 1)) {
                return 0;
            }
        }

        return 1;

    }//end customized()


    /**
     * Guest user
     *
     * @param object $gift
     *
     * @return \Ced\GroupBuying\Model\ResourceModel\Guest\Collection
     */
    public function guestuser(object $gift): \Ced\GroupBuying\Model\ResourceModel\Guest\Collection
    {
        return $this->_giftCollectionFactory->create()->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'groupgift_id',
            $gift['groupgift_id']
        );

    }//end guestuser()

    /**
     * Returns tier price
     *
     * @param int $productId
     *
     * @return mixed
     */
    public function getProductTierPrice(int $productId){
        $product = $this->productRepository->getById($productId);
        return $product->getTierPrice();
    }

    /**
     * Get total locked/purchased quantity
     *
     * @param int $groupId Main Group ID.
     *
     * @return mixed
     */
    public function getTotalQuantityPurchasedByGroup(int $groupId){
        $group = $this->_giftCollectionFactory->create()->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'groupgift_id',
            $groupId
        )->addExpressionFieldToSelect('total_purchase_quantity', 'SUM({{quantity}})', 'quantity');

        return $group->getFirstItem();
    }


}//end class
