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

use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\FormFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory;
use Magento\Framework\Escaper;
use \Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\State\UserLockedException;
use \Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\SecurityViolationException;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * Class Account
 * @package Ced\Affiliate\Model\Api\Affiliate
 */
class Account extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var Session
     */
    protected $getSession;
    /**
     * @var
     */
    protected $eventManager;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var
     */
    protected $mobiconnectHelper;
    /**
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

    /** @var Escaper */
    protected $escaper;
    /**
     *
     */
    const EMAIL_RESET = 'email_reset';
    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $localeLists;
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;
    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;
    /** @var AccountManagementInterface */
    protected $customerAccountManagement;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var QuoteIdMaskFactory
     */
    protected $idMaskFactory;

    /**
     * @var AddressFactory
     */
    protected $addressFactory;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Address\CollectionFactory
     */
    protected $addressCollectionFactory;

    /**
     * @var \Magento\Customer\Model\CustomerRegistryFactory
     */
    protected $customerRegistryFactory;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $blockFactory;

    /**
     * @var \Magento\Customer\Model\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var AttributeFactory
     */
    protected $entityAttributeFactory;

    /**
     * Account constructor.
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param CollectionFactory $customerCollectionFactory
     * @param QuoteIdMaskFactory $idMaskFactory
     * @param AddressFactory $addressFactory
     * @param FormFactory $formFactory
     * @param \Magento\Customer\Model\ResourceModel\Address\CollectionFactory $addressCollectionFactory
     * @param \Magento\Customer\Model\CustomerRegistryFactory $customerRegistryFactory
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param \Magento\Customer\Model\AttributeFactory $attributeFactory
     * @param AttributeFactory $entityAttributeFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Session $customerSession
     * @param Escaper $escaper
     * @param SubscriberFactory $subscriberFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param AccountManagementInterface $customerAccountManagement
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Quote\Model\QuoteIdMaskFactory $idMaskFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\FormFactory $formFactory,
        \Magento\Customer\Model\ResourceModel\Address\CollectionFactory $addressCollectionFactory,
        \Magento\Customer\Model\CustomerRegistryFactory $customerRegistryFactory,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Customer\Model\AttributeFactory $attributeFactory,
        \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory $entityAttributeFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Session $customerSession,
        Escaper $escaper,
        SubscriberFactory $subscriberFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        AccountManagementInterface $customerAccountManagement,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->getSession = $customerSession;
        $this->addressRepository = $addressRepository;
        $this->storeManager = $storeManager;
        $this->localeLists = $localeLists;
        $this->subscriberFactory = $subscriberFactory;
        $this->quoteRepository = $quoteRepository;
        $this->escaper = $escaper;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerFactory = $customerFactory;
        $this->quoteFactory = $quoteFactory;
        $this->customerRepository = $customerRepository;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->idMaskFactory = $idMaskFactory;
        $this->addressFactory = $addressFactory;
        $this->formFactory = $formFactory;
        $this->addressCollectionFactory = $addressCollectionFactory;
        $this->customerRegistryFactory = $customerRegistryFactory;
        $this->blockFactory = $blockFactory;
        $this->attributeFactory = $attributeFactory;
        $this->entityAttributeFactory = $entityAttributeFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

    }

    /**
     * Customer Registration Method
     *
     * @param String $customer Customer
     *
     * @return $data
     */
    public function customerRegister($customer)
    {
        $customercart_id = isset($customer['cart_id']) ? $customer['cart_id'] : 0;
        $data = array();
        $customerRegistered = $this->getCustomer($customer ['email']);
        $store = $this->storeManager->getStore();
        $gender = 'guest';
        $name = 'guest';
        if ($customerRegistered->getId()) {
            $error = __('There is already an account with this email address');
            $data = array(
                'data' => array(
                    'customer' => array(

                        'message' => $error,
                        'status' => 'error',
                        'gender' => $gender,
                        'name' => $name

                    )
                )
            );
            return $data;
        }
        $customers = $this->customerFactory->create()
            ->setData($customer);
        $customers->setPassword($customer['password']);
        try {
            $customers->save();

            if (isset($customer['is_subscribed']) && $customer['is_subscribed']) {
                $this->subscriberFactory->create()->subscribeCustomerById($customers->getId());
            }
            $session = $this->getSession;

            if ($customers->isConfirmationRequired()) {
                $customers->sendNewAccountEmail('confirmation', $session->getBeforeAuthUrl(), $store->getId());
                $message = __('Account confirmation is required. Please, check your email for the confirmation link.');
                $data = array(
                    'data' => array(
                        'customer' => array(

                            'customer_id' => $customers->getId(),
                            'isConfirmationRequired' => 'YES',
                            'status' => 'success',
                            'message' => $message,
                            'gender' => $gender,
                            'name' => $name

                        )
                    )
                );
            } else {
                try {
                    $isJustConfirmed = false;
                    $customers->sendNewAccountEmail($isJustConfirmed ? 'confirmed' : 'registered', '', $store->getId());
                } catch (\Exception $e) {
                    $message = __("Thank you for registering with " . $store->getName() . " store. Mail Not Send. ");
                    $data = array(
                        'data' => array(
                            'customer' => array(

                                'customer_id' => $customers->getId(),
                                'isConfirmationRequired' => 'NO',
                                'status' => 'success',
                                'message' => $message,
                                'gender' => $gender,
                                'name' => $name

                            )
                        )
                    );
                    return $data;
                }
                $message = __("Thank you for registering with " . $store->getName() . " store");
                $customer = $this->customerFactory->create()->load($customers->getId());

                $session->setCustomerAsLoggedIn($customer);
                if ($customer->getId()) {
                    switch ($customer->getData('gender')) {
                        case '1':
                            $gender = 'male';
                            break;
                        case '2':
                            $gender = 'female';
                            break;
                        case '3':
                            $gender = 'guest';
                            break;
                        default :
                            $gender = 'guest';
                    }
                    $name = $customer->getData('firstname');
                }
                $cus = array('customer_id' => $customers->getId());
                try {
                    $customerquote = $this->quoteRepository->getForCustomer($customers->getId());
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $customerquote = $this->quoteFactory->create();
                    $customerquote->setStore($store);
                    $customer = $this->customerRepository->getById($customers->getEntityId());
                    $customerquote->setCurrency();
                    $customerquote->assignCustomer($customer);
                }
                if ($customercart_id) {
                    $store = $this->storeManager->getStore();
                    try {
                        $guestquote = $this->quoteRepository->get($customercart_id);
                    } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                        $guestquote = $this->quoteFactory->create();
                        $guestquote->setStore($store);
                    }

                    $customerquote->merge($guestquote);
                    $customerquote->collectTotals()->save();
                }
                $data = array(
                    'data' => array(
                        'customer' => array(

                            'customer_id' => $customers->getId(),
                            'isConfirmationRequired' => 'NO',
                            'status' => 'success',
                            'message' => $message,
                            'gender' => $gender,
                            'name' => $name

                        )
                    )
                );
            }
            return $data;
        } catch (\Exception $e) {
            $message = __($e->getMessage());
            $data = array(
                'data' => array(
                    'customer' => array(

                        'message' => $message,
                        'status' => 'exception',
                        'gender' => $gender,
                        'name' => $name

                    )
                )
            );
            return $data;
        } catch (\InputException $e) {
            $message = __($e->getMessage());
            $data = array(
                'data' => array(
                    'customer' => array(

                        'message' => $message,
                        'status' => 'exception',
                        'gender' => $gender,
                        'name' => $name

                    )
                )
            );
            return $data;
        } catch (\LocalizedException $e) {
            $message = __($e->getMessage());
            $data = array(
                'data' => array(
                    'customer' => array(

                        'message' => $message,
                        'status' => 'exception',
                        'gender' => $gender,
                        'name' => $name

                    )
                )
            );
            return $data;
        }
    }

    /**
     * getCustomer
     *
     * @param String $email Email
     *
     * @return $customer
     */
    public function getCustomer($email)
    {
        $customer = $this->customerCollectionFactory->create()
            ->addAttributeToFilter('email', $email)
            ->addAttributeToSelect('*')
            ->getFirstItem();
        return $customer;
    }

    /**
     * customerLogin
     *
     * @param String $data Data
     *
     * @return $data
     */
    public function customerLogin($data)
    {
        $session = $this->getSession;
        $gender = 'guest';
        $name = 'guest';

        if ($data) {
            $login ['username'] = isset($data ['email']) ? $data ['email'] : '';
            $login ['password'] = isset($data ['password']) ? $data ['password'] : '';
            if (!empty($login ['username']) && !empty($login['password'])) {
                try {
                    $customer = $this->customerAccountManagement
                        ->authenticate($login['username'], $login['password']);
                    $session->setCustomerDataAsLoggedIn($customer);
                    $customerId = $session->getCustomer()->getId();
                    $secureHash = new \Magento\Framework\DataObject();
                    $secureHash->setCustomerId($customerId);
                    $dats = $this->eventManager->dispatch(
                        'login_hash_event',
                        ['mobi_app_model' => $this, 'customer' => $secureHash]
                    );
                    $hash = $secureHash->getHash();
                    try {
                        $customerquote = $this->quoteRepository->getForCustomer($customer->getId());
                    } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                        $customerquote = $this->quoteFactory->create();
                        $customerquote->setStore($store);
                        $customerd = $this->customerRepository->getById($customer->getEntityId());
                        $customerquote->setCurrency();
                        $customerquote->assignCustomer($customerd);
                    }

                    if ($customer->getId()) {
                        $registeredCustomer = $this->getCustomer($login ['username']);
                        switch ($registeredCustomer->getData('gender')) {
                            case '1':
                                $gender = 'male';
                                break;
                            case '2':
                                $gender = 'female';
                                break;
                            case '3':
                                $gender = 'guest';
                                break;
                            default :
                                $gender = 'guest';
                        }
                        $name = $registeredCustomer->getData('firstname');
                    }
                    $getSummaryCount = 0;
                    $mask_id = 0;
                    if (isset($data['cart_id'])) {
                        $store = $this->storeManager->getStore();
                        try {
                            $guestquote = $this->quoteRepository->get($data['cart_id']);
                            $customerquote->merge($guestquote);
                            $customerquote->collectTotals()->save();
                            $getSummaryCount = $customerquote->getItemsCount();
                            /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
                            $quoteIdMask = $this->idMaskFactory->create()
                                ->load($customerquote->getEntityId(), 'quote_id');
                            if ($quoteIdMask->getMaskedId() === null) {
                                $quoteIdMask->setQuoteId($customerquote->getEntityId())->save();
                            }
                            $mask_id = $customerquote->getEntityId();
                            if ($quoteIdMask && $quoteIdMask->getMaskedId())
                                $mask_id = $quoteIdMask->getMaskedId();

                        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                            $error = __($e->getMessage());
                            $data = array(
                                'data' => array(
                                    'customer' => array(
                                        array(
                                            'message' => $error,
                                            'gender' => $gender,
                                            'name' => $name,
                                            'status' => 'exception'
                                        )
                                    )
                                )
                            );
                            return $data;
                        }

                    }
                    $data = array(
                        'data' => array(
                            'customer' => array(
                                array(
                                    'customer_id' => $customerId,
                                    'hash' => $hash,
                                    'cart_summary' => $getSummaryCount,
                                    'mask_id' => $mask_id,
                                    'gender' => $gender,
                                    'name' => $name,
                                    'status' => 'success'
                                )
                            )
                        )
                    );
                } catch (EmailNotConfirmedException $e) {
                    $error = __('This account is not confirmed.');
                    $data = array(
                        'data' => array(
                            'customer' => array(
                                array(
                                    'message' => $error,
                                    'gender' => $gender,
                                    'name' => $name,
                                    'status' => 'exception'
                                )
                            )
                        )
                    );
                    return $data;
                } catch (UserLockedException $e) {
                    $error = __(
                        'The account is locked. Please wait and try again or contact %1.',
                        $this->getScopeConfig()->getValue('contact/email/recipient_email')
                    );
                    $data = array(
                        'data' => array(
                            'customer' => array(
                                array(
                                    'message' => $error,
                                    'gender' => $gender,
                                    'name' => $name,
                                    'status' => 'exception'
                                )
                            )
                        )
                    );
                    return $data;
                } catch (AuthenticationException $e) {
                    $error = __('Invalid login or password.');
                    $data = array(
                        'data' => array(
                            'customer' => array(
                                array(
                                    'message' => $error,
                                    'gender' => $gender,
                                    'name' => $name,
                                    'status' => 'exception'
                                )
                            )
                        )
                    );
                    return $data;
                } catch (\Exception $e) {
                    $error = __('An unspecified error occurred. Please contact us for assistance.');
                    $data = array(
                        'data' => array(
                            'customer' => array(
                                array(
                                    'message' => $error,
                                    'gender' => $gender,
                                    'name' => $name,
                                    'status' => 'exception'
                                )
                            )
                        )
                    );
                    return $data;
                }
            } else {
                $error = __('Login And Password are required');
                $data = array(
                    'data' => array(
                        'customer' => array(
                            array(
                                'message' => $error,
                                'gender' => $gender,
                                'name' => $name,
                                'status' => 'exception'
                            )
                        )
                    )
                );
                return $data;
            }
        } else {
            $error = __('Login And Password are required');
            $data = array(
                'data' => array(
                    'customer' => array(
                        array(
                            'message' => $error,
                            'gender' => $gender,
                            'name' => $name,
                            'status' => 'exception'
                        )
                    )
                )
            );
            return $data;
        }
        return $data;
    }

    /**
     * saveCustomerAddress
     *
     * @param String $data Data
     *
     * @return $data
     */
    public function saveCustomerAddress($data)
    {
        // Save data

        if ($data) {
            $session = $this->getSession;
            $customer = $session->getCustomer();
            $address = $this->addressFactory->create();
            $addressId = isset($data ['address_id']) ? $data ['address_id'] : 0;
            if ($addressId) {
                $existsAddress = $this->getAddressById($addressId);
                if ($existsAddress->getId() && $existsAddress->getCustomerId() == $data ['customer']) {
                    $address->setId($existsAddress->getId());
                }
            }
            $errors = array();
            $addressForm = $this->formFactory->create();
            $addressForm->setFormCode('customer_address_edit')->setEntity($address);
            $addressErrors = $addressForm->validateData($data);
            if ($addressErrors !== true) {
                $errors = $addressErrors;
            }
            try {
                $addressForm->compactData($data);
                $address->setCustomerId($data['customer'])->setisDefaultBilling(false)->setisDefaultShipping(false);
                $addressErrors = $address->validate();
                if ($addressErrors !== true) {
                    $errors = array_merge($errors, $addressErrors);
                }
                if (count($errors) === 0) {
                    $address->save();
                    $addressId = $address->getId();
                    $data = array(
                        'data' => array(
                            'customer' => array(
                                array(
                                    'message' => 'Customer Address has been updated',
                                    'status' => 'success',
                                    'address_id' => $addressId
                                )
                            )
                        )
                    );
                    return $data;
                } else {
                    foreach ($errors as $errorMessage) {
                        $message [] = $errorMessage;
                    }
                    $data = array(
                        'data' => array(
                            'customer' => array(
                                array(
                                    'message' => $message,
                                    'status' => 'error'
                                )
                            )
                        )
                    );
                    return $data;
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $data = array(
                    'data' => array(
                        'customer' => array(
                            array(
                                'message' => $e->getMessage(),
                                'status' => 'error'
                            )
                        )
                    )
                );
                return $data;
            } catch (\Exception $e) {
                $data = array(
                    'data' => array(
                        'customer' => array(
                            array(
                                'message' => $e->getMessage(),
                                'status' => 'error'
                            )
                        )
                    )
                );
                return $data;
            }
        }
    }

    /**
     * Retrieve customer address by address id
     *
     * @param int $addressId addressId
     *
     * @return Mage_Customer_Model_Address
     */
    public function getAddressById($addressId)
    {
        return $this->addressFactory->create()->load($addressId);
    }

    /**
     * Retrieve customer address by address
     *
     * @param String $data data
     *
     * @return $data
     */
    public function getCustomerAddress($data)
    {
        $customer = $this->customerFactory->create()
            ->load($data['customer']);
        $collection = $this->addressCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->setCustomerFilter($customer);
        $collection->setOrder('entity_id', 'DESC');
        $customerAddress = array();
        foreach ($collection as $address) {
            $customerAddress [] = array(
                'firstname' => $address ['firstname'],
                'lastname' => $address ['lastname'],
                'street' => $address ['street'],
                'city' => $address ['city'],
                'region_id' => $address ['region_id'],
                'region' => $address ['region'],
                'country' => $this->localeLists->getCountryTranslation($address ['country_id']),
                'pincode' => $address ['postcode'],
                'phone' => $address ['telephone'],
                'address_id' => $address ['entity_id']
            );
        }
        if (count($customerAddress) > 0) {
            $data = array(
                'data' => array(
                    'address' => $customerAddress,
                    'status' => 'success',
                    'customer_id' => $data ['customer']
                )
            );
        } else {
            $data = array(
                'data' => array(
                    'status' => 'no_address'
                )
            );
        }
        return $data;
    }

    /**
     * Delete customer address by address
     *
     * @param String $data data
     *
     * @return $data
     */
    public function deleteCustomerAddress($data)
    {
        $addressId = $data ['address_id'];
        if ($addressId) {
            try {
                $address = $this->addressRepository->getById($addressId);
                if ($address->getCustomerId() != $data ['customer']) {
                    $data = array(
                        'data' => array(
                            'customer' => array(
                                array(
                                    'message' => 'The address does not belong to this customer.',
                                    'status' => 'success'
                                )
                            )
                        )
                    );
                    return $data;
                }
                $this->addressRepository->deleteById($addressId);
                $data = array(
                    'data' => array(
                        'customer' => array(
                            array(
                                'message' => 'The address has been deleted.',
                                'status' => 'success'
                            )
                        )
                    )
                );
                return $data;
            } catch (\Exception $e) {
                $data = array(
                    'data' => array(
                        'customer' => array(
                            array(
                                'message' => $e->getMessage(),
                                'status' => 'exception'
                            )
                        )
                    )
                );
                return $data;
            }
        }
        return $data;
    }

    /**
     * logoutCustomer
     *
     * @param String $data data
     *
     * @return $data
     */
    public function logoutCustomer($data)
    {
        $session = $this->getSession;
        $session->logout();
        $data = array(
            'data' => array(
                'customer' => array(
                    array(
                        'message' => 'The customer has been successfully logged out.',
                        'status' => 'success'
                    )
                )
            )
        );
        return $data;
    }

    /**
     * forgotPassword
     *
     * @param String $data data
     *
     * @return $data
     */
    public function forgotPassword($data)
    {
        $email = $data['email'];
        if ($email) {
            if (!\Zend_Validate::is($email, 'EmailAddress')) {
                $data = array(
                    'data' => array(
                        'customer' => array(
                            array(
                                'message' => 'Invalid Email Address',
                                'status' => 'error'
                            )
                        )
                    )
                );
                return $data;
            }
            $customer = $this->customerFactory->create()
                ->setWebsiteId($this->storeManager->getStore()->getWebsiteId())
                ->loadByEmail($email);
            if ($customer->getId()) {
                try {
                    $this->customerAccountManagement->initiatePasswordReset(
                        $email,
                        Account::EMAIL_RESET
                    );
                    $data = array(
                        'data' => array(
                            'customer' => array(
                                array(
                                    'message' => __(
                                        'If there is an account associated with %1 you will
                                        receive an email with a link
                                        to reset your password.',
                                        $this->escaper->escapeHtml($email)
                                    ),
                                    'status' => 'success'
                                )
                            )
                        )
                    );
                    return $data;
                } catch (NoSuchEntityException $exception) {
                    $data = array(
                        'data' => array(
                            'customer' => array(
                                array(
                                    'message' => __($exception->getMessage()),
                                    'status' => 'error'
                                )
                            )
                        )
                    );
                    return $data;
                } catch (SecurityViolationException $exception) {
                    $data = array(
                        'data' => array(
                            'customer' => array(
                                array(
                                    'message' => __($exception->getMessage()),
                                    'status' => 'error'
                                )
                            )
                        )
                    );
                    return $data;
                } catch (\Exception $exception) {

                    $data = array(
                        'data' => array(
                            'customer' => array(
                                array(
                                    'message' => __($exception->getMessage()),
                                    'status' => 'error'
                                )
                            )
                        )
                    );
                    return $data;
                }
            } else {
                $data = array(
                    'data' => array(
                        'customer' => array(
                            array(
                                'message' => 'This email address was not found in our records.',
                                'status' => 'error'
                            )
                        )
                    )
                );
                return $data;
            }
        } else {
            $data = array(
                'data' => array(
                    'customer' => array(
                        array(
                            'message' => 'Customer email not specified.',
                            'status' => 'error'
                        )
                    )
                )
            );
            return $data;
        }
    }

    /**
     * updateProfile
     *
     * @param String $data data
     *
     * @return $data
     */
    public function updateProfile($data)
    {
        $gender = 'guest';
        $firstname = 'guest';
        $lastname = 'guest';
        $message = "Profile Updated Successfully";
        if (isset($data['request']) && $data['request']) {

            $customerRegistered = $this->getCustomer($data ['email']);
            if ($customerRegistered->getId()) {
                switch ($customerRegistered->getData('gender')) {
                    case '1':
                        $gender = 'male';
                        break;
                    case '2':
                        $gender = 'female';
                        break;
                    case '3':
                    case null:
                        $gender = 'guest';
                        break;
                    default :
                        $gender = 'guest';
                }
                $firstname = $customerRegistered->getFirstname();
                $lastname = $customerRegistered->getLastname();
                $data = array(
                    'data' => array(
                        'customer' => array(
                            array(
                                'message' => $message,
                                'gender' => $gender,
                                'firstname' => $firstname,
                                'lastname' => $lastname,
                                'status' => 'success'
                            )
                        )
                    )
                );
                return $data;
            }
        }
        $customer = false;
        if (isset($data ['customer']) && $data ['customer'])
            $customer = $this->customerFactory->create()->load($data ['customer']);
        if ($customer && isset($data ['change_password']) && $data ['change_password']) {
            $currPass = isset($data ['old_password']) ? $data ['old_password'] : '';
            $newPass = isset($data ['new_password']) ? $data ['new_password'] : '';
            $confPass = isset($data ['confirm_password']) ? $data ['confirm_password'] : '';
            if (empty($currPass) || empty($newPass) || empty($confPass)) {
                $data = array(
                    'data' => array(
                        'customer' => array(
                            array(
                                'message' => 'Password fields cannot be empty.',
                                'gender' => $gender,
                                'firstname' => $firstname,
                                'lastname' => $lastname,
                                'status' => 'error'
                            )
                        )
                    )
                );
                return $data;
            }

            if ($newPass != $confPass) {
                $data = array(
                    'data' => array(
                        'customer' => array(
                            array(
                                'message' => 'Please make sure your passwords match.',
                                'gender' => $gender,
                                'firstname' => $firstname,
                                'lastname' => $lastname,
                                'status' => 'error'
                            )
                        )
                    )
                );
                return $data;
            }
            $oldPass = $customer->getPasswordHash();

            if (strpos($oldPass, ':') == true) {
                list (, $salt) = explode(':', $oldPass);
            } else {
                $salt = false;
            }

            if ($customer->hashPassword($currPass, $salt) == $oldPass) {
                $customer->setPassword($newPass);
                $message = "Password Updated Successfully";
            } else {
                $data = array(
                    'data' => array(
                        'customer' => array(
                            array(
                                'message' => 'Invalid old password.',
                                'gender' => $gender,
                                'firstname' => $firstname,
                                'lastname' => $lastname,
                                'status' => 'error'
                            )
                        )
                    )
                );
                return $data;
            }
        }

        if ($customer && $customer->getId()) {
            $data['firstname'] = isset($data['firstname']) ? $data['firstname'] : '';
            $data['lastname'] = isset($data['lastname']) ? $data['lastname'] : $data['firstname'];
            $data['gender'] = isset($data['gender']) ? $data['gender'] : '';
            $customer->setFirstname($data['firstname'])->setLastname($data['lastname'])->setGender($data['gender'])->setEmail($data['email']);
            try {
                $customer->save();
            } catch (\Exception $e) {
                $data = array(
                    'data' => array(
                        'customer' => array(
                            array(
                                'message' => $e->getMessage(),
                                'gender' => '',
                                'firstname' => '',
                                'lastname' => '',
                                'status' => 'error'
                            )
                        )
                    )
                );
                return $data;
            }


            switch ($customer->getData('gender')) {
                case '1':
                    $gender = 'male';
                    break;
                case '2':
                    $gender = 'female';
                    break;
                case '3':
                case null:
                    $gender = 'guest';
                    break;
                default :
                    $gender = 'guest';
            }
            $firstname = $customer->getFirstname();
            $lastname = $customer->getLastname();
            $data = array(
                'data' => array(
                    'customer' => array(
                        array(
                            'message' => $message,
                            'gender' => $gender,
                            'firstname' => $firstname,
                            'lastname' => $lastname,
                            'status' => 'success'
                        )
                    )
                )
            );
            return $data;
        } else {
            $data = array(
                'data' => array(
                    'customer' => array(
                        array(
                            'message' => 'Customer Not Exist',
                            'gender' => $gender,
                            'firstname' => $firstname,
                            'lastname' => $lastname,
                            'status' => 'error'
                        )
                    )
                )
            );
            return $data;
        }
        //}
    }

    /**
     * getRequiredFields
     *
     * @param String $params params
     *
     * @return void
     */
    public function getRequiredFields($params)
    {

        $customer_id = isset($params['customer_id']) ? $params['customer_id'] : 0;
        $customer = '';
        $selected_gender = '';
        $selected_middlename = '';
        $selected_taxvat = '';
        $selected_prefix = '';
        $selected_suffix = '';
        $selected_dob = '';

        if ($customer_id)
            $customer = $this->customerRegistryFactory->create()->retrieve($customer_id);
        if ($customer && $customer->getId()) {
            $selected_gender = $customer->getGender();
            $selected_middlename = $customer->getMiddlename();
            $selected_prefix = $customer->getPrefix();
            $selected_suffix = $customer->getSuffix();
            $selected_taxvat = $customer->getTaxVat();
            $selected_dob = $customer->getDob();
        }


        $data = [];
        $_dob = $this->blockFactory->create()->createBlock('Magento\Customer\Block\Widget\Dob');
        if ($_dob) {
            $data['0']['dob'] = $_dob->isEnabled();
            $data['0']['value'] = $selected_dob;
            $data['0']['type'] = 'datepicker';
            $data['0']['label'] = 'DOB';
            $data['0']['name'] = 'dob';
        }

        $_taxvat = $this->blockFactory->create()->createBlock('Magento\Customer\Block\Widget\Taxvat');
        if ($_taxvat) {
            $data['1']['taxvat'] = $_taxvat->isEnabled();
            $data['1']['value'] = $selected_taxvat;
            $data['1']['type'] = 'text';
            $data['1']['label'] = 'Tax/VAT number';
            $data['1']['name'] = 'taxvat';
        }

        $_gender = $this->blockFactory->create()->createBlock('Magento\Customer\Block\Widget\Gender');
        if ($_gender) {
            $data['2']['gender'] = $_gender->isEnabled();
            $attributeModel = $this->attributeFactory->create()
                ->load($this->entityAttributeFactory->create()->getIdByCode('customer', 'gender'));
            $data['2']['gender_options'] = $attributeModel->getSource()->getAllOptions();
            $data['2']['value'] = $selected_gender;
            $data['2']['type'] = 'dropdown';
            $data['2']['label'] = 'Gender';
            $data['2']['name'] = 'gender';
        }
        $name = $this->blockFactory->create()->createBlock('Magento\Customer\Block\Widget\Name');

        $prefix = $name->showPrefix();
        if ($prefix) {
            $data['3']['prefix'] = $prefix;
            $data['3']['prefix_options'] = $name->getPrefixOptions();
            $data['3']['value'] = $selected_prefix;
            $data['3']['type'] = 'dropdown';
            $data['3']['label'] = 'Prefix';
            $data['3']['name'] = 'prefix';
        }

        $middle = $name->showMiddlename();
        if ($middle) {
            $data['4']['middlename'] = $middle;
            $data['4']['value'] = $selected_middlename;
            $data['4']['type'] = 'text';
            $data['4']['label'] = 'Middle Name';
            $data['4']['name'] = 'middlename';
        }

        $suffix = $name->showSuffix();
        if ($suffix) {
            $data['5']['suffix'] = $suffix;
            $data['5']['suffix_options'] = $name->getSuffixOptions();
            $data['5']['value'] = $selected_suffix;
            $data['5']['type'] = 'dropdown';
            $data['5']['label'] = 'Suffix';
            $data['5']['name'] = 'suffix';
        }

        return ['success' => true, 'data' => $data];
    }
}
