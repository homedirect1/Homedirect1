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

class Save extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    protected $_custmerSesion;

    protected $_scopeConfig;

    protected $affiliateWithdrawlFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ced\Affiliate\Model\AffiliateWithdrawlFactory $affiliateWithdrawlFactory
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->resultPageFactory = $resultPageFactory;
        $this->_custmerSesion = $session;
        $this->affiliateWithdrawlFactory = $affiliateWithdrawlFactory;
        parent::__construct($context);
    }

    public function execute()
    {

        if (!$this->_custmerSesion->isLoggedIn()) {
            $this->_redirect('customer/account/login');
            return;
        }

        $isTax = $this->_scopeConfig->getValue('affiliate/withdrawl/service_tax_enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data = $this->getRequest()->getPostValue();
        $affiliate_withdrawl = $this->affiliateWithdrawlFactory->create();
        if ($isTax) {
            $taxMode = $this->_scopeConfig->getValue('affiliate/withdrawl/service_tax_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($taxMode == 'fixed') {

                $taxValue = $this->_scopeConfig->getValue('affiliate/withdrawl/service_tax_charges', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $payableAmount = (int)$data['request_amount'] - (int)$taxValue;
            } else {

                $taxValue = $this->_scopeConfig->getValue('affiliate/withdrawl/service_tax_charges', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $newTax = ($data['request_amount'] * $taxValue) / 100;
                $payableAmount = (int)$data['request_amount'] - (int)$newTax;
            }
        } else {

            $taxValue = '0.00';
            $payableAmount = $data['request_amount'];
            $taxMode = "Not Applied";
        }
        $affiliate_withdrawl->addData($data);
        $affiliate_withdrawl->setServiceTax($taxValue);
        $affiliate_withdrawl->setPayableAmount($payableAmount);
        $affiliate_withdrawl->setCustomerName($this->_custmerSesion->getCustomer()->getFirstname() . ' ' . $this->_custmerSesion->getCustomer()->getLastname());
        $affiliate_withdrawl->setServiceTaxMode($taxMode);

        $affiliate_withdrawl->save();
        $this->messageManager->addSuccessMessage(__('Successfully Saved'));
        $this->_redirect('*/*/');
        return;
    }
}
