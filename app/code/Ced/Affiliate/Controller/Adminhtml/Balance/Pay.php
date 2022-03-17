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

namespace Ced\Affiliate\Controller\Adminhtml\Balance;

use Magento\Backend\App\Action;

/**
 * Class Pay
 * @package Ced\Affiliate\Controller\Adminhtml\Balance
 */
class Pay extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * @var \Ced\Affiliate\Model\AffiliateWithdrawlFactory
     */
    protected $affiliateWithdrawlFactory;

    /**
     * Pay constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param \Magento\Framework\File\Csv $fileCsv
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Ced\Affiliate\Model\AffiliateWithdrawlFactory $affiliateWithdrawlFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\File\Csv $fileCsv,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Ced\Affiliate\Model\AffiliateWithdrawlFactory $affiliateWithdrawlFactory
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->affiliateWithdrawlFactory = $affiliateWithdrawlFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Edit grid record
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {

        $resultredirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        $amount = $this->getRequest()->getParam('amount');
        $totalamount = $this->getRequest()->getParam('totalamount');
        $affiliateaccount = $this->affiliateAccountFactory->create();

        $modelData = $affiliateaccount->load($id);

        $affiliate_withdrawl = $this->affiliateWithdrawlFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $modelData->getCustomerId())
            ->addFieldToFilter('status', 0);
        $minAmount = $this->_scopeConfig->getValue('affiliate/withdrawl/min_withdrwal_amount',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $isTax = $this->_scopeConfig->getValue('affiliate/withdrawl/service_tax_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);


        if (count($affiliate_withdrawl->getData()) > 0) {
            $this->messageManager->addErrorMessage(__('You Have Already One Request In Pending'));
            $resultredirect->setPath('affiliate/manage/edit', array('id' => $id));
            return $resultredirect;

        }
        if ($totalamount < $minAmount) {
            $this->messageManager->addErrorMessage(__('You Should Have minimum %1 amount for withdrawal request', $minAmount));
            $resultredirect->setPath('affiliate/manage/edit', array('id' => $id));
            return $resultredirect;

        }

        if ($totalamount < $amount) {
            $this->messageManager->addErrorMessage(__('You are Requesting more than available balance'));
            $resultredirect->setPath('affiliate/manage/edit', array('id' => $id));
            return $resultredirect;

        }

        if ($isTax) {
            $taxMode = $this->_scopeConfig->getValue('affiliate/withdrawl/service_tax_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($taxMode == 'fixed') {

                $taxValue = $this->_scopeConfig->getValue('affiliate/withdrawl/service_tax_charges', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $payableAmount = (float)$amount - (float)$taxValue;
            } else {

                $taxValue = $this->_scopeConfig->getValue('affiliate/withdrawl/service_tax_charges', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $taxValue = ($amount * $taxValue) / 100;
                $payableAmount = (float)$amount - (float)$taxValue;
            }
        } else {

            $taxValue = '0.00';
            $payableAmount = $amount;
            $taxMode = "Not Applied";
        }

        $requestData['service_tax_mode'] = $taxMode;
        $requestData['request_amount'] = $amount;
        $requestData['service_tax'] = $taxValue;
        $requestData['payable_amount'] = $payableAmount;
        $requestData['customer_name'] = $modelData->getCustomerName();
        $requestData['customer_email'] = $modelData->getCustomerEmail();
        $requestData['customer_id'] = $modelData->getCustomerId();
        $requestData['payable_amount2'] = $payableAmount;
        $requestData['affiliate_id'] = $modelData->getAffiliateId();


        $this->_coreRegistry->register('current_account_data', $requestData);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ced_Affiliate');
        $resultPage->addBreadcrumb(__('CMS'), __('CMS'));
        $resultPage->addBreadcrumb(__('Manage'), __('Account'));
        $resultPage->getConfig()->getTitle()->prepend(__('AffiliateAccount'));
        return $resultPage;
    }


}
