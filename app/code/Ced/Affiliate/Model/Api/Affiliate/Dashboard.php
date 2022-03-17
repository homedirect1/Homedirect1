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
 * Class Dashboard
 * @package Ced\Affiliate\Model\Api\Affiliate
 */
class Dashboard extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory
     */
    protected $comissionCollectionFactory;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * Dashboard constructor.
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->comissionCollectionFactory = $comissionCollectionFactory;
        $this->affiliateHelper = $affiliateHelper;
    }

    /**
     * @param $customerId
     * @return array
     */
    public function getDashboardInformation($customerId)
    {

        $affiliateData = [];
        $affiliate = $this->affiliateAccountFactory->create()->load($customerId, 'customer_id');
        $affiliateId = $affiliate->getAffiliateId();
        $affiliateOrders = $this->comissionCollectionFactory->create()
            ->addFieldToFilter('affiliate_id', $affiliateId);
        if ($affiliateOrders->getData()) {
            $affiliateData['affiliate_order'] = $affiliateOrders->getData();
        }
        $totalAmount = $this->affiliateHelper->getAmount($affiliateId);
        $earnedAmount = $this->affiliateHelper->getAmountHistory($customerId);
        $affiliateBalance['total_amount'] = $this->affiliateHelper->getFormattedPrice($totalAmount[0]['total_amount']);
        $affiliateBalance['earned_amount'] = $this->affiliateHelper
            ->getFormattedPrice($earnedAmount[0]['earned_amount']);
        $affiliateBalance['remaining_amount'] = $this->affiliateHelper
            ->getFormattedPrice((float)$totalAmount[0]['total_amount'] - (float)$earnedAmount[0]['earned_amount']);

        $affiliateData['balance_history'] = $affiliateBalance;

        return ["data" => $affiliateData];
    }
}
