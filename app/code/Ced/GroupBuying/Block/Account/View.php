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
use Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory;
use Magento\Catalog\Helper\Output;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Sales order history block
 */
class View extends \Magento\Framework\View\Element\Template
{

    /**
     * @var string
     */
    protected $_template = 'groupbuy/account/view.phtml';

    /**
     * @var \Ced\Groupgift\Model\ResourceModel\Main\CollectionFactory
     */
    protected $_giftCollectionFactory;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var Collection
     */
    protected $gifts;

    /**
     * @var ObjectManagerInterface
     */
    public $_objectManager;

    /**
     * @var ScopeConfigInterface
     */
    public $_scopeConfig;

    /**
     * @var MainFactory
     */
    private $gift;

    /**
     * @var string
     */
    private $endDate;

    /**
     * @var string
     */
    private $startDate;

    /**
     * @var Output
     */
    private $outputHelper;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var MainFactory
     */
    private $groupMainFactory;

    /**
     * @var CollectionFactory
     */
    private $guestCollectionFactory;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ProductRepository
     */
    private $_productRepository;


    /**
     * Constructor
     *
     * @param Context $context
     * @param \Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory $giftCollectionFactory
     * @param MainFactory $groupMainFactory
     * @param CollectionFactory $guestCollectionFactory
     * @param SessionFactory $customerSession
     * @param ObjectManagerInterface $objectManager
     * @param ProductRepository $productRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerFactory $customerFactory
     * @param ProductFactory $productFactory
     * @param Output $outputHelper
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory $giftCollectionFactory,
        MainFactory $groupMainFactory,
        CollectionFactory $guestCollectionFactory,
        SessionFactory $customerSession,
        ObjectManagerInterface $objectManager,
        ProductRepository $productRepository,
        ScopeConfigInterface $scopeConfig,
        CustomerFactory $customerFactory,
        ProductFactory $productFactory,
        Output $outputHelper,
        StoreManagerInterface $storeManager,
        array $data=[]
    ) {
        $this->_giftCollectionFactory = $giftCollectionFactory;
        $this->_customerSession       = $customerSession;
        $this->_objectManager         = $objectManager;
        $this->_productRepository     = $productRepository;
        $this->_scopeConfig           = $scopeConfig;
        parent::__construct($context, $data);
        $this->gift = $groupMainFactory->create()->load($this->getRequest()->getParam('gift_id'));
        $this->setGift($this->gift);
        $this->customerFactory        = $customerFactory;
        $this->guestCollectionFactory = $guestCollectionFactory;
        $this->groupMainFactory       = $groupMainFactory;
        $this->productFactory         = $productFactory;
        $this->outputHelper           = $outputHelper;
        $this->storeManager           = $storeManager;

    }//end __construct()


    /**
     * Get group member name
     *
     * @return string|null
     */
    public function getGuests(): ?string
    {
        $customerId = $this->_customerSession->create()->getCustomerId();
        $customer   = $this->customerFactory->create()->load($customerId);

        $guests = $this->guestCollectionFactory->create()->addFieldToFilter('groupgift_id', $this->getGift()->getId())->addFieldToFilter('guest_email', $customer['email'])->getFirstItem();
        return $guests['guest_name'];

    }//end getGuests()


    /**
     * Get post url
     *
     * @param object $gift
     *
     * @return string
     */
    public function getPostUrl(object $gift): string
    {
        return $this->getUrl('*/*/post', ['gift_id' => $gift->getId()]);

    }//end getPostUrl()


    /**
     * Get back url
     *
     * @return string
     */
    public function getBackUrl(): string
    {
        return $this->getUrl('groupbuying/account/list');

    }//end getBackUrl()


    /**
     * Get images
     *
     * @return Product
     */
    public function getImages()
    {
        $customer_Id = $this->_customerSession->create()->getCustomerId();
        $customer    = $this->customerFactory->create()->load($customer_Id);
        $image       = $this->groupMainFactory->create()->load($this->getGift()->getId());
        $_product    = $this->productFactory->create()->load($image['original_product_id']);
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
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl()
    {
        $image   = $this->gift->getData();
        $product = $this->_productRepository->getById($image['original_product_id']);
        return $product->getUrlModel()->getUrl($product);

    }//end getImageUrl()


    /**
     * Checks if group is full
     *
     * @return boolean
     */
    public function isGroupFull(): bool
    {
        $groupId                = $this->getGift()->getId();
        $guestCollectionFactory = $this->guestCollectionFactory->create();
        $guestExist             = $guestCollectionFactory->addFieldToFilter('guest_email', $this->_customerSession->create()->getCustomer()->getEmail())->getFirstItem();

        if ($guestExist === null) {
            $groupSize        = $this->groupMainFactory->create()->load($groupId)->getGroupSize();
            $groupMemberCount = $guestCollectionFactory->addFieldToFilter('groupgift_id', $groupId)->addFieldToFilter('request_approval', 2)->count();

            return !($groupSize <= $groupMemberCount);
        }

        return false;

    }//end isGroupFull()


    /**
     * Checks if current date is grater than last group join date
     *
     * @return integer
     */
    public function isJoiningDatePassed(): int
    {
        $groupId          = $this->getGift()->getId();
        $groupMainFactory = $this->groupMainFactory->create()->load($groupId);
        $this->startDate  = strtotime($groupMainFactory->getStartDate());
        $this->endDate    = strtotime($groupMainFactory->getEndDate());
        $currentDate      = strtotime(date('Y-m-d'));
        if ($this->startDate > $currentDate) {
            return -1;
        } else if ($this->endDate < $currentDate) {
            return 0;
        } else if ($this->startDate <= $currentDate && $this->endDate >= $currentDate) {
            return true;
        } else {
            return -2;
        }

    }//end isJoiningDatePassed()


    /**
     * Get group start date
     *
     * @return string
     */
    public function getStartDate(): string
    {
        return $this->startDate;

    }//end getStartDate()


    /**
     * Get group join last date
     *
     * @return string
     */
    public function getEndDate():string
    {
        return $this->endDate;

    }//end getEndDate()

    /**
     * @return mixed
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }


}//end class
