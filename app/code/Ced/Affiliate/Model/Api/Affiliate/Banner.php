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
 * Class Banner
 * @package Ced\Affiliate\Model\Api\Affiliate
 */
class Banner extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateBanner\CollectionFactory
     */
    protected $bannerCollectionFactory;

    /**
     * Banner constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storemanager
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateBanner\CollectionFactory $bannerCollectionFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storemanager,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Ced\Affiliate\Model\ResourceModel\AffiliateBanner\CollectionFactory $bannerCollectionFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_storeManager = $storemanager;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->bannerCollectionFactory = $bannerCollectionFactory;
    }

    /**
     * @param $customerId
     * @return array
     */
    public function getBannerInformation($customerId)
    {

        $affiliateData = [];
        $affiliate = $this->affiliateAccountFactory->create()->load($customerId, 'customer_id');
        $affiliateId = $affiliate->getAffiliateId();

        $affiliateBanner = $this->bannerCollectionFactory->create();
        $affiliateBanner->getSelect()
            ->columns(['affiliate' => new \Zend_Db_Expr('(' . $affiliateId . ')'),
                'image_url' => new \Zend_Db_Expr('("' . $this->getImageUrl() . '")')]);

        $affiliateData['banner_data'] = $affiliateBanner->getData();


        return ["data" => $affiliateData];
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl()
    {

        $url = $this->_storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'banner/files/';
        return $url;

    }
}
