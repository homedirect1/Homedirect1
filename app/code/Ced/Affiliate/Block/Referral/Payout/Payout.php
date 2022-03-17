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

namespace Ced\Affiliate\Block\Referral\Payout;

use Magento\Framework\View\Element\Template\Context;

/**
 * Class Payout
 * @package Ced\Affiliate\Block\Referral\Payout
 */
class Payout extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_getSession;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\Transaction\CollectionFactory
     */
    protected $affiliateTransactionCollectionFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateReferral\CollectionFactory
     */
    protected $affiliateReferralCollectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;

    /**
     * @var \Magento\Framework\Locale\Currency
     */
    public $localeCurrency;

    /**
     * Payout constructor.
     * @param \Ced\Affiliate\Helper\Data $dataHelper
     * @param \Ced\Affiliate\Model\ResourceModel\Transaction\CollectionFactory $affiliateTransactionCollectionFactory
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateReferral\CollectionFactory $affiliateReferralCollectionFactory
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Locale\Currency $localeCurrency
     */
    public function __construct(
        \Ced\Affiliate\Helper\Data $dataHelper,
        \Ced\Affiliate\Model\ResourceModel\Transaction\CollectionFactory $affiliateTransactionCollectionFactory,
        \Ced\Affiliate\Model\ResourceModel\AffiliateReferral\CollectionFactory $affiliateReferralCollectionFactory,
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Locale\Currency $localeCurrency
    )
    {
        $this->dataHelper = $dataHelper;
        $this->affiliateTransactionCollectionFactory = $affiliateTransactionCollectionFactory;
        $this->affiliateReferralCollectionFactory = $affiliateReferralCollectionFactory;
        $this->_getSession = $customerSession;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $context->getStoreManager();
        $this->localeCurrency = $localeCurrency;
        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\View\Element\Template
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set("Payout Referral Points");
        return $this;
    }

    /**
     *
     */
    public function _construct()
    {
        $customer = $this->_getSession->getCustomer();
        $customer_Id = $customer->getId();
        $productModel = $this->affiliateReferralCollectionFactory->create()
            ->addFieldtoFilter('customer_id', [
                'customer_id' => $customer_Id
            ])->addFieldtoFilter('signup_status', '1');
        $this->setCollection($productModel);
    }

    /**
     * @return int
     */
    public function pendingAmount()
    {
        $amount = 0;
        $customer = $this->_getSession->getCustomer();
        $customer_Id = $customer->getId();
        $referred_list = $this->affiliateTransactionCollectionFactory->create()
            ->addFieldtoFilter('customer_id', [
                'customer_id' => $customer_Id
            ]);
        foreach ($referred_list as $value) {
            $amount += $value['earned_amount'];
        }
        return $amount;
    }

    /**
     * @param $amount
     * @return mixed
     */
    public function getFormattedPrice($amount)
    {
        return $this->dataHelper->getFormattedPrice($amount);
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {

        return $this->_scopeConfig->getValue('affiliate/referfriend/referral_reward_denomination_range');

    }
}