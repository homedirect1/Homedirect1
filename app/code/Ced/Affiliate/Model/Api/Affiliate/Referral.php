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

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class Referral
 * @package Ced\Affiliate\Model\Api\Affiliate
 */
class Referral extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

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
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateReferral\CollectionFactory
     */
    protected $referralCollectionFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\Refersource\CollectionFactory
     */
    protected $refersourceCollectionFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateTraffic\CollectionFactory
     */
    protected $trafficCollectionFactory;

    /**
     * Referral constructor.
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param \Magento\Customer\Model\Customer\Mapper $customerMapper
     * @param DataObjectHelper $dataObjectHelper
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateReferral\CollectionFactory $referralCollectionFactory
     * @param \Ced\Affiliate\Model\ResourceModel\Refersource\CollectionFactory $refersourceCollectionFactory
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateTraffic\CollectionFactory $trafficCollectionFactory
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlInterface,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterfaceFactory $customerDataFactory,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        DataObjectHelper $dataObjectHelper,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Ced\Affiliate\Model\ResourceModel\AffiliateReferral\CollectionFactory $referralCollectionFactory,
        \Ced\Affiliate\Model\ResourceModel\Refersource\CollectionFactory $refersourceCollectionFactory,
        \Ced\Affiliate\Model\ResourceModel\AffiliateTraffic\CollectionFactory $trafficCollectionFactory
    )
    {
        $this->_customerRepository = $customerRepository;
        $this->_customerMapper = $customerMapper;
        $this->_customerDataFactory = $customerDataFactory;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->urlBuilder = $urlInterface;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->referralCollectionFactory = $referralCollectionFactory;
        $this->refersourceCollectionFactory = $refersourceCollectionFactory;
        $this->trafficCollectionFactory = $trafficCollectionFactory;
    }

    /**
     * @param $customerId
     * @return array
     */
    public function getReferralInformation($customerId)
    {

        $affiliateReferral['referral_url'] = $this->generateReferralLink($customerId);
        $affiliateReferral['google_url'] = $this->generateGoogleReferralLink($customerId);
        $affiliateReferral['fb_url'] = $this->generateFBReferralLink($customerId);
        $affiliateReferral['twitter_url'] = $this->generateTwitterReferralLink($customerId);
        $affiliateReferral['referal_pending'] = $this->pendingreferral($customerId);
        $affiliateReferral['referal_acknowledged'] = $this->registeredreferral($customerId);
        $affiliateData['referral_details'] = $affiliateReferral;

        $affiliateSources['google'] = $this->getCount('google', $customerId);
        $affiliateSources['facebook'] = $this->getCount('facebook', $customerId);
        $affiliateSources['twitter'] = $this->getCount('twitter', $customerId);
        $affiliateSources['email'] = $this->getCount('email', $customerId);

        $affiliateData['referral_sources'] = $affiliateSources;
        $affiliateData['referred_list'] = $this->getReferralList($customerId)->getData();


        return ["data" => $affiliateData];
    }


    /**
     * @param $customer_Id
     * @return string
     */
    protected function generateReferralLink($customer_Id)
    {
        $invitation_code = $this->getInvitationCode($customer_Id);
        $code = $this->urlBuilder->getUrl("customer/account/create", ["affid" => $this->getInvitationCode($customer_Id)]);
        return $code;
    }

    /**
     * @param $customer_id
     * @return \Magento\Framework\Api\AttributeInterface|mixed|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getInvitationCode($customer_id)
    {
        $customerRepository = $this->_customerRepository;
        $customer_model = $customerRepository->getById($customer_id);

        $invitation_code = $customer_model->getCustomAttribute('invitation_code');
        if ($invitation_code != null) {
            $invitation_code = $invitation_code->getValue();
        } else {
            $this->createInvitationCode($customer_id);
            $this->getInvitationCode($customer_id);
        }
        return $invitation_code;
    }


    /**
     * @param $customerId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function createInvitationCode($customerId)
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

        }
    }

    /**
     * @return string
     */
    protected function generateInvitationCode()
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
     * @param $customer_Id
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function generateFBReferralLink($customer_Id)
    {
        $invitation_code = $this->getInvitationCode($customer_Id);
        $code = $this->urlBuilder->getUrl("customer/account/create", ["affid" => base64_encode('facebook-' . $this->getInvitationCode($customer_Id))]);
        return $code;
    }

    /**
     * @param $customer_Id
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function generateGoogleReferralLink($customer_Id)
    {
        $invitation_code = $this->getInvitationCode($customer_Id);
        $code = $this->urlBuilder->getUrl("customer/account/create", ["affid" => base64_encode('google-' . $this->getInvitationCode($customer_Id))]);
        return $code;
    }

    /**
     * @param $customer_Id
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function generateTwitterReferralLink($customer_Id)
    {
        $invitation_code = $this->getInvitationCode($customer_Id);
        $code = $this->urlBuilder->getUrl("customer/account/create", ["affid" => base64_encode('twitter-' . $this->getInvitationCode($customer_Id))]);
        return $code;
    }

    /**
     * @param $customer_Id
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function generateWatsappReferralLink($customer_Id)
    {
        $invitation_code = $this->getInvitationCode();
        $code = $this->urlBuilder->getUrl("customer/account/create", ["affid" => base64_encode('whatsapp-' . $this->getInvitationCode($customer_Id))]);
        return $code;
    }

    /**
     * @param $customer_Id
     * @return mixed
     */
    protected function getAffiliateUrl($customer_Id)
    {

        $model = $this->affiliateAccountFactory->create()->load($customer_Id, 'customer_id');
        return $model->getAffiliateUrl();
    }

    /**
     * @param $customer_Id
     * @return int|void
     */
    protected function pendingreferral($customer_Id)
    {
        $pendingreferral = $this->referralCollectionFactory->create()->addFieldtoFilter('customer_id', [
            'customer_id' => $customer_Id
        ])->addFieldtoFilter('signup_status', 0)->getData();
        return sizeof($pendingreferral);
    }

    /**
     * @param $customer_Id
     * @return int|void
     */
    public function registeredreferral($customer_Id)
    {
        $registeredreferral = $this->referralCollectionFactory->create()->addFieldtoFilter('customer_id', [
            'customer_id' => $customer_Id
        ])->addFieldtoFilter('signup_status', 1)->getData();
        return sizeof($registeredreferral);
    }

    /**
     * @param $customer_Id
     * @return mixed
     */
    protected function getReferralList($customer_Id)
    {

        $productModel = $this->referralCollectionFactory->create()->addFieldtoFilter('customer_id', [
            'customer_id' => $customer_Id
        ]);
        return $productModel;
    }

    /**
     * @param $source
     * @param $customer_Id
     * @return int|void
     */
    protected function getCount($source, $customer_Id)
    {
        $count = array();
        $count = $this->refersourceCollectionFactory->create()
            ->addFieldtoFilter('customer_id', $customer_Id)
            ->addFieldtoFilter('source', $source);
        return count($count);
    }

    /**
     * @return mixed
     */
    protected function getTotalSources()
    {

        $traffic = $this->trafficCollectionFactory->create()
            ->addFieldToFilter('affiliate_id', $this->_getSession->getAffiliateId());
        $traffic->getSelect()->reset('columns')->columns(['facebook_tclick' => 'SUM(facebook_click)',
            'google_tclick' => 'SUM(google_click)', 'twitter_tclick' => 'SUM(twitter_click)',
            'email_tclick' => 'SUM(email_click)', 'total_clicks' => 'SUM(total_click)']);
        return $traffic->getData();
    }
}
