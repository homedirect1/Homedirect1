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

namespace Ced\Affiliate\Block\Account\Dashboard;

use Ced\Affiliate\Model\ResourceModel\AffiliateComission\Collection;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Info
 * @package Ced\Affiliate\Block\Account\Dashboard
 */
class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * Cached subscription object
     *
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $_subscription;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $_subscriberFactory;

    /** @var \Magento\Customer\Helper\View */
    protected $_helperView;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory
     */
    protected $comissionCollectionFactory;

    /**
     * @var \Ced\Affiliate\Model\AmountSummaryFactory
     */
    protected $amountSummaryFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory
     */
    protected $withdrawlCollectionFactory;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * Info constructor.
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory
     * @param \Ced\Affiliate\Model\AmountSummaryFactory $amountSummaryFactory
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory $withdrawlCollectionFactory
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Helper\View $helperView
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory,
        \Ced\Affiliate\Model\AmountSummaryFactory $amountSummaryFactory,
        \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory $withdrawlCollectionFactory,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Helper\View $helperView,
        array $data = []
    )
    {
        $this->currentCustomer = $currentCustomer;
        $this->_subscriberFactory = $subscriberFactory;
        $this->_helperView = $helperView;
        $this->_customerSession = $customerSession;
        $this->comissionCollectionFactory = $comissionCollectionFactory;
        $this->amountSummaryFactory = $amountSummaryFactory;
        $this->withdrawlCollectionFactory = $withdrawlCollectionFactory;
        $this->affiliateHelper = $affiliateHelper;
        parent::__construct($context, $data);
    }

    /**
     * Returns the Magento Customer Model for this block
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {
        try {
            return $this->currentCustomer->getCustomer();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Get the full name of a customer
     *
     * @return string full name
     */
    public function getName()
    {
        return $this->_helperView->getCustomerName($this->getCustomer());
    }

    /**
     * @return string
     */
    public function getChangePasswordUrl()
    {
        return $this->_urlBuilder->getUrl('customer/account/edit/changepass/1');
    }

    /**
     * Get Customer Subscription Object Information
     *
     * @return \Magento\Newsletter\Model\Subscriber
     */
    public function getSubscriptionObject()
    {
        if (!$this->_subscription) {
            $this->_subscription = $this->_createSubscriber();
            $customer = $this->getCustomer();
            if ($customer) {
                $this->_subscription->loadByEmail($customer->getEmail());
            }
        }
        return $this->_subscription;
    }

    /**
     * Gets Customer subscription status
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsSubscribed()
    {
        return $this->getSubscriptionObject()->isSubscribed();
    }

    /**
     * Newsletter module availability
     *
     * @return bool
     */
    public function isNewsletterEnabled()
    {
        return $this->getLayout()->getBlockSingleton('Magento\Customer\Block\Form\Register')->isNewsletterEnabled();
    }

    /**
     * @return \Magento\Newsletter\Model\Subscriber
     */
    protected function _createSubscriber()
    {
        return $this->_subscriberFactory->create();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return $this->currentCustomer->getCustomerId() ? parent::_toHtml() : '';
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {


        $orderStatus = $this->_scopeConfig->getValue('affiliate/comission/add_comission_when',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $holdingDays = $this->_scopeConfig->getValue('affiliate/comission/holding_time',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $model = $this->comissionCollectionFactory->create()
            ->addFieldToFilter('affiliate_id', $this->_customerSession->getAffiliateId());
        $model->addFieldToFilter('status', 'complete');

        if ($holdingDays && $holdingDays > 0) {
            $timeStamp = time();
            $toDate = date('Y-m-d H:i:s', $timeStamp);
            $fromDate = date('Y-m-d H:i:s', $timeStamp - 86400 * $holdingDays);
            $model->addFieldToFilter('create_at', array('lteq' => $fromDate));
        }

        $model->getSelect()->reset('columns')->columns(['total_amount' => 'SUM(comission)']);
        $this->setAmountSummary($model->getData());

        $amountSummary = $this->amountSummaryFactory->create()
            ->load($this->_customerSession->getAffiliateId(), 'affiliate_id');
        return $model->getData();

    }

    /**
     * @return mixed
     */
    public function isAllowedForRequest()
    {

        return $this->_scopeConfig->getValue('affiliate/withdrawl/withdrawl_request',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getAmountHistory()
    {

        $amountSummary = $this->withdrawlCollectionFactory->create()
            ->addFieldToFilter('customer_id', $this->_customerSession->getCustomerId())
            ->addFieldToFilter('status', '1');
        $amountSummary->getSelect()->reset('columns')->columns(['earned_amount' => 'SUM(request_amount)']);
        return $amountSummary->getData();
    }

    /**
     * @return mixed
     */
    public function isAvailableWithdrawl()
    {

        return $this->affiliateHelper->isEnableWthdrawlRequest();
    }

    /**
     * @param $amount
     * @return mixed
     */
    public function getFormattedPrice($amount)
    {
        return $this->affiliateHelper->getFormattedPrice($amount);
    }
}
