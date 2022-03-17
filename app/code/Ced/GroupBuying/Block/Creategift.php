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

namespace Ced\GroupBuying\Block;
use Ced\GroupBuying\Model\Session;
use Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Creategift extends Template
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $_objectManager;

    protected $session;

    public $_scopeConfig;

    public $_coreRegistry = null;

    private $productFactory, $customerSession, $collectionFactory;

    public $groupBuyingSession;


    /**
     * @param Context                     $context
     * @param \Magento\Framework\Registry $registry
     * @param ProductFactory              $productFactory
     * @param SessionFactory              $sessionFactory
     * @param CollectionFactory           $collectionFactory
     * @param Session                     $groupBuyingSession
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        ProductFactory $productFactory,
        SessionFactory $sessionFactory,
        CollectionFactory $collectionFactory,
        Session $groupBuyingSession
    ) {
        $this->_coreRegistry      = $registry;
        $this->productFactory     = $productFactory;
        $this->customerSession    = $sessionFactory;
        $this->collectionFactory  = $collectionFactory;
        $this->groupBuyingSession = $groupBuyingSession;
        parent::__construct($context);

    }//end __construct()


    /**
     * Checks if the product has group buying or not
     *
     * @return boolean
     */
    public function isGrouped(): bool
    {
        $id      = $this->_coreRegistry->registry('current_product')->getId();
        $product = $this->productFactory->create()->load($id);
        // @codingStandardsIgnoreStart
        return (bool)$product->getGroupBuy();
        // @codingStandardsIgnoreEnd

    }//end isGrouped()


    /**
     * Check if the customer is logged-in or not
     *
     * @return boolean
     */
    public function isLogin(): bool
    {
        // @codingStandardsIgnoreStart
        return $this->customerSession->create()->isLoggedIn() === true;
        // @codingStandardsIgnoreEnd

    }//end isLogin()


    /**
     * Get current product ID
     *
     * @return integer
     */
    public function getId(): int
    {
        // @codingStandardsIgnoreStart
        return $this->_coreRegistry->registry('current_product')->getId();
        // @codingStandardsIgnoreEnd

    }//end getId()


    /**
     * Checks if current user already created a group for the product
     *
     * @return boolean
     */
    public function isGroupCreated(): bool
    {
        $customerSession = $this->customerSession->create();
        if ($customerSession->isLoggedIn() === true) {
            $groupCollectionFactory = $this->collectionFactory->create();
            // @codingStandardsIgnoreStart
            $customerGroupCount = $groupCollectionFactory->addFieldToFilter('owner_customer_id', $customerSession->getCustomer()->getId())->addFieldToFilter('original_product_id', $this->getId())->count();
            // @codingStandardsIgnoreEnd
            return $customerGroupCount > 0;
        }

        return false;

    }//end isGroupCreated()


    /**
     * Returns the total groups created by customer
     *
     * @return integer|null
     */
    public function getCustomerGroupCount(): ?int
    {
        $customerSession = $this->customerSession->create();

        if ($customerSession->isLoggedIn() === true) {
            $customerId              = $customerSession->getCustomer()->getId();
            $customerGroupCollection = $this->collectionFactory->create();
            //@codingStandardsIgnoreStart
            return $customerGroupCollection->addFieldToFilter('owner_customer_id', $customerId)->count();
            // @codingStandardsIgnoreEnd
        }

        return null;

    }//end getCustomerGroupCount()


    /**
     * Get the email of currently logged in customer
     *
     * @return string
     */
    public function getCustomerEmail(): string
    {
        $customerSession = $this->customerSession->create();
        //@codingStandardsIgnoreStart
        return $customerSession->getCustomer()->getEmail();
        // @codingStandardsIgnoreEnd

    }//end getCustomerEmail()


}//end class
