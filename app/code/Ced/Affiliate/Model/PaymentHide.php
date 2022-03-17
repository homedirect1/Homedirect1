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

namespace Ced\Affiliate\Model;

use Magento\Directory\Helper\Data as DirectoryHelper;

/**
 * Class PaymentHide
 * @package Ced\Affiliate\Model
 */
class PaymentHide extends \Ced\Affiliate\Model\AffiliatePayment
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var AffiliateWalletFactory
     */
    protected $affiliateWalletFactory;

    /**
     * PaymentHide constructor.
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Customer\Model\Session $session
     * @param AffiliateWalletFactory $affiliateWalletFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param DirectoryHelper|null $directory
     */
    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Customer\Model\Session $session,
        \Ced\Affiliate\Model\AffiliateWalletFactory $affiliateWalletFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        DirectoryHelper $directory = null
    )
    {
        $this->cart = $cart;
        $this->session = $session;
        $this->affiliateWalletFactory = $affiliateWalletFactory;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data,
            $directory
        );
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        $checkoutdata = $this->cart;
        $customer = $this->session;
        if ($customer->isLoggedIn()) {
            $creditdata = $this->affiliateWalletFactory->create()->load($customer->getCustomerId(), 'customer_id');
            $discountTotal = 0;
            foreach ($checkoutdata->getQuote()->getAllItems() as $item) {
                $discountTotal += $item->getDiscountAmount();
            }
            $total = $checkoutdata->getQuote()->getBaseGrandTotal();
            $paymentamount = $total - $discountTotal;
            if ($creditdata->getRemainingAmount() < $paymentamount)
                return false;
            else
                return true;
        }
        return false;
    }
}