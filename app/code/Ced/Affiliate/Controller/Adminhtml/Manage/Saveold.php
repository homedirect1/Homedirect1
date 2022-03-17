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

namespace Ced\Affiliate\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\View\Result\PageFactory;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Customer\Model\Address\Mapper;
use Magento\Customer\Api\AddressRepositoryInterface;


/**
 * Class Save
 * @package Ced\Affiliate\Controller\Adminhtml\Manage
 */
class Save extends \Magento\Customer\Controller\Adminhtml\Index
{

    protected $_fileFactory;

    private $emailNotification;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * Save constructor.
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Magento\Framework\UrlInterface $url
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Customer\Model\Metadata\FormFactory $formFactory
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Customer\Helper\View $viewHelper
     * @param \Magento\Framework\Math\Random $random
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param Mapper $addressMapper
     * @param AccountManagementInterface $customerAccountManagement
     * @param AddressRepositoryInterface $addressRepository
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param AddressInterfaceFactory $addressDataFactory
     * @param \Magento\Customer\Model\Customer\Mapper $customerMapper
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectFactory $objectFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Magento\Backend\Model\Session $backendSession,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Magento\Framework\UrlInterface $url,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Customer\Helper\View $viewHelper,
        \Magento\Framework\Math\Random $random,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        Mapper $addressMapper,
        AccountManagementInterface $customerAccountManagement,
        AddressRepositoryInterface $addressRepository,
        CustomerInterfaceFactory $customerDataFactory,
        AddressInterfaceFactory $addressDataFactory,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        DataObjectFactory $objectFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->backendSession = $backendSession;
        $this->affiliateHelper = $affiliateHelper;
        $this->url = $url;
        parent::__construct($context, $coreRegistry, $fileFactory, $customerFactory, $addressFactory, $formFactory, $subscriberFactory, $viewHelper, $random, $customerRepository, $extensibleDataObjectConverter, $addressMapper, $customerAccountManagement, $addressRepository, $customerDataFactory, $addressDataFactory, $customerMapper, $dataObjectProcessor, $dataObjectHelper, $objectFactory, $layoutFactory, $resultLayoutFactory, $resultPageFactory, $resultForwardFactory, $resultJsonFactory);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        $model = $this->affiliateAccountFactory->create();
        if ($data) {


            if ($this->getRequest()->getParam('id')) {
                $id = $this->getRequest()->getParam('id');
                $model->load($this->getRequest()->getParam('id'));
                $model->setStatus($data['affiliate_status']);
                $model->setApprove($data['affiliate_status']);
                $model->setCustomerName($data['affiliate_name']);
                $model->setReferralWebsite($data['referral_website']);
            } else {
                try {
                    $customer = $this->createCustomer();
                    if (!$customer) {
                        $this->backendSession->setFormData(true);
                        return $resultRedirect->setPath('*/*/newAccount');
                    }
                } catch (\Exception $e) {
                    return $resultRedirect->setPath('*/*/newAccount');
                }
                $documentField = array('0' => 'identityfile', '1' => 'addressfile', '2' => 'companyfile');
                $uploadDocument = $this->affiliateHelper->uploadAdminDocument($documentField, $customer);

                if (!$uploadDocument) {

                    $this->messageManager->addErrorMessage(__('Error In Uploading Document'));
                    $resultRedirect->setPath('*/*/newAccount');
                    return $resultRedirect;

                }

                $affiliateid = rand();
                $url = $this->url;
                $affiliateurl = $url->getUrl('', array('_query' => array('affiliate' => $affiliateid)));
                $model->setCustomerId($customer->getId());
                $model->setCustomerName($data['customer']['firstname'] . ' ' . $data['customer']['lastname']);
                $model->setReferralWebsite($data['referral_website']);
                $model->setCustomerEmail($data['customer']['email']);
                $model->setAffiliateUrl($affiliateurl);
                $model->setCreatedAt(time());
                $model->setAffiliateId($affiliateid);
                $model->setStatus($data['affiliate_status']);
                $model->setIdentityfile($uploadDocument['document']['identityfile']);
                $model->setAddressfile($uploadDocument['document']['addressfile']);
                if (isset($uploadDocument['document']['companyfile']))
                    $model->setCompanyfile($uploadDocument['document']['companyfile']);
                $model->setApprove($data['affiliate_status']);

            }
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('Account has been edited And Save Successfully'));
                $this->backendSession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/account');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while editing the request.'));
            }

            $this->_getSession()->setFormData($this->getRequest()->getPostValue());
            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        } else {
            $this->messageManager->addErrorMessage(__('No Data To Save'));
        }
        return $resultRedirect->setPath('*/*/account');
    }

    /**
     * @return bool|CustomerInterface
     */
    public function createCustomer()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $returnToEdit = false;
        $originalRequestData = $this->getRequest()->getPostValue();
        $customerId = isset($originalRequestData['customer']['entity_id'])
            ? $originalRequestData['customer']['entity_id']
            : null;
        if ($originalRequestData) {
            try {
                $password = $this->getRequest()->getParam('password');
                $confirmation = $this->getRequest()->getParam('password_confirmation');
                $this->checkPasswordConfirmation($password, $confirmation);
                // optional fields might be set in request for future processing by observers in other modules
                $customerData = $this->_extractCustomerData();
                $addressesData = $this->_extractCustomerAddressData($customerData);
                $request = $this->getRequest();
                $isExistingCustomer = (bool)$customerId;
                $customer = $this->customerDataFactory->create();
                if ($isExistingCustomer) {
                    $currentCustomer = $this->_customerRepository->getById($customerId);
                    $customerData = array_merge(
                        $this->customerMapper->toFlatArray($currentCustomer),
                        $customerData
                    );
                    $customerData['id'] = $customerId;
                }

                $this->dataObjectHelper->populateWithArray(
                    $customer,
                    $customerData,
                    '\Magento\Customer\Api\Data\CustomerInterface'
                );
                $addresses = [];
                foreach ($addressesData as $addressData) {
                    $region = isset($addressData['region']) ? $addressData['region'] : null;
                    $regionId = isset($addressData['region_id']) ? $addressData['region_id'] : null;
                    $addressData['region'] = [
                        'region' => $region,
                        'region_id' => $regionId,
                    ];
                    $addressDataObject = $this->addressDataFactory->create();
                    $this->dataObjectHelper->populateWithArray(
                        $addressDataObject,
                        $addressData,
                        '\Magento\Customer\Api\Data\AddressInterface'
                    );
                    $addresses[] = $addressDataObject;
                }

                $this->_eventManager->dispatch(
                    'adminhtml_customer_prepare_save',
                    ['customer' => $customer, 'request' => $request]
                );
                $customer->setAddresses($addresses);
                $customer->setStoreId($customerData['sendemail_store_id']);

                // Save customer
                if ($isExistingCustomer) {
                    $this->_customerRepository->save($customer);

                    $this->getEmailNotification()->credentialsChanged($customer, $currentCustomer->getEmail());

                } else {
                    $customer = $this->customerAccountManagement->createAccount($customer, $password);
                    $customerId = $customer->getId();
                }

                $isSubscribed = null;
                if ($this->_authorization->isAllowed(null)) {
                    $isSubscribed = $this->getRequest()->getPost('subscription');
                }
                if ($isSubscribed !== null) {
                    if ($isSubscribed !== 'false') {
                        $this->_subscriberFactory->create()->subscribeCustomerById($customerId);
                    } else {
                        $this->_subscriberFactory->create()->unsubscribeCustomerById($customerId);
                    }
                }

                // After save
                $this->_eventManager->dispatch(
                    'adminhtml_customer_save_after',
                    ['customer' => $customer, 'request' => $request]
                );
                $this->_getSession()->unsCustomerFormData();
                // Done Saving customer, finish save action
                $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);
                $this->messageManager->addSuccessMessage(__('You saved the customer.'));
                $returnToEdit = (bool)$this->getRequest()->getParam('back', false);
            } catch (\Magento\Framework\Validator\Exception $exception) {
                $messages = $exception->getMessages();
                if (empty($messages)) {
                    $messages = $exception->getMessage();
                }
                $this->_addSessionErrorMessages($messages);
                $this->_getSession()->setCustomerFormData($originalRequestData);
                $returnToEdit = true;
            } catch (LocalizedException $exception) {
                $this->backendSession->setFormData(true);
                $this->_addSessionErrorMessages($exception->getMessage());
                $this->_getSession()->setCustomerFormData($originalRequestData);
                $returnToEdit = true;
                return false;
            } catch (\Exception $exception) {

                $this->backendSession->setFormData(true);
                $this->messageManager->addExceptionMessage($exception, __('Something went wrong while saving the customer.'));
                $this->_getSession()->setCustomerFormData($originalRequestData);
                $returnToEdit = true;
                return false;
            }
        }

        return $customer;


    }

    /**
     * @return array
     */
    protected function _extractCustomerData()
    {
        $customerData = [];
        if ($this->getRequest()->getPost('customer')) {
            $serviceAttributes = [
                CustomerInterface::DEFAULT_BILLING,
                CustomerInterface::DEFAULT_SHIPPING,
                'confirmation',
                'sendemail_store_id',
                'extension_attributes',
            ];

            $customerData = $this->_extractData(
                $this->getRequest(),
                'adminhtml_customer',
                \Magento\Customer\Api\CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
                $serviceAttributes,
                'customer'
            );
        }

        if (isset($customerData['disable_auto_group_change'])) {
            $customerData['disable_auto_group_change'] = (int)filter_var(
                $customerData['disable_auto_group_change'],
                FILTER_VALIDATE_BOOLEAN
            );
        }

        return $customerData;
    }

    /**
     * Perform customer data filtration based on form code and form object
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param string $formCode The code of EAV form to take the list of attributes from
     * @param string $entityType entity type for the form
     * @param string[] $additionalAttributes The list of attribute codes to skip filtration for
     * @param string $scope scope of the request
     * @param \Magento\Customer\Model\Metadata\Form $metadataForm to use for extraction
     * @return array Filtered customer data
     */
    protected function _extractData(
        \Magento\Framework\App\RequestInterface $request,
        $formCode,
        $entityType,
        $additionalAttributes = [],
        $scope = null,
        \Magento\Customer\Model\Metadata\Form $metadataForm = null
    )
    {
        if ($metadataForm === null) {
            $metadataForm = $this->_formFactory->create(
                $entityType,
                $formCode,
                [],
                false,
                \Magento\Customer\Model\Metadata\Form::DONT_IGNORE_INVISIBLE
            );
        }
        $filteredData = $metadataForm->extractData($request, $scope);

        $object = $this->_objectFactory->create(['data' => $request->getPostValue()]);
        $requestData = $object->getData($scope);
        foreach ($additionalAttributes as $attributeCode) {
            $filteredData[$attributeCode] = isset($requestData[$attributeCode]) ? $requestData[$attributeCode] : false;
        }

        $formAttributes = $metadataForm->getAttributes();
        /** @var \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute */
        foreach ($formAttributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $frontendInput = $attribute->getFrontendInput();
            if ($frontendInput != 'boolean' && $filteredData[$attributeCode] === false) {
                unset($filteredData[$attributeCode]);
            }
        }

        if (empty($filteredData['extension_attributes'])) {
            unset($filteredData['extension_attributes']);
        }

        return $filteredData;
    }

    /**
     * Saves default_billing and default_shipping flags for customer address
     *
     * @param array $addressIdList
     * @param array $extractedCustomerData
     * @return array
     */
    protected function saveDefaultFlags(array $addressIdList, array & $extractedCustomerData)
    {
        $result = [];
        $extractedCustomerData[CustomerInterface::DEFAULT_BILLING] = null;
        $extractedCustomerData[CustomerInterface::DEFAULT_SHIPPING] = null;
        foreach ($addressIdList as $addressId) {
            $scope = sprintf('address/%s', $addressId);
            $addressData = $this->_extractData(
                $this->getRequest(),
                'adminhtml_customer_address',
                \Magento\Customer\Api\AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
                ['default_billing', 'default_shipping'],
                $scope
            );

            if (is_numeric($addressId)) {
                $addressData['id'] = $addressId;
            }
            // Set default billing and shipping flags to customer
            if (!empty($addressData['default_billing']) && $addressData['default_billing'] === 'true') {
                $extractedCustomerData[CustomerInterface::DEFAULT_BILLING] = $addressId;
                $addressData['default_billing'] = true;
            } else {
                $addressData['default_billing'] = false;
            }
            if (!empty($addressData['default_shipping']) && $addressData['default_shipping'] === 'true') {
                $extractedCustomerData[CustomerInterface::DEFAULT_SHIPPING] = $addressId;
                $addressData['default_shipping'] = true;
            } else {
                $addressData['default_shipping'] = false;
            }
            $result[] = $addressData;
        }
        return $result;
    }

    /**
     * Reformat customer addresses data to be compatible with customer service interface
     *
     * @param array $extractedCustomerData
     * @return array
     */
    protected function _extractCustomerAddressData(array & $extractedCustomerData)
    {
        $addresses = $this->getRequest()->getPost('address');
        $result = [];
        if (is_array($addresses)) {
            if (isset($addresses['_template_'])) {
                unset($addresses['_template_']);
            }

            $addressIdList = array_keys($addresses);
            $result = $this->saveDefaultFlags($addressIdList, $extractedCustomerData);
        }

        return $result;
    }

    /**
     * @param $password
     * @param $confirmation
     */
    protected function checkPasswordConfirmation($password, $confirmation)
    {
        if ($password != $confirmation) {
            throw new InputException(__('Please make sure your passwords match.'));
        }
    }
}
