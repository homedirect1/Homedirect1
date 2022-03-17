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

namespace Ced\Affiliate\Controller\Withdrawl;

/**
 * Class Request
 * @package Ced\Affiliate\Controller\Withdrawl
 */
class Request extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_custmerSesion;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory
     */
    protected $affiliateCollectionFactory;

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
     * Request constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory $affiliateCollectionFactory
     * @param \Ced\Affiliate\Model\AffiliateWithdrawlFactory $affiliateWithdrawlFactory
     * @param \Ced\Affiliate\Model\AffiliateWalletFactory $affiliateWalletFactory
     * @param \Ced\Affiliate\Model\AmountSummaryFactory $amountSummaryFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory $affiliateCollectionFactory,
        \Ced\Affiliate\Model\AffiliateWithdrawlFactory $affiliateWithdrawlFactory,
        \Ced\Affiliate\Model\AffiliateWalletFactory $affiliateWalletFactory,
        \Ced\Affiliate\Model\AmountSummaryFactory $amountSummaryFactory
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_custmerSesion = $session;
        $this->affiliateHelper = $affiliateHelper;
        $this->affiliateCollectionFactory = $affiliateCollectionFactory;
        $this->affiliateWithdrawlFactory = $affiliateWithdrawlFactory;
        $this->affiliateWalletFactory = $affiliateWalletFactory;
        $this->amountSummaryFactory = $amountSummaryFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {

        if (!$this->affiliateHelper->isAffiliateEnable()) {

            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/index');
            return $resultRedirect;
        }

        if (!$this->_custmerSesion->isLoggedIn()) {
            $this->_redirect('customer/account/login');
            return;
        }

        $isTax = $this->_scopeConfig->getValue('affiliate/withdrawl/service_tax_enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $minAmount = $this->_scopeConfig->getValue('affiliate/withdrawl/min_withdrwal_amount', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data = $this->getRequest()->getPostValue();


        $affiliate_withdrawl = $this->affiliateCollectionFactory->create()
            ->addFieldToFilter('customer_id', $this->_custmerSesion->getCustomerId())
            ->addFieldToFilter('status', 0);

        if (count($affiliate_withdrawl->getData()) > 0) {
            $this->messageManager->addErrorMessage(__('You Have Already One Request In Pending'));
            $this->_redirect('*/*/index');
            return;

        }


        if ($data['totalamount'] < $minAmount) {
            $this->messageManager->addErrorMessage(__('You Should Have minimum %1 amount for withdrawal request', $minAmount));
            $this->_redirect('*/*/index');
            return;

        }

        if ($data['totalamount'] < $data['amount']) {
            $this->messageManager->addErrorMessage(__('You are Requesting more than available balance'));
            $this->_redirect('*/*/index');
            return;

        }
        if ($isTax) {
            $taxMode = $this->_scopeConfig->getValue('affiliate/withdrawl/service_tax_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($taxMode == 'fixed') {

                $taxValue = $this->_scopeConfig->getValue('affiliate/withdrawl/service_tax_charges', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $payableAmount = (float)$data['amount'] - (float)$taxValue;
            } else {

                $taxValue = $this->_scopeConfig->getValue('affiliate/withdrawl/service_tax_charges', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $taxValue = ($data['amount'] * $taxValue) / 100;
                $payableAmount = (float)$data['amount'] - (float)$taxValue;
            }
        } else {

            $taxValue = '0.00';
            $payableAmount = $data['amount'];
            $taxMode = "Not Applied";
        }

        $customerName = $this->_custmerSesion->getCustomer()->getFirstname() . ' ' . $this->_custmerSesion->getCustomer()->getLastname();
        $postData = $this->getRequest()->getPostValue();
        $withdrawl = $this->affiliateWithdrawlFactory->create();
        $withdrawl->setCustomerId($this->_custmerSesion->getCustomer()->getId());
        $withdrawl->setAffiliateId($this->getRequest()->getParam('affiliateId'));
        $withdrawl->setRequestAmount($postData['amount']);
        $withdrawl->setCustomerName($customerName);
        $withdrawl->setCustomerEmail($this->_custmerSesion->getCustomer()->getEmail());
        $withdrawl->setPaymentMode($postData['payment_method']);
        $withdrawl->setCreatedAt(time());
        $withdrawl->setServiceTax($taxValue);
        $withdrawl->setServiceTaxMode($taxMode);
        $withdrawl->setPayableAmount($payableAmount);
        if (isset($data['redemmedformwallet'])) {
            $withdrawl->setRedemmedFromWallet(true);
        }
        $withdrawl->setStatus(\Ced\Affiliate\Model\AffiliateWithdrawl::PENDING);
        if (isset($data['iscredit'])) {
            $withdrawl->setIscredit(true);
            $amountWallet = $this->affiliateWalletFactory->create()->load($this->_custmerSesion->getCustomerId(), 'customer_id');
            $amountWallet->setRemainingAmount((float)$amountWallet->getRemainingAmount() - (float)$postData['amount']);
            $amountWallet->save();
        } else {
            $amount_summary = $this->amountSummaryFactory->create()->load($this->_custmerSesion->getAffiliateId(), 'affiliate_id');
            $amount_summary->setRemainingAmount((float)$amount_summary->getRemainingAmount() - (float)$postData['amount']);
            $amount_summary->save();

        }
        $withdrawl->save();

        $this->messageManager->addSuccessMessage(__('You Successfully Requested'));
        $this->_redirect('affiliate/comission/index');
        return;

    }

}
