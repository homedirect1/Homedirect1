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

use Magento\Framework\UrlFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Save
 * @package Ced\Affiliate\Controller\Account
 */
class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_custmerSesion;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlModel;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * Save constructor.
     * @param UrlFactory $urlFactory
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     */
    public function __construct(
        UrlFactory $urlFactory,
        StoreManagerInterface $storeManager,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
    )
    {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->_custmerSesion = $session;
        $this->urlModel = $urlFactory->create();
        $this->affiliateHelper = $affiliateHelper;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->_custmerSesion->isLoggedIn()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */

            $resultRedirect->setPath('*/account/login');
            return $resultRedirect;
        }
        $affiliateId = rand();
        $affiliateurl = $this->urlModel->getUrl('', array('_query' => array('affiliate' => $affiliateId)));
        $data = $this->getRequest()->getPostValue();
        $postFiles = $this->getRequest()->getFiles();

        $document = ['0' => 'identityfile', '1' => 'addressfile'/*,'2'=>'companyfile'*/];
        if (!empty($postFiles)) {
            foreach ($postFiles as $key => $file) {
                if (!in_array($key, $document) && empty($file['error'])) {
                    $document[(count($document)) + 1] = $key;
                }
            }
        }
        $uploadDocument = $this->affiliateHelper->uploadDocument($document);
        if (!$uploadDocument) {
            $this->messageManager->addErrorMessage(__('Error In Uploading Document'));
            $resultRedirect->setPath('*/account/newSignup');
            return $resultRedirect;

        }

        $affiliateSave = $this->affiliateAccountFactory->create();
        $affiliateSave->setCustomerId($data['customer_id']);
        $affiliateSave->setCustomerEmail($this->_custmerSesion->getCustomer()->getEmail());
        $affiliateSave->setCustomerName($this->_custmerSesion->getCustomer()->getFirstname());
        $affiliateSave->setReferralWebsite($data['referral_website']);
        $affiliateSave->setCreatedAt(time());
        $isApprovalRequired = $this->scopeConfig->getValue('affiliate/admin/approval',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($isApprovalRequired) {
            $affiliateSave->setStatus(\Ced\Affiliate\Model\AffiliateAccount::PENDING);
            $affiliateSave->setApprove(\Ced\Affiliate\Model\AffiliateAccount::PENDING);
        } else {
            $affiliateSave->setStatus(\Ced\Affiliate\Model\AffiliateAccount::APPROVE);
            $affiliateSave->setApprove(\Ced\Affiliate\Model\AffiliateAccount::APPROVE);
        }
        $affiliateSave->setAffiliateId($affiliateId);
        $affiliateSave->setAffiliateUrl($affiliateurl);
        $affiliateSave->setIdentityType($data['identity']);
        if (isset($uploadDocument['document']) && $uploadDocument['document']['identityfile'])
            $affiliateSave->setIdentityfile($uploadDocument['document']['identityfile']);

        if (isset($uploadDocument['document']) && $uploadDocument['document']['addressfile'])
            $affiliateSave->setAddressfile($uploadDocument['document']['addressfile']);

        if (isset($uploadDocument['document']['companyfile']))
            $affiliateSave->setCompanyfile($uploadDocument['document']['companyfile']);

        $affiliateSave->save();

        if ($isApprovalRequired) {
            $this->messageManager->addSuccessMessage(__('Thank you for registering with %1. Your Account Is Under Admin Approval.On Approval You Will Receive an Email', $this->storeManager->getStore()->getFrontendName()));
            $resultRedirect->setPath('customer/account/index');
            return $resultRedirect;
        } else {
            $this->messageManager->addSuccessMessage(__('Thank you for registering'));
            $resultRedirect->setPath('affiliate/account/index');
            return $resultRedirect;
        }

    }
}
