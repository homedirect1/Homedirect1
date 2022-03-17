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
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Block\Checkout;

/**
 * Class Discount
 * @package Ced\Affiliate\Block\Checkout
 */
class Discount extends \Magento\Framework\View\Element\Template
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
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Magento\Customer\Model\Session $session,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->affiliateHelper = $affiliateHelper;
        $this->session = $session;
        $this->checkoutSession = $checkoutSession;
        $this->comissionCollectionFactory = $comissionCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     *
     * @return boolean
     */
    public function getDiscountAmount()
    {
        $results = [];
        if (!$this->affiliateHelper->isAffiliateEnable()) {

            $results['enable'] = false;
            return $results;
        }

        $customer = $this->session;
        $affiliateId = $this->checkoutSession->getAffiliateId();
        $quote = $this->checkoutSession->getQuote();
        $total = $quote->getBaseSubtotal();
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

                    $discountAmount = ($total * $discountValue) / 100;
                }

                $secondOrderDisountCheck = $this->_scopeConfig->getValue('affiliate/discount/second_discount',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                if ($secondOrderDisountCheck) {

                    if ($customer->isLoggedIn()) {

                        $model = $this->comissionCollectionFactory->create()
                            ->addFieldToFilter('customer_email', $customer->getCustomer()->getEmail())
                            ->addFieldToFilter('affiliate_id', $affiliateId);

                        if ($model->getData()) {

                            $discountType = $this->_scopeConfig
                                ->getValue('affiliate/discount/seoond_discount_type',
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            $discountValue =
                                $this->_scopeConfig->getValue('affiliate/discount/second_discount_amount',
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            if ($discountType == 'fixed') {
                                $discountAmount = $discountValue;
                            } else {

                                $discountAmount = ($total * $discountValue) / 100;
                            }
                        }
                    }
                }

                $results['enable'] = true;
                $results['discount'] = $discountAmount;
                return $results;
            }
        }
        $results['enable'] = false;
        return $results;

    }
}
