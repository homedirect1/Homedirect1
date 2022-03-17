<?php //@codingStandardsIgnoreStart
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
//@codingStandardsIgnoreEnd
namespace Ced\GroupBuying\Block;

use Ced\GroupBuying\Model\Main;
use Ced\GroupBuying\Model\MainFactory;
use Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory;
use Ced\GroupBuying\Model\ResourceModel\Main\Collection;
use Magento\Catalog\Model\Session;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Element\Template;

class ExistingGroupList extends Template //@codingStandardsIgnoreLine
{

    /**
     * @var SessionFactory
     */
    private $sessionFactory;

    /**
     * @var MainFactory
     */
    private $mainFactory;

    /**
     * @var CollectionFactory
     */
    private $guestCollectionFactory;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory 
     */
    private $groupCollectionFactory;
    /**
     * @var \Ced\GroupBuying\Helper\Data
     */
    private $helper;


    /**
     * @param Template\Context                                            $context
     * @param \Magento\Framework\Registry                                 $registry
     * @param \Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory $groupCollectionFactory
     * @param CustomerFactory                                             $customerFactory
     * @param MainFactory                                                 $mainFactory
     * @param CollectionFactory                                           $guestCollectionFactory
     * @param SessionFactory                                              $sessionFactory
     * @param array                                                       $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory $groupCollectionFactory,
        CustomerFactory $customerFactory,
        MainFactory $mainFactory,
        CollectionFactory $guestCollectionFactory,
        SessionFactory $sessionFactory,
        //Inject group buying helper
        \Ced\GroupBuying\Helper\Data $groupBuyingHelper,
        array $data=[]
    ) {
        parent::__construct(
            $context,
            $data
        );

        $this->registry               = $registry;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->customerFactory        = $customerFactory;

        $this->guestCollectionFactory = $guestCollectionFactory;
        $this->mainFactory            = $mainFactory;
        $this->sessionFactory         = $sessionFactory;
        $this->helper = $groupBuyingHelper;

    }//end __construct()


    /**
     * Get the list of approved groups
     *
     * @return Collection
     */
    public function getGroupList(): Collection
    {
        $productId              = $this->registry->registry('current_product')->getId();
        $groupCollectionFactory = $this->groupCollectionFactory->create();
        return $groupCollectionFactory->addFieldToFilter('is_approve', 1)->addFieldToFilter('original_product_id', $productId); //@codingStandardsIgnoreLine

    }//end getGroupList()


    /**
     * Returns the full name of the group admin
     *
     * @param integer $customerId Customer ID.
     *
     * @return       string
     * @noinspection PhpUndefinedMethodInspection
     */
    public function getGroupAdminName(int $customerId): string
    {
        $customer = $this->customerFactory->create()->load($customerId);
        return $customer->getFirstname().' '.$customer->getLastname(); //@codingStandardsIgnoreLine

    }//end getGroupAdminName()


    /**
     * Returns group vacancy for particular group
     *
     * @param integer $groupId Group ID.
     *
     * @return integer
     */
    public function getGroupVacancy(int $groupId): int
    {
        $groupSize                    = $this->mainFactory->create()->load($groupId)->getGroupSize();
        $groupMemberCollectionFactory = $this->guestCollectionFactory->create();
        $totalGroupMembers             = $groupMemberCollectionFactory->addFieldToFilter('groupgift_id', $groupId)->addFieldToFilter('request_approval', 2)->count(); //@codingStandardsIgnoreLine
        return ($groupSize - $totalGroupMembers);

    }//end getGroupVacancy()


    /**
     * Get currently logged in customer ID
     *
     * @return mixed
     */
    public function getCurrentCustomerId()
    {
        return $this->sessionFactory->create()->getCustomer()->getId(); //@codingStandardsIgnoreLine

    }//end getCurrentCustomerId()


    /**
     * Checks if customer already joined the group
     *
     * @param integer $groupId Group ID.
     *
     * @return boolean
     */
    public function isCustomerAlreadyJoined(int $groupId): bool
    {
        $currentUserEmail = $this->sessionFactory->create()->getCustomer()->getEmail();
        $groupCount       = $this->guestCollectionFactory->create()->addFieldToFilter('groupgift_id', $groupId)->addFieldToFilter('guest_email', $currentUserEmail)->addFieldToFilter( //@codingStandardsIgnoreLine
            [
                'request_approval',
                'request_approval',
                'request_approval'
            ],
            [
                ['neq' => 1],
                ['neq' => 0],
                ['eq' => null]
            ]
        )->count();
        return $groupCount > 0;

    }//end isCustomerAlreadyJoined()


    /**
     * Get URL for approve controller
     *
     * @param Main $group Group table model object.
     *
     * @return string
     */
    public function getApproveUrl(Main $group): string
    {
        // @codingStandardsIgnoreStart
        return $this->getUrl('groupbuying/account/approve', ['gift_id' => $group->getId()]);
        // @codingStandardsIgnoreEnd

    }//end getApproveUrl()


    /**
     * Get URL for view controller
     *
     * @param Main $group Group table model object.
     *
     * @return string
     */
    public function getViewUrl(Main $group): string
    {
        //@codingStandardsIgnoreStart
        return $this->getUrl('groupbuying/account/grpview', ['gid' => $group->getId()]);
        //@codingStandardsIgnoreEnd

    }//end getViewUrl()


    /**
     * Checks if customer is logged in or not
     *
     * @return boolean
     */
    public function isLoggedIn(): bool
    {
        return $this->sessionFactory->create()->isLoggedIn(); //@codingStandardsIgnoreLine

    }//end isLoggedIn()
    
    //Getter for helper
    public function getHelper()
    {
        return $this->helper;
    }


}//end class
