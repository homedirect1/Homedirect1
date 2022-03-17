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

namespace Ced\Affiliate\Observer;

use Ced\Affiliate\Model\ResourceModel\AffiliateReferral\Collection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;

/**
 * Class CheckSignup
 * @package Ced\Affiliate\Observer
 */
Class CheckSignup implements ObserverInterface
{

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    const SIGNUP_STATUS_DISABLE = 0;

    const SIGNUP_STATUS_ENABLE = 1;

    const TRANSACTION_TYPE_CREDIT = 1;

    const TRANSACTION_TYPE_DEBIT = 2;

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
     * @var DataObjectHelper
     */
    protected $_dataObjectHelper;

    /**
     * @var CustomerInterfaceFactory
     */
    protected $_customerDataFactory;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * @var \Ced\Affiliate\Model\RefersourceFactory
     */
    protected $refersourceFactory;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * @var \Ced\Affiliate\Model\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var \Ced\Affiliate\Model\AffiliateReferralFactory
     */
    protected $affiliateReferralFactory;

    /**
     * CheckSignup constructor.
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Ced\Affiliate\Model\RefersourceFactory $refersourceFactory
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Ced\Affiliate\Model\TransactionFactory $transactionFactory
     * @param CollectionFactory $customerCollectionFactory
     * @param \Ced\Affiliate\Model\AffiliateReferralFactory $affiliateReferralFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param RequestInterface $request
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Magento\Customer\Model\Customer\Mapper $customerMapper
     */
    public function __construct(
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Ced\Affiliate\Model\RefersourceFactory $refersourceFactory,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Ced\Affiliate\Model\TransactionFactory $transactionFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Ced\Affiliate\Model\AffiliateReferralFactory $affiliateReferralFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        RequestInterface $request,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterfaceFactory $customerDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Magento\Customer\Model\Customer\Mapper $customerMapper
    )
    {
        $this->request = $request;
        $this->_scopeConfig = $scopeConfig;
        $this->_date = $date;
        $this->_customerRepository = $customerRepository;
        $this->_customerMapper = $customerMapper;
        $this->_customerDataFactory = $customerDataFactory;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->affiliateHelper = $affiliateHelper;
        $this->refersourceFactory = $refersourceFactory;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->transactionFactory = $transactionFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->affiliateReferralFactory = $affiliateReferralFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->affiliateHelper->isAffiliateEnable()) {

            return $this;
        }
        try {
            $referral_id = '';
            $customer = $observer->getCustomer();
            $customer_id = $customer->getId();
            $this->saveInvitationCode($customer_id);
            $referral_code = $this->request->getPost('referral_code');
            $referal_source = 'email';
            $referal_source = $this->request->getPost('referal_source');
            if ($referral_code != "") {
                $referral_id = $this->getCustomerIdByReferralCode($referral_code);
            }

            if ($referal_source != "") {
                $Refersource = $this->refersourceFactory->create();
                try {
                    $affiliateData = $this->affiliateAccountFactory->create()->load($referral_id, 'customer_id');
                    $Refersource->setData('customer_id', $referral_id);
                    $Refersource->setData('referred_email', $customer->getEmail());
                    $Refersource->setData('source', $referal_source);
                    $Refersource->setData('affiliate_id', $affiliateData->getAffiliateId());
                    $Refersource->save();
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }
            $transaction = $this->transactionFactory->create();
            $signup_bonus = $this->_scopeConfig->getValue('affiliate/referfriend/signup_bonus');
            $referral_reward = $this->_scopeConfig->getValue('affiliate/referfriend/referral_reward');
            $transaction->setData('customer_id', $customer_id);
            $transaction->setData('description', "Joining Bonus");
            $transaction->setData('earned_amount', $signup_bonus);
            $transaction->setData('transaction_type', self::TRANSACTION_TYPE_CREDIT);
            $transaction->setData('creation_date', $this->_date->gmtDate());
            $transaction->save();
            if (!empty($referral_id)) {
                $transaction = $this->transactionFactory->create();
                $transaction->setData('customer_id', $referral_id);
                $transaction->setData('description', "Referral Reward For-" . $customer->getEmail());
                $transaction->setData('creation_date', $this->_date->gmtDate());
                $transaction->setData('earned_amount', $referral_reward);
                $transaction->setData('transaction_type', self::TRANSACTION_TYPE_CREDIT);
                $transaction->save();
            }

            if (!empty($referral_id)) {
                $referred_friends_model = $this->affiliateReferralFactory->create();
                $referred_friends = $referred_friends_model->getCollection()
                    ->addFieldToFilter('referred_email', $customer->getEmail())
                    ->addFieldToFilter('customer_id', $referral_id)->getData();

                if (sizeof($referred_friends) > 0) {
                    foreach ($referred_friends as $key => $value) {
                        $referred_friends_model->load($value['id']);
                    }
                    $referred_friends_model->setData('signup_status', self::SIGNUP_STATUS_ENABLE);
                    $referred_friends_model->setData('signup_date', $this->_date->gmtDate());
                    $referred_friends_model->setData('amount', $referral_reward);
                    $referred_friends_model->save();
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            die("hel");
        }
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
            echo $e->getMessage();
            die("---");
        }
    }

    /**
     * @param $referral_code
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerIdByReferralCode($referral_code)
    {
        $code = $this->customerCollectionFactory->create()
            ->addAttributeToFilter('invitation_code', $referral_code)->getData();
        foreach ($code as $key => $value) {
            $customerId = $value['entity_id'];
        }
        if (isset($customerId)) {
            return $customerId;
        }
    }
}
