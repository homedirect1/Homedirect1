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

use Ced\GroupBuying\Api\MainRepositoryInterface;
use Ced\GroupBuying\Model\GuestFactory;
use Ced\GroupBuying\Model\MainFactory;
use Ced\GroupBuying\Model\ResourceModel\Guest\Collection;
use Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory;
use Magento\Catalog\Helper\Output;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * Sales order history block
 */
class Grpview extends \Magento\Framework\View\Element\Template
{

    /**
     * @var string
     */
    protected $_template = 'groupbuy/account/grpview.phtml';

    /**
     * @var \Ced\Groupgift\Model\ResourceModel\Main\CollectionFactory
     */
    protected $_giftCollectionFactory;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $gifts;

    public $_scopeConfig;

    public $_objectManager;

    private $guestFactory, $productFactory;
    private Output $outputHelper;
    private CustomerFactory $customerFactory;
    private CollectionFactory $guestCollectionFactory;


    /**
     * Constructor
     *
     * @param Context $context
     * @param CollectionFactory $guestCollectionFactory
     * @param \Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory $giftCollectionFactory
     * @param MainRepositoryInterface $mainRepository
     * @param GuestFactory $guestFactory
     * @param ObjectManagerInterface $objectManager
     * @param Session $customerSession
     * @param ProductRepository $productRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerFactory $customerFactory
     * @param ProductFactory $productFactory
     * @param Output $outputHelper
     * @param array $data
     * @throws NoSuchEntityException
     */
    public function __construct(
        Context $context,
        CollectionFactory $guestCollectionFactory,
        \Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory $giftCollectionFactory,
        MainRepositoryInterface $mainRepository,
        GuestFactory $guestFactory,
        ObjectManagerInterface $objectManager,
        Session $customerSession,
        ProductRepository $productRepository,
        ScopeConfigInterface $scopeConfig,
        CustomerFactory $customerFactory,
        ProductFactory $productFactory,
        Output $outputHelper,
        array $data=[]
    ) {
        $this->_giftCollectionFactory = $giftCollectionFactory;
        $this->_customerSession       = $customerSession;
        $this->productRepository     = $productRepository;
        $this->_scopeConfig           = $scopeConfig;
        $this->_objectManager         = $objectManager;
        parent::__construct($context, $data);
        $gift = $mainRepository->getById($this->getRequest()->getParam('gid'));
        $this->mainRepository = $mainRepository;
        $this->setGift($gift);
        $this->guestCollectionFactory = $guestCollectionFactory;
        $this->customerFactory        = $customerFactory;
        $this->guestFactory           = $guestFactory;
        $this->productFactory         = $productFactory;
        $this->outputHelper           = $outputHelper;

    }//end __construct()


    /**
     * Get guest name
     *
     * @return string
     */
    public function getGuests(): string
    {
        $customerId = $this->_customerSession->getCustomerId();
        $customer   = $this->customerFactory->create()->load($customerId);
        $guests     = $this->guestCollectionFactory->create()->addFieldToFilter('groupgift_id', $this->getGift()->getId())->addFieldToFilter(
            'guest_email',
            $customer['email']
        )->getFirstItem();
        return $guests['guest_name'];

    }//end getGuests()


    /**
     * Get post url
     *
     * @param object $gift
     *
     * @return string
     */
    public function getPostUrl(object $gift)
    {
        return $this->getUrl('*/*/post', ['gift_id' => $gift->getId()]);

    }//end getPostUrl()


    /**
     * Get back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('groupbuying/account/list');

    }//end getBackUrl()


    /**
     * Get images
     *
     * @return Product
     */
    public function getImages(): Product
    {
        $customer_Id = $this->_customerSession->getCustomerId();
        $customer    = $this->customerFactory->create()->load($customer_Id);
        $image       = $this->mainRepository->getById($this->getGift()->getId());
        $_product    = $this->productRepository->getById($image['original_product_id']);
        return $_product;

    }//end getImages()


    /**
     * Get helper
     *
     * @return Output
     */
    public function gethelper()
    {
        return $this->outputHelper;

    }//end gethelper()


    /**
     * Get image url
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getImageUrl(): string
    {
        $image   = $this->mainRepository->getById($this->getGift()->getId());
        $product = $this->productRepository->getById($image['original_product_id']);
        return $product->getUrlModel()->getUrl($product);

    }//end getImageUrl()


    /**
     * Get all group member
     *
     * @return Collection
     */
    public function getAllGuest(): Collection
    {
        return $this->guestCollectionFactory->create()->addFieldToFilter('groupgift_id', $this->getGift()->getId());

    }//end getAllGuest()


}//end class
