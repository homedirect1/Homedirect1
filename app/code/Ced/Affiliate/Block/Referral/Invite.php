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

namespace Ced\Affiliate\Block\Referral;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class Invite
 * @package Ced\Affiliate\Block\Referral
 */
class Invite extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

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
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * Invite constructor.
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param \Magento\Customer\Model\Customer\Mapper $customerMapper
     * @param DataObjectHelper $dataObjectHelper
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterfaceFactory $customerDataFactory,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        DataObjectHelper $dataObjectHelper,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
    )
    {
        $this->_customerSession = $customerSession;
        $this->_customerRepository = $customerRepository;
        $this->_customerMapper = $customerMapper;
        $this->_customerDataFactory = $customerDataFactory;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\View\Element\Template
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set("Refer Friends");
        return $this;
    }

    /**
     * @return string
     */
    public function generateReferralLink()
    {
        $customer = $this->_customerSession->getCustomer();
        $customer_Id = $customer->getId();
        $invitation_code = $this->getInvitationCode();
        $code = $this->getUrl("customer/account/create", ["affid" => $this->getInvitationCode()]);
        return $code;
    }

    /**
     * @return \Magento\Framework\Api\AttributeInterface|mixed|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getInvitationCode()
    {
        $customer = $this->_customerSession->getCustomer();
        $customer_id = $customer->getId();
        $customerRepository = $this->_customerRepository;
        $customer_model = $customerRepository->getById($customer_id);

        $invitation_code = $customer_model->getCustomAttribute('invitation_code');
        if ($invitation_code != null) {
            $invitation_code = $invitation_code->getValue();
        } else {
            $this->createInvitationCode($customer_id);
            $this->getInvitationCode();
        }
        return $invitation_code;
    }

    /**
     * @return string
     */
    public function CustomerEmail()
    {
        $customer = $this->_customerSession->getCustomer();
        $customer_mail = $customer->getEmail();
        return $customer_mail;
    }

    /**
     * @param $customer_id
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createInvitationCode($customer_id)
    {
        $customerData = ["invitation_code" => $this->generateInvitationCode()];
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
     * @return string
     */
    public function generateInvitationCode()
    {
        $length = 6;
        $rndId = md5(uniqid(rand(), 1));
        $rndId = strip_tags(stripslashes($rndId));
        $rndId = str_replace(array(".", "$"), "", $rndId);
        $rndId = strrev(str_replace("/", "", $rndId));
        if (!is_null($rndId)) {
            return strtoupper(substr($rndId, 0, $length));
        }
        return strtoupper($rndId);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function generateFBReferralLink()
    {
        $customer = $this->_customerSession->getCustomer();
        $customer_Id = $customer->getId();
        $invitation_code = $this->getInvitationCode();
        $code = $this->getUrl("customer/account/create",
            ["affid" => base64_encode('facebook-' . $this->getInvitationCode())]);
        return $code;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function generateGoogleReferralLink()
    {
        $customer = $this->_customerSession->getCustomer();
        $customer_Id = $customer->getId();
        $invitation_code = $this->getInvitationCode();
        $code = $this->getUrl("customer/account/create",
            ["affid" => base64_encode('google-' . $this->getInvitationCode())]);
        return $code;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function generateTwitterReferralLink()
    {
        $customer = $this->_customerSession->getCustomer();
        $customer_Id = $customer->getId();
        $invitation_code = $this->getInvitationCode();
        $code = $this->getUrl("customer/account/create",
            ["affid" => base64_encode('twitter-' . $this->getInvitationCode())]);
        return $code;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function generateWatsappReferralLink()
    {
        $customer = $this->_customerSession->getCustomer();
        $customer_Id = $customer->getId();
        $invitation_code = $this->getInvitationCode();
        $code = $this->getUrl("customer/account/create",
            ["affid" => base64_encode('whatsapp-' . $this->getInvitationCode())]);
        return $code;
    }

    /**
     * @return mixed
     */
    public function getAffiliateUrl()
    {
        $model = $this->affiliateAccountFactory->create()
            ->load($this->_customerSession->getCustomer()->getId(), 'customer_id');
        return $model->getAffiliateUrl();
    }

    /**
     * @param $class
     * @return mixed
     */
    public function getHelper($class)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->create($class);
    }

    /**
     * @return mixed
     */
    public function getDefaultMessage()
    {
        return $this->_scopeConfig->getValue('affiliate/referfriend/email_content',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getDefaultSubject()
    {
        return $this->_scopeConfig->getValue('affiliate/referfriend/email_subject',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}