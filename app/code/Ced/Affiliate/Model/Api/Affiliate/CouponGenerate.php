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
 * Class CouponGenerate
 * @package Ced\Affiliate\Model\Api\Affiliate
 */
class CouponGenerate implements \Ced\Affiliate\Api\Affiliate\CouponGenerateInterface
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\DiscountDenomination\CollectionFactory
     */
    protected $discDenominationCollectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var \Ced\Affiliate\Model\CouponFactory
     */
    protected $couponFactory;

    /**
     * @var \Ced\Affiliate\Model\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\Transaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * CouponGenerate constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Ced\Affiliate\Model\ResourceModel\DiscountDenomination\CollectionFactory $discDenominationCollectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory
     * @param \Ced\Affiliate\Model\CouponFactory $couponFactory
     * @param \Ced\Affiliate\Model\TransactionFactory $transactionFactory
     * @param \Ced\Affiliate\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Ced\Affiliate\Model\ResourceModel\DiscountDenomination\CollectionFactory $discDenominationCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory,
        \Ced\Affiliate\Model\CouponFactory $couponFactory,
        \Ced\Affiliate\Model\TransactionFactory $transactionFactory,
        \Ced\Affiliate\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
    )
    {
        $this->_date = $date;
        $this->_logger = $logger;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->discDenominationCollectionFactory = $discDenominationCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->ruleFactory = $ruleFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->couponFactory = $couponFactory;
        $this->transactionFactory = $transactionFactory;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
    }

    /**
     * @param $parameters
     * @return array|string
     */
    public function generateCoupon($parameters)
    {
        $this->_logger->critical(json_encode($parameters));

        if (!isset($parameters['customerId']) && !$parameters['customerId']) {
            $affiliateData['error'] = true;
            $affiliateData['error_message'] = __('No Customer Id');
            return ['data' => $affiliateData];
        }

        $amount = $parameters["discount_coupon"];
        $available = $this->pendingamount($parameters);
        if ($amount > $available) {
            $affiliateData['error'] = true;
            $affiliateData['error_message'] = __('You have insufficient balance');
            return ['data' => $affiliateData];

        }
        $affiliateObject = $this->affiliateAccountFactory->create()->load($parameters['customerId'], 'customer_id');
        $customer_Id = $customer->getCustomerId();
        $customer_Email = $affiliateObject->getCustomerEmail();
        $discountType = "cart_fixed";
        $discountAmount = $amount;
        $percoupon = 1;
        $percustomer = 1;
        $email = $customer_Email;

        $minpurchase = 0;
        $cart_amount_check_rule = $this->discDenominationCollectionFactory->create()
            ->addFieldToFilter('status', 0)->addFieldToFilter('discount_amount', $amount)->getData();

        foreach ($cart_amount_check_rule as $key => $value) {
            $minpurchase = $value['cart_amount'];
        }
        $expireDays = $this->scopeConfig->getValue('affiliate/referfriend/discount_code_expiration_days');
        if (!isset($expireDays)) {
            $expireDays = 0;
        }
        $today = date_create($this->_date->date('Y-m-d H:i:s'));

        $next = date_format(date_add($today,
            date_interval_create_from_date_string($expireDays . " days")), "Y-m-d H:i:s");

        $couponlength = 8;
        $promo_name = 'Discount coupon code for ' . $email;
        $uniqueId = $this->generatePromoCode($couponlength);
        $rule = $this->ruleFactory->create();
        $rule->setName($promo_name);
        $rule->setDescription('Generated automatically for the Discount coupon code');
        $rule->setCouponCode($uniqueId);
        $rule->setFromDate($this->_date->date('Y-m-d H:i:s'));
        $rule->setToDate($next);
        $rule->setUsesPerCoupon($percoupon);
        $rule->setUsesPerCustomer($percustomer);
        $customerGroups = $this->groupCollectionFactory->create();
        $groups = array();
        foreach ($customerGroups as $group) {
            $groups[] = $group->getId();
        }

        $conditions =
            [
                '1' =>
                    [
                        'type' => 'Magento\SalesRule\Model\Rule\Condition\Combine',
                        'aggregator' => 'all',
                        'value' => '1',
                        'new_child' => ''
                    ],

                '1--1' =>
                    [
                        'type' => 'Magento\SalesRule\Model\Rule\Condition\Address',
                        'attribute' => 'base_subtotal',
                        'operator' => '>=',
                        'value' => $minpurchase
                    ]

            ];
        $rule->setData('conditions', $conditions);
        $rule->setCustomerGroupIds($groups);
        $rule->setIsActive(true);
        $rule->setStopRulesProcessing(true);
        $rule->setIsRss(false);
        $rule->setIsAdvanced(true);
        $rule->setSortOrder(false);
        $rule->setSimpleAction($discountType);
        $rule->setDiscountAmount($discountAmount);
        $rule->setDiscountQty(false);
        $rule->setDiscountStep(false);
        $rule->setSimpleFreeShipping(false);
        $rule->setApplyToShipping(false);
        $rule->setWebsiteIds(array(true));
        $rule->loadPost($rule->getData());
        $rule->setCouponType(2);
        $labels = array();
        $labels[1] = ' Discount Coupon for ' . $email;
        $rule->setStoreLabels($labels);
        try {
            $rule->save();
        } catch (\Exception $e) {
            $affiliateData['error'] = true;
            $affiliateData['error_message'] = $e->getMessage();
            return ['data' => $affiliateData];

        }

        $couponModel = $this->couponFactory->create();
        $couponModel->setData('customer_id', $customer_Id);
        $couponModel->setData('coupon_code', $uniqueId);
        $couponModel->setData('status', 1);
        $couponModel->setData('expiration_date', $next);
        $couponModel->setData('amount', $discountAmount);
        $couponModel->setData('cart_amount', $minpurchase);
        try {
            $couponModel->save();
        } catch (\Exception $e) {
            $affiliateData['error'] = true;
            $affiliateData['error_message'] = $e->getMessage();
            return ['data' => $affiliateData];
        }

        $transaction = $this->transactionFactory->create();
        $transaction->setData('customer_id', $customer_Id);
        $transaction->setData('description', 'Discount Coupon Generated: ' . $uniqueId);
        $transaction->setData('earned_amount', (-$amount));
        $transaction->setData('transaction_type', 2);
        try {
            $transaction->save();
        } catch (\Exception $e) {
            $affiliateData['error'] = true;
            $affiliateData['error_message'] = $e->getMessage();
            return ['data' => $affiliateData];
        }
        $affiliateData['success'] = true;
        $affiliateData['success_message'] = __("Discount Coupon Generated SuccessFully:" . $uniqueId);
        return ['data' => $affiliateData];

    }

    /**
     * function generatePromoCode
     *
     * for creating the coupon code
     * @return string containing coupon code
     */

    private function generatePromoCode($length = null)
    {
        $rndId = md5(uniqid(rand(), 1));
        $rndId = strip_tags(stripslashes($rndId));
        $rndId = str_replace(array(".", "$"), "", $rndId);
        $rndId = strrev(str_replace("/", "", $rndId));
        if (!is_null($rndId)) {
            return strtoupper(substr($rndId, 0, $length));
        }
        return strtoupper($rndId);
    }

    /**
     * @param $parameters
     * @return int
     */
    public function pendingamount($parameters)
    {
        $amount = 0;
        $customer_Id = $parameters['customerId'];
        $referred_list = $this->transactionCollectionFactory->addFieldtoFilter('customer_id', [
            'customer_id' => $customer_Id
        ]);
        foreach ($referred_list as $value) {
            $amount += $value['earned_amount'];
        }
        return $amount;
    }

}