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
 * Class ReferralSummary
 * @package Ced\Affiliate\Model\Api\Affiliate
 */
class ReferralSummary extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\Transaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * ReferralSummary constructor.
     * @param \Ced\Affiliate\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->transactionCollectionFactory = $transactionCollectionFactory;
    }

    /**
     * @param $customerId
     * @return array
     */
    public function getReferralSummaryInformation($customerId)
    {

        $affiliateData = [];
        $affiliateData['summary_data'] = $this->getSummaryCollection($customerId)->getData();
        return ["data" => $affiliateData];
    }

    /**
     * @param $customer_Id
     * @return mixed
     */
    protected function getSummaryCollection($customer_Id)
    {

        $customerModel = $this->transactionCollectionFactory->create()->addFieldtoFilter('customer_id', [
            'customer_id' => $customer_Id
        ]);
        return $customerModel;
    }
}
