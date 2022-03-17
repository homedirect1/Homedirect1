<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Model\Api\Affiliate;

/**
 * Class ReferralPayout
 * @package Ced\Affiliate\Model\Api\Affiliate
 */
class ReferralPayout extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\Transaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\Coupon\CollectionFactory
     */
    protected $couponCollectionFactory;

    /**
     * ReferralPayout constructor.
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Ced\Affiliate\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Ced\Affiliate\Model\ResourceModel\Coupon\CollectionFactory $couponCollectionFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Ced\Affiliate\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ced\Affiliate\Model\ResourceModel\Coupon\CollectionFactory $couponCollectionFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->affiliateHelper = $affiliateHelper;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->couponCollectionFactory = $couponCollectionFactory;
    }

    /**
     * @param $customerId
     * @return array
     */
    public function getReferralPayoutInformation($customerId)
    {

        $affiliateData = [];
        $affiliate = $this->affiliateAccountFactory->create()->load($customerId, 'customer_id');
        $affiliateId = $affiliate->getAffiliateId();

        $affiliateData['earned_amount'] = $this->affiliateHelper->getFormattedPrice($this->getEarnedamount($customerId));
        $affiliateData['payout_discoupon'] = $this->getOptionList($this->getEarnedamount($customerId));
        $affiliateData['coupon_collection'] = $this->getCouponCollection($customerId);
        return ["data" => $affiliateData];
    }

    /**
     * @param $customer_Id
     * @return int
     */
    protected function getEarnedamount($customer_Id)
    {
        $amount = 0;
        $referred_list = $this->transactionCollectionFactory->create()->addFieldtoFilter('customer_id', [
            'customer_id' => $customer_Id
        ]);
        foreach ($referred_list as $value) {
            $amount += $value['earned_amount'];
        }
        return $amount;
    }

    /**
     * @param $earnedAmount
     * @return array
     */
    protected function getOptionList($earnedAmount)
    {
        $options = [];
        $amount = $this->scopeConfig->getValue('affiliate/referfriend/referral_reward_denomination_range');
        if (!$amount)
            $amount = 1;

        $gap = intval($earnedAmount / $amount);
        for ($i = 1; $gap >= $i; $i++) {
            $options[] = $amount * $i;
        }
        return $options;
    }

    /**
     * @param $customer_Id
     * @return mixed
     */
    protected function getCouponCollection($customer_Id)
    {

        $coupons = $this->couponCollectionFactory->create()->addFieldtoFilter('customer_id', [
            'customer_id' => $customer_Id
        ]);
        return $coupons->getData();
    }
}
