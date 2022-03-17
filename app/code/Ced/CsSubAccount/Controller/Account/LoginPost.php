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
 * @package     Ced_CsSubAccount
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Controller\Account;

use Ced\CsMarketplace\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Ced\CsMarketplace\Model\Url as CustomerUrl;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey\Validator;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LoginPost extends \Ced\CsMarketplace\Controller\Account\LoginPost
{
    /**
     * @var AccountManagementInterface
     */
    protected $vendorAccountManagement;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var VendorAcRedirect
     */
    protected $vendorAcRedirect;

    /**
     * @var VendorUrl
     */
    protected $vendorUrl;

    /**
     * @var VendorSession
     */
    protected $vendorSession;

    /**
     * @var \Ced\CsMarketplace\Model\VendorFactory
     */
    protected $vendorFactory;

    /**
     * @var \Ced\CsSubAccount\Model\CsSubAccountFactory
     */
    protected $csSubAccountFactory;

    /**
     * @var \Magento\Framework\Encryption\Encryptor
     */
    protected $encryptor;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * LoginPost constructor.
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory
     * @param \Magento\Framework\Encryption\Encryptor $encryptor
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $vendorAccountManagement
     * @param CustomerUrl $vendorHelperData
     * @param Validator $formKeyValidator
     * @param AccountRedirect $vendorAcRedirect
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Ced\CsMarketplace\Helper\Cookie $cookieData
     */
    public function __construct(
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        Context $context,
        Session $customerSession,
        AccountManagementInterface $vendorAccountManagement,
        CustomerUrl $vendorHelperData,
        Validator $formKeyValidator,
        AccountRedirect $vendorAcRedirect,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ced\CsMarketplace\Helper\Cookie $cookieData
    )
    {
        $this->vendorSession = $customerSession;
        $this->vendorAccountManagement = $vendorAccountManagement;
        $this->vendorUrl = $vendorHelperData;
        $this->_scopeConfig = $scopeConfig;
        $this->formKeyValidator = $formKeyValidator;
        $this->vendorAcRedirect = $vendorAcRedirect;
        $this->vendorFactory = $vendorFactory;
        $this->csSubAccountFactory = $csSubAccountFactory;
        $this->encryptor = $encryptor;
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $customerSession, $vendorAccountManagement, $vendorHelperData, $formKeyValidator, $vendorAcRedirect, $cookieData);
    }

    /**
     * Login post action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if ($this->vendorSession->isLoggedIn()) {
            /**
             * @var \Magento\Framework\Controller\Result\Redirect $resultRedirect
             */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('csmarketplace/vendor/index');
            return $resultRedirect;
        }
        if ($this->getRequest()->isPost()) {

            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $check = $this->vendorFactory->create()->loadByEmail($login['username']);
                    if ($check && $check->getId()) {
                        $customer = $this->vendorAccountManagement->authenticate($login['username'], $login['password']);
                        $this->vendorSession->setCustomerDataAsLoggedIn($customer);
                        $this->vendorSession->regenerateId();
                    } else {
                        if (!$this->_scopeConfig->getValue('ced_cssubaccount/general/cssubaccount_active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
                            $this->messageManager->addErrorMessage(__('Sub-vendor Account System is disabled by admin.'));
                            $this->_redirect('csmarketplace/account/login');
                            return;
                        }
                        $subvendor = $this->csSubAccountFactory->create()->load($login['username'], 'email')->getData();

                        if (!empty($subvendor) && $subvendor['status'] == \Ced\CsSubAccount\Model\CsSubAccount::ACCOUNT_APPROVE) {
                            if ($this->encryptor->decrypt($subvendor['password']) == $login['password']) {
                                $vendor = $this->vendorFactory->create()->load($subvendor['parent_vendor']);
                                $customer = $this->customerFactory->create()->load($vendor->getCustomerId());
                                $this->vendorSession->setCustomerAsLoggedIn($customer);
                                $this->vendorSession->regenerateId();
                                $this->vendorSession->setSubVendorData($subvendor);

                            } else{
                                $this->messageManager->addErrorMessage(__('Invalid login or password.'));
                            }
                        } elseif (!empty($subvendor) && $subvendor['status'] == \Ced\CsSubAccount\Model\CsSubAccount::ACCOUNT_DISAPPROVE) {
                            $this->messageManager->addErrorMessage(__('Sub-vendor account has been disapproved by the vendor.'));
                        } elseif (!empty($subvendor) && $subvendor['status'] == \Ced\CsSubAccount\Model\CsSubAccount::ACCOUNT_NEW) {
                            $this->messageManager->addErrorMessage(__('Your Sub-vendor account is under approval.'));
                        }else{
                            $this->messageManager->addErrorMessage(__('Invalid login or password.'));
                        }
                    }
                } catch (EmailNotConfirmedException $e) {
                    $value = $this->vendorUrl->getEmailConfirmationUrl($login['username']);
                    $message = __(
                        'This account is not confirmed.' .
                        ' <a href="%1">Click here</a> to resend confirmation email.',
                        $value
                    );
                    $this->messageManager->addErrorMessage($message);
                    $this->vendorSession->setUsername($login['username']);
                } catch (AuthenticationException $e) {
                    $message = __('Invalid login or password.');
                    $this->messageManager->addErrorMessage($message);
                    $this->vendorSession->setUsername($login['username']);
                } catch (\Exception $e) {
                    print_r($e->getMessage());
                    die;
                    $this->messageManager->addErrorMessage(__('Invalid login or password.'));
                }
            } else {
                $this->messageManager->addErrorMessage(__('A login and a password are required.'));
            }
        }
        return $this->vendorAcRedirect->getRedirect();
    }
}
