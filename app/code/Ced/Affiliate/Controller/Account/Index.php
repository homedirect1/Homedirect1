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

namespace Ced\Affiliate\Controller\Account;

/**
 * Class Index
 * @package Ced\Affiliate\Controller\Account
 */
class Index extends \Ced\Affiliate\Controller\Affiliate
{
    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * Index constructor.
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->affiliateHelper = $affiliateHelper;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        parent::__construct($context, $customerSession, $formKeyValidator, $resultForwardFactory, $resultPageFactory);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->affiliateHelper->isAffiliateEnable()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/index');
            return $resultRedirect;
        }

        if (!$this->_getSession()->isLoggedIn()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('affiliate/account/login');
            return $resultRedirect;
        }

        $affiliateModel = $this->affiliateAccountFactory->create()
            ->load($this->_customerSession->getCustomer()->getEmail(), 'customer_email');

        if (!$affiliateModel->getData()) {
            // $this->messageManager->addErrorMessage(__('Invalid index Affiliate Account'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('affiliate/account/newAccount');
            return $resultRedirect;
        }

        if ($affiliateModel->getData() && $affiliateModel->getStatus() == '2') {

            $this->messageManager->addErrorMessage(__('Your Account Is Disapproved.Contact Administrator'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/index');
            return $resultRedirect;
        }
        if ($affiliateModel->getData() && $affiliateModel->getStatus() == '0') {

            $this->messageManager->addErrorMessage(__('Your Account Is Not Approved Yet'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/index');
            return $resultRedirect;
        }
        $this->_getSession()->setAffiliateId($affiliateModel->getAffiliateId());
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('My Account'));
        return $resultPage;
    }
}
