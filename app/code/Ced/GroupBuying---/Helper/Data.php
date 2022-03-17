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

namespace Ced\GroupBuying\Helper;

use Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const CONFIG_GROUP_SIZE              = 'groupbuyingsection/general/groupSize';
    const CONFIG_GROUP_INFO              = 'groupbuyingsection/general/groupInfo';
    const CONFIG_GROUP_APPROVAL_STATUS   = 'groupbuyingsection/general/groupApproval';
    const CONFIG_GROUP_MASS_EMAIL_STATUS = 'groupbuyingsection/emailConfig/groupMassEmail';
    const CONFIG_GROUP_EMAIL_TEMPLATE    = 'groupbuyingsection/emailConfig/emailTemplate';

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    private $guestCollectionFactory, $mainFactory;

    /**
     * @var \Ced\Groupgift\Model\ResourceModel\Main\CollectionFactory
     */
    protected $_giftCollectionFactory;

    /**
     * Product Repository variable
     *
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;


    /**
     * TODO
     *
     * @param \Magento\Framework\App\Helper\Context                        $context
     * @param \Ced\GroupBuying\Model\MainFactory                           $mainFactory
     * @param \Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory $guestCollectionFactory
     * @param CollectionFactory                                           $giftCollectionFactory
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Ced\GroupBuying\Model\MainFactory $mainFactory,
        \Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory $guestCollectionFactory,
        CollectionFactory $giftCollectionFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->guestCollectionFactory = $guestCollectionFactory;
        $this->mainFactory            = $mainFactory;

        $this->_giftCollectionFactory = $giftCollectionFactory;
        $this->productRepository = $productRepository;

        parent::__construct($context);

    }//end __construct()


    /**
     * TODO
     *
     * @param  string $config_path
     * @return mixed
     */
    public function getConfig(string $config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

    }//end getConfig()


    /**
     * TODO
     *
     * @param  integer $groupId
     * @return integer
     */
    public function getGroupVacancy(int $groupId): int
    {
        $groupSize         = $this->mainFactory->create()->load($groupId)->getGroupSize();
        $totalGroupMembers = $this->guestCollectionFactory->create()->addFieldToFilter('groupgift_id', $groupId)->addFieldToFilter('request_approval', 2)->count();
        return ($groupSize - $totalGroupMembers);

    }//end getGroupVacancy()

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


}//end class
