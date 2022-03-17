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

namespace Ced\Affiliate\Block\Comission;

/**
 * Class ComissionRequest
 * @package Ced\Affiliate\Block\Comission
 */
class ComissionRequest extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory
     */
    protected $comissionCollectionFactory;

    /**
     * @var \Ced\Affiliate\Model\PaymentMethodsFactory
     */
    protected $paymentMethodsFactory;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * @var \Ced\Affiliate\Model\AmountSummaryFactory
     */
    protected $amountSummaryFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory
     */
    protected $withdrawlCollectionFactory;

    /**
     * ComissionRequest constructor.
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory
     * @param \Ced\Affiliate\Model\PaymentMethodsFactory $paymentMethodsFactory
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Ced\Affiliate\Model\AmountSummaryFactory $amountSummaryFactory
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory $withdrawlCollectionFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory,
        \Ced\Affiliate\Model\PaymentMethodsFactory $paymentMethodsFactory,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Ced\Affiliate\Model\AmountSummaryFactory $amountSummaryFactory,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory $withdrawlCollectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    )
    {
        $this->_customerSession = $customerSession;
        $this->comissionCollectionFactory = $comissionCollectionFactory;
        $this->paymentMethodsFactory = $paymentMethodsFactory;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->affiliateHelper = $affiliateHelper;
        $this->amountSummaryFactory = $amountSummaryFactory;
        $this->withdrawlCollectionFactory = $withdrawlCollectionFactory;
        parent::__construct($context, $data);


    }

    /**
     * @return mixed
     */
    public function getComisssion()
    {
        return $this->comissionCollectionFactory->create();
    }

    /**
     * @return mixed
     */
    public function getActivePayment()
    {
        return $this->paymentMethodsFactory->create()
            ->getPaymentMethodsArray($this->_customerSession->getCustomer()->getId());
    }

    /**
     * @return mixed
     */
    public function getAffiliateId()
    {

        $model = $this->affiliateAccountFactory->create()
            ->load($this->_customerSession->getCustomer()->getId(), 'customer_id');
        return $model->getAffiliateId();
    }

    /**
     * @return mixed
     */
    public function getComissionAmount()
    {
        $orderStatus = $this->_scopeConfig->getValue('affiliate/comission/add_comission_when',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $holdingDays = $this->_scopeConfig->getValue('affiliate/comission/holding_time',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (!$holdingDays) {
            $holdingDays = 1;
        }
        $timeStamp = time();
        $toDate = date('Y-m-d H:i:s', $timeStamp);
        $fromDate = date('Y-m-d H:i:s', $timeStamp - 86400 * $holdingDays);
        $model = $this->comissionCollectionFactory->create()
            ->addFieldToFilter('affiliate_id', $this->_customerSession->getAffiliateId());
        $model->addFieldToFilter('status', 'complete');
        $model->addFieldToFilter('create_at', array('from' => $fromDate, 'to' => $toDate));
        $model->getSelect()->reset('columns')->columns(['total_amount' => 'SUM(comission)']);
        $this->setAmountSummary($model->getData());
        return $model->getData();
    }

    /**
     * @param $amount
     * @return mixed
     */
    public function getFormattedPrice($amount)
    {
        return $this->affiliateHelper->getFormattedPrice($amount);
    }

    /**
     * @param $amount
     */
    public function setAmountSummary($amount)
    {


        if (isset($amount[0]['total_amount']) && $amount[0]['total_amount'] && $amount[0]['total_amount'] > 0) {

            $amountSummary = $this->amountSummaryFactory->create();

            if (!$amountSummary->load($this->_customerSession->getAffiliateId(), 'affiliate_id')->getData()) {
                $amountSummary = $this->amountSummaryFactory->create();

                $tot = $amountSummary->getTotalAmount();
                $totrem = $amountSummary->getRemainingAmount();
                $amountSummary->setTotalAmount($amount[0]['total_amount']);

                $rem = $amount[0]['total_amount'] - $tot;

                $amountSummary->setRemainingAmount($totrem + $rem);
                $amountSummary->setAffiliateId($this->_customerSession->getAffiliateId(), 'affiliate_id');
                $amountSummary->save();
            } else {
                $amountSummary->setTotalAmount($amount[0]['total_amount']);
                $amountSummary->setRemainingAmount($amount[0]['total_amount']);
                $amountSummary->save();
            }
        }
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
     * @return mixed
     */
    public function getMinBalance()
    {
        return $this->getFormattedPrice($this->_scopeConfig->getValue('affiliate/withdrawl/min_withdrwal_amount',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    /**
     * @return mixed
     */
    public function getDays()
    {
        return $this->_scopeConfig->getValue('affiliate/withdrawl/cancel_days',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
} 
