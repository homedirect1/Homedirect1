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

namespace Ced\Affiliate\Model\Quote;

/**
 * Class Discount
 * @package Ced\Affiliate\Model\Quote
 */
class Discount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory
     */
    protected $comissionCollectionFactory;

    /**
     * Discount constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Magento\Customer\Model\Session $session,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory
    )
    {
        $this->setCode('customdiscount');
        $this->_scopeConfig = $scopeConfig;
        $this->affiliateHelper = $affiliateHelper;
        $this->session = $session;
        $this->checkoutSession = $checkoutSession;
        $this->comissionCollectionFactory = $comissionCollectionFactory;
    }


    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this|\Magento\Quote\Model\Quote\Address\Total\AbstractTotal
     */
    public function collect(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        parent::collect($quote, $shippingAssignment, $total);

        if (!$this->affiliateHelper->isAffiliateEnable()) {

            return $this;
        }

        $customer = $this->session;
        $affiliateId = $this->checkoutSession->getAffiliateId();
        $totalAmount = $quote->getBaseSubtotal();
        if ($affiliateId) {
            $check = $this->_scopeConfig->getValue('affiliate/discount/discountto',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($check == 'affiliate' || $check == 'cartaffiliate') {

                $discountType = $this->_scopeConfig->getValue('affiliate/discount/discount_type',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $discountValue = $this->_scopeConfig->getValue('affiliate/discount/discount_amount',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                if ($discountType == 'fixed') {
                    $discountAmount = $discountValue;
                } else {

                    $discountAmount = ($totalAmount * $discountValue) / 100;
                }

                $secondOrderDisountCheck = $this->_scopeConfig->getValue('affiliate/discount/second_discount',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                if ($secondOrderDisountCheck) {

                    if ($customer->isLoggedIn()) {

                        $model = $this->comissionCollectionFactory->create()
                            ->addFieldToFilter('customer_email', $customer->getCustomer()->getEmail())
                            ->addFieldToFilter('affiliate_id', $affiliateId);

                        if ($model->getData()) {

                            $discountType = $this->_scopeConfig->getValue('affiliate/discount/seoond_discount_type',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            $discountValue = $this->_scopeConfig->getValue('affiliate/discount/second_discount_amount',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            if ($discountType == 'fixed') {
                                $discountAmount = $discountValue;
                            } else {

                                $discountAmount = ($totalAmount * $discountValue) / 100;
                            }
                        }
                    }
                }

                $label = 'Affiliate Discount';
                $discountAmount = -$discountAmount;

                $appliedCartDiscount = 0;
                if ($total->getDiscountDescription()) {
                    // If a discount exists in cart and another discount is applied, the add both discounts.
                    $appliedCartDiscount = $total->getDiscountAmount();
                    $discountAmount = $total->getDiscountAmount() + $discountAmount;
                    $label = $total->getDiscountDescription() . ', ' . $label;
                }

                $total->setDiscountDescription($label);
                $total->setDiscountAmount($discountAmount);
                $total->setBaseDiscountAmount($discountAmount);
                $total->setSubtotalWithDiscount($total->getSubtotal() + $discountAmount);
                $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() + $discountAmount);
                if (isset ($appliedCartDiscount)) {
                    $total->addTotalAmount($this->getCode(), $discountAmount - $appliedCartDiscount);
                    $total->addBaseTotalAmount($this->getCode(), $discountAmount - $appliedCartDiscount);
                } else {
                    $total->addTotalAmount($this->getCode(), $discountAmount);
                    $total->addBaseTotalAmount($this->getCode(), $discountAmount);
                }
                return $this;
            }
        }
    }

    /**
     * Add discount total information to address
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array|null
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $result = null;
        $amount = $total->getDiscountAmount();

        if ($amount != 0) {
            $description = $total->getDiscountDescription();
            $result = [
                'code' => $this->getCode(),
                'title' => strlen($description) ? __('Discount (%1)', $description) : __('Discount'),
                'value' => $amount
            ];
        }
        return $result;
    }
}
