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
 * @package     Ced_ReferralSystem
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\ReferralSystem\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class CheckSignup
 * @package Ced\ReferralSystem\Observer
 */
Class CheckSignup implements ObserverInterface
{
    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @var \Magento\Customer\Model\Customer\Mapper
     */
    protected $_customerMapper;

    /**
     * @var CustomerInterfaceFactory
     */
    protected $_customerDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $_dataObjectHelper;

    /**
     * @var \Ced\ReferralSystem\Model\TransactionFactory
     */
    protected $transactionModel;

    /**
     * @var \Ced\ReferralSystem\Model\Refersource
     */
    protected $_refersourceModel;

    /**
     * @var \Ced\ReferralSystem\Model\ReferList
     */
    protected $referListModel;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customerModel;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    const SIGNUP_STATUS_DISABLE = 0;

    const SIGNUP_STATUS_ENABLE = 1;

    const TRANSACTION_TYPE_CREDIT = 1;

    const TRANSACTION_TYPE_DEBIT = 2;

    /**
     * CheckSignup constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param RequestInterface $request
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Magento\Customer\Model\Customer\Mapper $customerMapper
     * @param \Ced\ReferralSystem\Model\TransactionFactory $transactionModel
     * @param \Ced\ReferralSystem\Model\Refersource $refersourceModel
     * @param \Ced\ReferralSystem\Model\ReferList $referListModel
     * @param \Magento\Customer\Model\Customer $customerModel
     * @param ManagerInterface $messageManager
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        RequestInterface $request,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterfaceFactory $customerDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        \Ced\ReferralSystem\Model\TransactionFactory $transactionModel,
        \Ced\ReferralSystem\Model\Refersource $refersourceModel,
        \Ced\ReferralSystem\Model\ReferList $referListModel,
        \Magento\Customer\Model\Customer $customerModel,
        ManagerInterface $messageManager,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    )
    {
        $this->request = $request;
        $this->_scopeConfig = $scopeConfig;
        $this->_date = $date;
        $this->_customerRepository = $customerRepository;
        $this->_customerMapper = $customerMapper;
        $this->_customerDataFactory = $customerDataFactory;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->transactionModel = $transactionModel;
        $this->referSourceModel = $refersourceModel;
        $this->referListModel = $referListModel;
        $this->customerModel = $customerModel;
        $this->messageManager = $messageManager;
        $this->priceHelper = $priceHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $customer = $observer->getCustomer();
            $customer_id = $customer->getId();
            $referral_id = '';
            $this->saveInvitationCode($customer_id);
            $referral_code = $this->request->getPost('referral_code');
            $referal_source = $this->request->getPost('referal_source') ? $this->request->getPost('referal_source') : 'email';
            if ($referral_code) {
                $referral_id = $this->getCustomerIdByReferralCode($referral_code);
            }

            if ($referal_source && $referral_id) {
                try {
                    $referSource = $this->referSourceModel;
                    $referSource->setData('customer_id', $referral_id);
                    $referSource->setData('referred_email', $customer->getEmail());
                    $referSource->setData('source', $referal_source);
                    $referSource->save();
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__($e->getMessage()));
                }
            }

            $signup_bonus = $this->_scopeConfig->getValue('referral/system/signup_bonus', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $referral_reward = $this->_scopeConfig->getValue('referral/system/referral_reward', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            if ($referral_id) {
                $transaction = $this->transactionModel->create();
                $transaction->setData('customer_id', $customer_id);
                $transaction->setData('description', "Joining Bonus");
                $transaction->setData('earned_amount', $signup_bonus);
                $transaction->setData('transaction_type', self::TRANSACTION_TYPE_CREDIT);
                $transaction->setData('creation_date', $this->_date->gmtDate());
                $transaction->save();
                $currencySymbol = $this->priceHelper->currency($signup_bonus, true, false);
                $this->messageManager->addSuccessMessage(__('Congratulations! you have received the joining bonus of %1', $currencySymbol));

                $transaction = $this->transactionModel->create();
                $transaction->setData('customer_id', $referral_id);
                $transaction->setData('description', "Referral Reward For-" . $customer->getEmail());
                $transaction->setData('creation_date', $this->_date->gmtDate());
                $transaction->setData('earned_amount', $referral_reward);
                $transaction->setData('transaction_type', self::TRANSACTION_TYPE_CREDIT);
                $transaction->save();

                $referred_friends_model = $this->referListModel;
                $referred_friends = $referred_friends_model->getCollection()
                    ->addFieldToFilter('referred_email', $customer->getEmail())
                    ->addFieldToFilter('customer_id', $referral_id)->getFirstItem();

                if ($referred_friends && $referred_friends->getId()) {
                    $referred_friends_model->load($referred_friends->getId());
                    $referred_friends_model->setData('signup_status', self::SIGNUP_STATUS_ENABLE);
                    $referred_friends_model->setData('signup_date', $this->_date->gmtDate());
                    $referred_friends_model->setData('amount', $referral_reward);
                    $referred_friends_model->save();
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }
        return $this;
    }

    /**
     * @param $customer_id
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function saveInvitationCode($customer_id)
    {
        $customerData = $this->request->getParams();
        $customerId = $customer_id;
        $savedCustomerData = $this->_customerRepository->getById($customerId);
        $customerm = $this->_customerDataFactory->create();
        $customerData = array_merge($this->_customerMapper->toFlatArray($savedCustomerData), $customerData);
        $customerData['id'] = $customerId;
        $this->_dataObjectHelper->populateWithArray(
            $customerm,
            $customerData,
            '\Magento\Customer\Api\Data\CustomerInterface'
        );
        try {
            $this->_customerRepository->save($customerm);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }
    }

    /**
     * @param $referral_code
     * @return mixed
     */
    public function getCustomerIdByReferralCode($referral_code)
    {
        $customer = $this->customerModel->getCollection()->addAttributeToFilter('invitation_code', $referral_code)
            ->getFirstItem();
        $customerId = $customer->getId();
        return $customerId;
    }
}
