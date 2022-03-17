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
 * @package     Ced_CsPromotions
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsPromotions\Model;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Utility
 *
 * @package Magento\SalesRule\Model
 */
class Utility extends \Magento\SalesRule\Model\Utility
{
    /**
     * @var array
     */
    protected $_roundingDeltas = [];

    /**
     * @var array
     */
    protected $_baseRoundingDeltas = [];

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Coupon\UsageFactory
     */
    protected $usageFactory;

    /**
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $couponFactory;

    /**
     * @var \Magento\SalesRule\Model\Rule\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $objectFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\SalesRule\Model\ResourceModel\Coupon\UsageFactory $usageFactory
     * @param CouponFactory $couponFactory
     * @param Rule\CustomerFactory $customerFactory
     * @param \Magento\Framework\DataObjectFactory $objectFactory
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\SalesRule\Model\ResourceModel\Coupon\UsageFactory $usageFactory,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\SalesRule\Model\Rule\CustomerFactory $customerFactory,
        \Magento\Framework\DataObjectFactory $objectFactory,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->couponFactory = $couponFactory;
        $this->customerFactory = $customerFactory;
        $this->usageFactory = $usageFactory;
        $this->objectFactory = $objectFactory;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Check if rule can be applied for specific address/quote/customer
     *
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function canProcessRule($rule, $address)
    {
        if ($rule->hasIsValidForAddress($address) && !$address->isObjectNew()) {
            return $rule->getIsValidForAddress($address);
        }

        /**
         * check per coupon usage limit
         */
        if ($rule->getCouponType() != \Magento\SalesRule\Model\Rule::COUPON_TYPE_NO_COUPON) {
            $couponCode = $address->getQuote()->getCouponCode();
            if (strlen($couponCode)) {
                /** @var \Magento\SalesRule\Model\Coupon $coupon */
                $coupon = $this->couponFactory->create();
                $coupon->load($couponCode, 'code');
                if ($coupon->getId()) {
                    // check entire usage limit
                    if ($coupon->getUsageLimit() && $coupon->getTimesUsed() >= $coupon->getUsageLimit()) {
                        $rule->setIsValidForAddress($address, false);
                        return false;
                    }
                    // check per customer usage limit
                    $customerId = $address->getQuote()->getCustomerId();
                    if ($customerId && $coupon->getUsagePerCustomer()) {
                        $couponUsage = $this->objectFactory->create();
                        $this->usageFactory->create()->loadByCustomerCoupon(
                            $couponUsage,
                            $customerId,
                            $coupon->getId()
                        );
                        if ($couponUsage->getCouponId() &&
                            $couponUsage->getTimesUsed() >= $coupon->getUsagePerCustomer()
                        ) {
                            $rule->setIsValidForAddress($address, false);
                            return false;
                        }
                    }
                }
            }
        }

        /**
         * check per rule usage limit
         */
        $ruleId = $rule->getId();
        if ($ruleId && $rule->getUsesPerCustomer()) {
            $customerId = $address->getQuote()->getCustomerId();
            /** @var \Magento\SalesRule\Model\Rule\Customer $ruleCustomer */
            $ruleCustomer = $this->customerFactory->create();
            $ruleCustomer->loadByCustomerRule($customerId, $ruleId);
            if ($ruleCustomer->getId()) {
                if ($ruleCustomer->getTimesUsed() >= $rule->getUsesPerCustomer()) {
                    $rule->setIsValidForAddress($address, false);
                    return false;
                }
            }
        }
        $rule->afterLoad();
        /**
         * quote does not meet rule's conditions
         */
        if ($rule->getVendorId() != "0") {
            $productPrice = 0;
            foreach ($address->getAllItems() as $item) {
                if($item->getVendorId() === $rule->getVendorId()){
                    $productPrice += ($item->getPrice() * $item->getQty());
                }
            }
            $address->setBaseSubtotal($productPrice);
        }

        if (!$rule->validate($address)) {
            $rule->setIsValidForAddress($address, false);
            return false;
        }
        /**
         * passed all validations, remember to be valid
         */
        $rule->setIsValidForAddress($address, true);
        return true;
    }
}

