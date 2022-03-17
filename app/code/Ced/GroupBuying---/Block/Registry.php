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

use _HumbugBoxe8a38a0636f4\phpDocumentor\Reflection\Types\Integer;
use Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory;
use Ced\GroupBuying\Model\Session;
use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Block class for new group modal form.
 * @codingStandardsIgnoreStart
 */
class Registry extends Template //@codingStandardsIgnoreEnd
{

    /**
     * Result page factory
     *
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * Scope config interface
     *
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry|null
     */
    public ?\Magento\Framework\Registry $coreRegistry = null;

    /**
     * Group main table collection
     *
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * Customer session
     *
     * @var SessionFactory
     */
    private SessionFactory $customerSession;

    /**
     * Custom session specific class variable
     *
     * @var Session
     */
    public Session $groupBuyingSession;

    /**
     * @var ProductRepository
     */
    private ProductRepository $productRep;


    /**
     * Constructor
     *
     * @param Context                     $context           Template Context.
     * @param ScopeConfigInterface        $scopeConfig       Scope config.
     * @param \Magento\Framework\Registry $registry          Product price is stored in it.
     * @param CollectionFactory           $collectionFactory Group main table model collection.
     * @param SessionFactory              $customerSession   Customer session.
     * @param Session                     $groupBuySession   Custom session for group buying module.
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $registry,
        CollectionFactory $collectionFactory,
        SessionFactory $customerSession,
        Session $groupBuySession,
        ProductRepository $productRep
    ) {
        $this->_scopeConfig       = $scopeConfig;
        $this->coreRegistry       = $registry;
        $this->collectionFactory  = $collectionFactory;
        $this->customerSession    = $customerSession;
        $this->groupBuyingSession = $groupBuySession;
        $this->productRep         = $productRep;
        parent::__construct($context);

    }//end __construct()


    /**
     * Returns the total groups created by customer
     *
     * @return \phpDocumentor\Reflection\Types\Integer|null
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


    /**
     * Get product object using product ID
     *
     * @param string $id Product ID.
     *
     * @return object
     * @throws NoSuchEntityException Exception on product missing.
     */
    public function getProductById(string $id): object
    {
        return $this->productRep->getById($id);

    }//end getProductById()


}//end class
