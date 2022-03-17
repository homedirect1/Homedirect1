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
 * @package     Ced_CsSubAccount
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Block\Vendor\Profile;

use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class View
 * @package Ced\CsSubAccount\Block\Vendor\Profile
 */
class View extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{

    /**
     * @var \Ced\CsMarketplace\Model\VendorFactory
     */
    public $_vendor;

    /**
     * @var int
     */
    public $_totalattr;

    /**
     * @var int
     */
    public $_savedattr;

    /**
     * @var \Ced\CsMarketplace\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    protected $regionCollectionFactory;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Ced\CsMarketplace\Model\UrlFactory
     */
    protected $marketplaceUrlFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     */
    protected $setCollectionFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var \Ced\CsSubAccount\Model\CsSubAccountFactory
     */
    protected $csSubAccountFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * View constructor.
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Ced\CsMarketplace\Model\UrlFactory $marketplaceUrlFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $groupCollectionFactory
     * @param \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param Context $context
     * @param \Ced\CsMarketplace\Model\Session $customerSession
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Ced\CsMarketplace\Model\UrlFactory $marketplaceUrlFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $groupCollectionFactory,
        \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        Context $context,
        \Ced\CsMarketplace\Model\Session $customerSession,
        UrlFactory $urlFactory
    )
    {
        parent::__construct($vendorFactory, $customerFactory, $context, $customerSession, $urlFactory);

        $this->_vendor = $vendorFactory;
        $this->_totalattr = 0;
        $this->_customerSession = $customerSession;
        $this->_savedattr = 0;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->countryFactory = $countryFactory;
        $this->marketplaceUrlFactory = $marketplaceUrlFactory;
        $this->setCollectionFactory = $setCollectionFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->csSubAccountFactory = $csSubAccountFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $country_id
     * @return mixed
     */
    public function getGroup($country_id)
    {
        return $country_id;
    }

    /**
     * @param $country_id
     * @return string
     */
    public function getCountryIdValue($country_id)
    {
        $regionCollection = $this->regionCollectionFactory->create()->addCountryFilter($country_id);
        if ($regionCollection->getData() != null) {
            return 'true';
        } else {
            return 'false';
        }
    }

    /**
     * @param $region_id
     * @return mixed
     */
    public function getRegionFromId($region_id)
    {
        foreach ($this->regionCollectionFactory->create() as $region) {
            if ($region->getData('region_id') == $region_id)
                return $region->getData('default_name');
        }
    }

    /**
     * @param $cid
     * @return string
     */
    public function getCountryId($cid)
    {
        $country = $this->countryFactory->create()->loadByCode($cid);
        return $country->getName();
    }

    /**
     * @return mixed
     */
    public function getMediaUrl()
    {
        return $this->marketplaceUrlFactory->create()->getMediaUrl();
    }

    /**
     * @return mixed
     */
    public function getVendorAttributeInfo()
    {

        $entityTypeId = $this->_vendor->create()->getEntityTypeId();

        $setIds = $this->setCollectionFactory->create()
            ->setEntityTypeFilter($entityTypeId)->getAllIds();

        $groupCollection = $this->groupCollectionFactory->create();
        if (count($setIds) > 0) {
            $groupCollection->addFieldToFilter('attribute_set_id', array('in' => $setIds));
        }

        $groupCollection->setSortOrder()->load();

        $vendor_info = $this->_vendor->create()->load($this->getVendorId());
        $total = 0;
        $saved = 0;

        foreach ($groupCollection as $group) {
            $attributes = $this->_vendor->create()->getAttributes($group->getId(), true);
            if (count($attributes) == 0) {
                continue;
            }
        }

        $this->_totalattr = $total;
        $this->_savedattr = $saved;
        return $groupCollection;
    }

    /**
     * @return mixed
     */
    public function getSubVendor()
    {
        $subVendor = $this->_customerSession->getSubVendorData();
        $subVendor = $this->csSubAccountFactory->create()->load($subVendor['id']);
        return $subVendor;
    }


}
