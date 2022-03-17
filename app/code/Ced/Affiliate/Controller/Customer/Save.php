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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Controller\Customer;

/**
 * Class Save
 * @package Ced\Affiliate\Controller\Customer
 */
class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Ced\Affiliate\Model\DiscountDenominationFactory
     */
    protected $discountDenominationFactory;

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
     * @var \Magento\Customer\Model\Session
     */
    protected $_getSession;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Save constructor.
     * @param \Ced\Affiliate\Model\DiscountDenominationFactory $discountDenominationFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory
     * @param \Ced\Affiliate\Model\CouponFactory $couponFactory
     * @param \Ced\Affiliate\Model\TransactionFactory $transactionFactory
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        \Ced\Affiliate\Model\DiscountDenominationFactory $discountDenominationFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory,
        \Ced\Affiliate\Model\CouponFactory $couponFactory,
        \Ced\Affiliate\Model\TransactionFactory $transactionFactory,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    )
    {
        $this->_date = $date;
        $this->_getSession = $customerSession;
        $this->discountDenominationFactory = $discountDenominationFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->ruleFactory = $ruleFactory;
        $this->couponFactory = $couponFactory;
        $this->transactionFactory = $transactionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {

        $data = $this->getRequest()->getPost();
        $amount = $data["discount_coupon"];
        $available = $this->pendingamount();
        if ($amount > $available) {
            $this->messageManager->addErrorMessage(__("You have insufficient balance"));
            $this->_redirect('affiliate/customer/payout');
            return;
        }
        $customer = $this->_getSession->getCustomer();
        $customer_Id = $customer->getId();
        $customer_Email = $customer->getEmail();
        $discountType = "cart_fixed";
        $discountAmount = $amount;
        $percoupon = 1;
        $percustomer = 1;
        $email = $customer_Email;

        $minpurchase = 0;
        $cart_amount_check_rule = $this->discountDenominationFactory->create()
            ->getCollection()
            ->addFieldToFilter('status', 0)
            ->addFieldToFilter('discount_amount', $amount)
            ->getData();

        foreach ($cart_amount_check_rule as $key => $value) {
            $minpurchase = $value['cart_amount'];
        }
        $expireDays = $this->scopeConfig->getValue('affiliate/discount/discount_code_expiration_days');
        if (!isset($expireDays)) {
            $expireDays = 0;
        }
        $today = date_create($this->_date->date('Y-m-d H:i:s'));

        $next = date_format(date_add($today, date_interval_create_from_date_string($expireDays . " days")), "Y-m-d H:i:s");

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
        $rule->setIsActive(1);
        $rule->setStopRulesProcessing(1);
        $rule->setIsRss(0);
        $rule->setIsAdvanced(1);
        $rule->setSortOrder(0);
        $rule->setSimpleAction($discountType);
        $rule->setDiscountAmount($discountAmount);
        $rule->setDiscountQty(0);
        $rule->setDiscountStep(0);
        $rule->setSimpleFreeShipping(0);
        $rule->setApplyToShipping(0);
        $rule->setWebsiteIds(array(1));
        $rule->loadPost($rule->getData());
        $rule->setCouponType(2);
        $labels = array();
        $labels[1] = ' Discount Coupon for ' . $email;
        $rule->setStoreLabels($labels);
        try {
            $rule->save();
        } catch (\Exception $e) {
            $e->getMessage();
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
            echo $e->getMessage();
        }

        $transaction = $this->transactionFactory->create();
        $transaction->setData('customer_id', $customer_Id);
        $transaction->setData('description', 'Discount Coupon Generated: ' . $uniqueId);
        $transaction->setData('earned_amount', (-$amount));
        $transaction->setData('transaction_type', 2);
        try {
            $transaction->save();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        $this->messageManager->addSuccessMessage(__("Discount Coupon Generated SuccessFully:" . $uniqueId));
        $this->_redirect('affiliate/customer/payout');
        return;

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
     * @return int
     */
    public function pendingamount()
    {
        $amount = 0;
        $customer = $this->_getSession->getCustomer();
        $customer_Id = $customer->getId();
        $referred_list = $this->transactionFactory->create()->getCollection()->addFieldtoFilter('customer_id', [
            'customer_id' => $customer_Id
        ]);
        foreach ($referred_list as $value) {
            $amount += $value['earned_amount'];
        }
        return $amount;
    }
}