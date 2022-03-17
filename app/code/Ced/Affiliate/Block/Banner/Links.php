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

namespace Ced\Affiliate\Block\Banner;

/**
 * Class Links
 * @package Ced\Affiliate\Block\Banner
 */
class Links extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateBanner\CollectionFactory
     */
    protected $bannerCollectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * Links constructor.
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateBanner\CollectionFactory $bannerCollectionFactory
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\ResourceModel\AffiliateBanner\CollectionFactory $bannerCollectionFactory,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        array $data = []
    )
    {
        $this->date = $date;
        $this->_customerSession = $customerSession;
        $this->bannerCollectionFactory = $bannerCollectionFactory;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getBanners()
    {
        return $this->bannerCollectionFactory->create()->addFieldToFilter('status', 1);
    }

    /**
     * @return $this|\Magento\Framework\View\Element\Template
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getBanners()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'sales.order.history.pager'
            )->setLimit(4)
                ->setCollection(
                    $this->getBanners()
                );

            $this->setChild('pager', $pager);
            $this->getBanners()->load();
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAffiliateLink()
    {
        return $this->affiliateAccountFactory->create()->load($this->_customerSession->getCustomerId(), 'customer_id');
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

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return false|int
     */
    public function getCurrentTimestamp()
    {
        return strtotime($this->date->gmtDate('Y-m-d'));
    }

    /**
     * @return array
     */
    public function getActiveSharing()
    {
        $active = $this->_scopeConfig->getValue('affiliate/referfriend/social_networking',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return explode(',', $active);
    }

    /**
     * @param $class
     * @return mixed
     */
    public function getHelper($class)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->create($class);
    }
}