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

/**
 * Class ReferralList
 * @package Ced\Affiliate\Model\Api\Affiliate
 */
class ReferralList extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\Refersource\CollectionFactory
     */
    protected $refersourceCollectionFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateTraffic\CollectionFactory
     */
    protected $trafficCollectionFactory;

    /**
     * ReferralList constructor.
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Ced\Affiliate\Model\ResourceModel\Refersource\CollectionFactory $refersourceCollectionFactory
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateTraffic\CollectionFactory $trafficCollectionFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Ced\Affiliate\Model\ResourceModel\Refersource\CollectionFactory $refersourceCollectionFactory,
        \Ced\Affiliate\Model\ResourceModel\AffiliateTraffic\CollectionFactory $trafficCollectionFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->refersourceCollectionFactory = $refersourceCollectionFactory;
        $this->trafficCollectionFactory = $trafficCollectionFactory;
    }

    /**
     * @param $customerId
     * @return array
     */
    public function getReferralListInformation($customerId)
    {

        $affiliateData = [];
        $affiliate = $this->affiliateAccountFactory->create()->load($customerId, 'customer_id');
        $affiliateId = $affiliate->getAffiliateId();
        $affiliateSources['google'] = $this->getCount('google', $customerId);
        $affiliateSources['facebook'] = $this->getCount('facebook', $customerId);
        $affiliateSources['twitter'] = $this->getCount('twitter', $customerId);
        $affiliateSources['email'] = $this->getCount('email', $customerId);
        $affiliateData['referral_sources'] = $affiliateSources;
        $affiliateData['traffic_history'] = $this->getTarfficHistory($affiliateId)->getData();
        return ["data" => $affiliateData];
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
     * @param $affiliateId
     * @return mixed
     */
    protected function getTarfficHistory($affiliateId)
    {
        $this->_trafficCollection = $this->trafficCollectionFactory->create()
            ->addFieldToFilter('affiliate_id', $affiliateId);
        return $this->_trafficCollection;


    }
}
