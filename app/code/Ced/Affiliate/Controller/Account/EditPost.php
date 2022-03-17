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

use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\State\UserLockedException;

/**
 * Class EditPost
 * @package Ced\Affiliate\Controller\Account
 */
class EditPost extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * Form code for data extractor
     */
    const FORM_DATA_EXTRACTOR_CODE = 'customer_account_edit';

    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var CustomerExtractor
     */
    protected $customerExtractor;

    /**
     * @var Session
     */
    protected $session;

    /** @var EmailNotificationInterface */
    protected $emailNotification;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var AuthenticationInterface
     */
    protected $authentication;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * EditPost constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param Validator $formKeyValidator
     * @param CustomerExtractor $customerExtractor
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param AuthenticationInterface $authentication
     * @param EmailNotificationInterface $emailNotification
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerRepositoryInterface $customerRepository,
        Validator $formKeyValidator,
        CustomerExtractor $customerExtractor,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        AuthenticationInterface $authentication,
        EmailNotificationInterface $emailNotification,
        ScopeConfigInterface $scopeConfig
    )
    {
        parent::__construct($context);
        $this->session = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerRepository = $customerRepository;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerExtractor = $customerExtractor;
        $this->affiliateHelper = $affiliateHelper;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->authentication = $authentication;
        $this->emailNotification = $emailNotification;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get authentication
     *
     * @return AuthenticationInterface
     */
    private function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * Get email notification
     *
     * @return EmailNotificationInterface
     * @deprecated
     */
    private function getEmailNotification()
    {
        return $this->emailNotification;
    }

    /**
     * Change customer email or password action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $validFormKey = $this->formKeyValidator->validate($this->getRequest());
        $postData = $this->getRequest()->getPostValue();
        if ($validFormKey && $this->getRequest()->isPost()) {
            $currentCustomerDataObject = $this->getCustomerDataObject($this->session->getCustomerId());
            $customerCandidateDataObject = $this->populateNewCustomerDataObject(
                $this->_request,
                $currentCustomerDataObject
            );

            try {
                // whether a customer enabled change email option
                $this->processChangeEmailRequest($currentCustomerDataObject);

                // whether a customer enabled change password option
                $isPasswordChanged = $this->changeCustomerPassword($currentCustomerDataObject->getEmail());

                $this->customerRepository->save($customerCandidateDataObject);
                $this->getEmailNotification()->credentialsChanged(
                    $customerCandidateDataObject,
                    $currentCustomerDataObject->getEmail(),
                    $isPasswordChanged
                );

                $document = array('0' => 'identityfile', '1' => 'addressfile', '2' => 'companyfile');

                $uploadDocument = $this->affiliateHelper->uploadDocument($document);

                $affiliateaccount = $this->affiliateAccountFactory->create()
                    ->load($this->session->getCustomerId(), 'customer_id');

                if (isset($postData['email']))
                    $affiliateaccount->setCustomerEmail($postData['email']);

                $affiliateaccount->setCustomerName($postData['firstname'] . $postData['lastname']);

                if ($uploadDocument) {

                    if (isset($uploadDocument['document']['identityfile']))
                        $affiliateaccount->setIdentityfile($uploadDocument['document']['identityfile']);

                    if (isset($uploadDocument['document']['addressfile']))
                        $affiliateaccount->setAddressfile($uploadDocument['document']['addressfile']);

                    if (isset($uploadDocument['document']['companyfile']))
                        $affiliateaccount->setCompanyfile($uploadDocument['document']['companyfile']);

                }
                $affiliateaccount->save();
                $this->dispatchSuccessEvent($customerCandidateDataObject);
                $this->messageManager->addSuccessMessage(__('You saved the account information.'));
                return $resultRedirect->setPath('affiliate/account');
            } catch (InvalidEmailOrPasswordException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (UserLockedException $e) {
                $message = __(
                    'The account is locked. Please wait and try again or contact %1.',
                    $this->getScopeConfig()->getValue('contact/email/recipient_email')
                );
                $this->session->logout();
                $this->session->start();
                $this->messageManager->addErrorMessage($message);
                return $resultRedirect->setPath('affiliate/account/login');
            } catch (InputException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                foreach ($e->getErrors() as $error) {
                    $this->messageManager->addErrorMessage($error->getMessage());
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('We can\'t save the Affiliate.'));
            }

            $this->session->setCustomerFormData($this->getRequest()->getPostValue());
        }

        return $resultRedirect->setPath('*/*/edit');
    }

    /**
     * Get scope config
     *
     * @return ScopeConfigInterface
     */
    private function getScopeConfig()
    {
        return $this->scopeConfig;
    }

    /**
     * Account editing action completed successfully event
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customerCandidateDataObject
     * @return void
     */
    private function dispatchSuccessEvent(\Magento\Customer\Api\Data\CustomerInterface $customerCandidateDataObject)
    {
        $this->_eventManager->dispatch(
            'customer_account_edited',
            ['email' => $customerCandidateDataObject->getEmail()]
        );
    }

    /**
     * Get customer data object
     *
     * @param int $customerId
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    private function getCustomerDataObject($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }

    /**
     * Create Data Transfer Object of customer candidate
     *
     * @param \Magento\Framework\App\RequestInterface $inputData
     * @param \Magento\Customer\Api\Data\CustomerInterface $currentCustomerData
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    private function populateNewCustomerDataObject(
        \Magento\Framework\App\RequestInterface $inputData,
        \Magento\Customer\Api\Data\CustomerInterface $currentCustomerData
    )
    {
        $customerDto = $this->customerExtractor->extract(self::FORM_DATA_EXTRACTOR_CODE, $inputData);
        $customerDto->setId($currentCustomerData->getId());
        if (!$customerDto->getAddresses()) {
            $customerDto->setAddresses($currentCustomerData->getAddresses());
        }
        if (!$inputData->getParam('change_email')) {
            $customerDto->setEmail($currentCustomerData->getEmail());
        }

        return $customerDto;
    }

    /**
     * Change customer password
     *
     * @param string $email
     * @return boolean
     * @throws InvalidEmailOrPasswordException|InputException
     */
    protected function changeCustomerPassword($email)
    {
        $isPasswordChanged = false;
        if ($this->getRequest()->getParam('change_password')) {
            $currPass = $this->getRequest()->getPost('current_password');
            $newPass = $this->getRequest()->getPost('password');
            $confPass = $this->getRequest()->getPost('password_confirmation');
            if ($newPass != $confPass) {
                throw new InputException(__('Password confirmation doesn\'t match entered password.'));
            }

            $isPasswordChanged = $this->customerAccountManagement->changePassword($email, $currPass, $newPass);
        }

        return $isPasswordChanged;
    }

    /**
     * Process change email request
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $currentCustomerDataObject
     * @return void
     * @throws InvalidEmailOrPasswordException
     * @throws UserLockedException
     */
    private function processChangeEmailRequest(\Magento\Customer\Api\Data\CustomerInterface $currentCustomerDataObject)
    {
        if ($this->getRequest()->getParam('change_email')) {
            // authenticate user for changing email
            try {
                $this->getAuthentication()->authenticate(
                    $currentCustomerDataObject->getId(),
                    $this->getRequest()->getPost('current_password')
                );
            } catch (InvalidEmailOrPasswordException $e) {
                throw new InvalidEmailOrPasswordException(__('The password doesn\'t match this account.'));
            }
        }
    }
}
