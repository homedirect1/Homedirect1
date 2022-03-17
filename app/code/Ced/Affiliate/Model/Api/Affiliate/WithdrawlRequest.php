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
 * Class WithdrawlRequest
 * @package Ced\Affiliate\Model\Api\Affiliate
 */
class WithdrawlRequest implements \Ced\Affiliate\Api\Affiliate\WithdrawlRequestInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory
     */
    protected $withdrawlCollectionFactory;

    /**
     * @var \Ced\Affiliate\Model\AffiliateWithdrawlFactory
     */
    protected $affiliateWithdrawlFactory;

    /**
     * @var \Ced\Affiliate\Model\AffiliateWalletFactory
     */
    protected $affiliateWalletFactory;

    /**
     * @var \Ced\Affiliate\Model\AmountSummaryFactory
     */
    protected $amountSummaryFactory;

    /**
     * WithdrawlRequest constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory $withdrawlCollectionFactory
     * @param \Ced\Affiliate\Model\AffiliateWithdrawlFactory $affiliateWithdrawlFactory
     * @param \Ced\Affiliate\Model\AffiliateWalletFactory $affiliateWalletFactory
     * @param \Ced\Affiliate\Model\AmountSummaryFactory $amountSummaryFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory $withdrawlCollectionFactory,
        \Ced\Affiliate\Model\AffiliateWithdrawlFactory $affiliateWithdrawlFactory,
        \Ced\Affiliate\Model\AffiliateWalletFactory $affiliateWalletFactory,
        \Ced\Affiliate\Model\AmountSummaryFactory $amountSummaryFactory
    )
    {
        $this->_logger = $logger;
        $this->_scopeConfig = $scopeConfig;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->withdrawlCollectionFactory = $withdrawlCollectionFactory;
        $this->affiliateWithdrawlFactory = $affiliateWithdrawlFactory;
        $this->affiliateWalletFactory = $affiliateWalletFactory;
        $this->amountSummaryFactory = $amountSummaryFactory;
    }

    /**
     * @param $parameters
     * @return array|string
     */
    public function withdrawlRequest($parameters)
    {


        $this->_logger->critical(json_encode($parameters));
        $isTax = $this->_scopeConfig->getValue('affiliate/withdrawl/service_tax_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $minAmount = $this->_scopeConfig->getValue('affiliate/withdrawl/min_withdrwal_amount',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (!isset($parameters['customerId']) && !$parameters['customerId']) {
            $affiliateData['error'] = true;
            $affiliateData['error_message'] = __('No Customer Id');
            return ['data' => $affiliateData];
        }

        $affiliateObject = $this->affiliateAccountFactory->create()->load($parameters['customerId'], 'customer_id');
        $affiliate_withdrawl = $this->withdrawlCollectionFactory->create()
            ->addFieldToFilter('customer_id', $parameters['customerId'])->addFieldToFilter('status', 0);
        if (count($affiliate_withdrawl->getData()) > 0) {
            $affiliateData['error'] = true;
            $affiliateData['error_message'] = __('You Have Already One Request In Pending');
            return ['data' => $affiliateData];
        }
        if ($parameters['totalamount'] < $minAmount) {
            $affiliateData['error'] = true;
            $affiliateData['error_message'] = __('You Should Have minimum %1 amount for withdrawal request', $minAmount);
            return ['data' => $affiliateData];
        }

        if ($parameters['totalamount'] < $parameters['amount']) {
            $affiliateData['error'] = true;
            $affiliateData['error_message'] = __('You are Requesting more than available balance');
            return ['data' => $affiliateData];
        }
        $affiliate_withdrawl = $this->affiliateWithdrawlFactory->create();
        if ($isTax) {
            $taxMode = $this->_scopeConfig->getValue('affiliate/withdrawl/service_tax_mode',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($taxMode == 'fixed') {
                $taxValue = $this->_scopeConfig->getValue('affiliate/withdrawl/service_tax_charges',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $payableAmount = (float)$parameters['amount'] - (float)$taxValue;
            } else {
                $taxValue = $this->_scopeConfig->getValue('affiliate/withdrawl/service_tax_charges',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $taxValue = ($parameters['amount'] * $taxValue) / 100;
                $payableAmount = (float)$parameters['amount'] - (float)$taxValue;
            }
        } else {
            $taxValue = '0.00';
            $payableAmount = $parameters['amount'];
            $taxMode = "Not Applied";
        }

        $customerName = $affiliateObject->getCustomername();
        $withdrawl = $this->affiliateWithdrawlFactory->create();
        $withdrawl->setCustomerId($affiliateObject->getCustomerId());
        $withdrawl->setAffiliateId($affiliateObject->getAffiliateId());
        $withdrawl->setRequestAmount($parameters['amount']);
        $withdrawl->setCustomerName($customerName);
        $withdrawl->setCustomerEmail($affiliateObject->getCustomerEmail());
        $withdrawl->setPaymentMode($parameters['payment_method']);
        $withdrawl->setCreatedAt(time());
        $withdrawl->setServiceTax($taxValue);
        $withdrawl->setServiceTaxMode($taxMode);
        $withdrawl->setPayableAmount($payableAmount);
        $withdrawl->setStatus(\Ced\Affiliate\Model\AffiliateWithdrawl::PENDING);
        if (isset($parameters['iscredit'])) {
            $withdrawl->setIscredit(true);
            $amountWallet = $this->affiliateWalletFactory->create()
                ->load($affiliateObject->getCustomerId(), 'customer_id');
            $amountWallet->setRemainingAmount((float)$amountWallet->getRemainingAmount() - (float)$parameters['amount']);
            $amountWallet->save();
        } else {
            $amount_summary = $this->amountSummaryFactory->create()
                ->load($affiliateObject->getAffiliateId(), 'affiliate_id');
            $amount_summary->setRemainingAmount((float)$amount_summary->getRemainingAmount() - (float)$parameters['amount']);
            $amount_summary->save();
        }
        $withdrawl->save();
        $affiliateData['success'] = true;
        $affiliateData['success_message'] = __('Successfully Requested');
        return ['data' => $affiliateData];
    }

}