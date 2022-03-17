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
 * @package     Ced_CsHyperlocal
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsHyperlocal\Block\Vshops\ListBlock;

use Ced\CsHyperlocal\Helper\Data as HyperlocalHelperData;
use Ced\CsMarketplace\Helper\Acl;
use Ced\CsMarketplace\Helper\Data as CsMarketplaceHelperData;
use Ced\CsMarketplace\Helper\Tool\Image;
use Ced\CsMarketplace\Model\Vendor;
use Ced\CsMarketplace\Model\Vshop;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Product\ProductList;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Directory\Helper\Data;
use Magento\Framework\Url\Helper\Data as UrlHelperData;
use Magento\Tax\Helper\Data as TaxHelperData;

/**
 * Class ListBlock
 * @package Ced\CsHyperlocal\Block\Vshops\ListBlock
 */
class ListBlock extends \Ced\CsMarketplace\Block\Vshops\ListBlock
{
    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'Magento\Catalog\Block\Product\ProductList\Toolbar';

    /**
     * @var Vendor Collection
     */
    protected $_vendorCollection;

    /**
     * @var \Magento\Framework\Registry $_coreRegistry
     */
    public $_coreRegistry = null;

    /**
     * @var null
     */
    protected $_checkout = null;

    /**
     * @var null
     */
    protected $_quote = null;

    /**
     * @var HyperlocalHelperData
     */
    protected $hyperlocalHelper;

    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $marketplaceHelper;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory
     */
    protected $zipCodeCollectionFactory;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vendor\Collection|\Magento\Framework\Data\Collection\AbstractDb
     */
    protected $vendorCollection;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\Collection
     */
    protected $shipareaCollection;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vshop\CollectionFactory
     */
    protected $vshopCollectionFactory;

    /**
     * @var \Ced\CsMarketplace\Model\Vendor
     */
    protected $vendor;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;


    /**
     * ListBlock constructor.
     * @param Image $imageHelper
     * @param Acl $aclHelper
     * @param TaxHelperData $magentoTaxHelper
     * @param Data $magentoDirectoryHelper
     * @param Resolver $layerResolver
     * @param UrlHelperData $urlHelper
     * @param Vshop $vshop
     * @param Vendor $vendor
     * @param CsMarketplaceHelperData $csmarketplaceHelper
     * @param ProductList $prodListHelper
     * @param Context $context
     * @param HyperlocalHelperData $hyperlocalHelper
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\Collection $zipCodeCollectionFactory
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vendor\Collection $vendorCollection
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\Collection $shipareaCollection
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vshop\CollectionFactory $vshopCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param array $data
     */
    public function __construct(
        Image $imageHelper,
        Acl $aclHelper,
        TaxHelperData $magentoTaxHelper,
        Data $magentoDirectoryHelper,
        Resolver $layerResolver,
        UrlHelperData $urlHelper,
        Vshop $vshop,
        Vendor $vendor,
        CsMarketplaceHelperData $csmarketplaceHelper,
        ProductList $prodListHelper,
        Context $context,
        HyperlocalHelperData $hyperlocalHelper,
        \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipCodeCollectionFactory,
        \Ced\CsMarketplace\Model\ResourceModel\Vendor\Collection $vendorCollection,
        \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\Collection $shipareaCollection,
        \Ced\CsMarketplace\Model\ResourceModel\Vshop\CollectionFactory $vshopCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        array $data = [])
    {
        $this->_coreRegistry = $context->getRegistry();
        $this->hyperlocalHelper = $hyperlocalHelper;
        $this->marketplaceHelper = $csmarketplaceHelper;
        $this->zipCodeCollectionFactory = $zipCodeCollectionFactory;
        $this->vendorCollection = $vendorCollection->addFieldToFilter('status', \Ced\CsMarketplace\Model\Vendor::VENDOR_APPROVED_STATUS);
        $shipareaCollection = $shipareaCollection->addFieldToFilter('status', \Ced\CsHyperlocal\Model\Shiparea::STATUS_ENABLED);
        $shipareaCollection->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
        $this->shipareaCollection = $shipareaCollection;
        $this->vshopCollectionFactory = $vshopCollectionFactory;
        $this->vendor = $vendor;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($imageHelper,
            $aclHelper, $magentoTaxHelper,
            $magentoDirectoryHelper, $layerResolver,
            $urlHelper, $vshop, $vendor,
            $csmarketplaceHelper, $prodListHelper,
            $context, $data);
    }

    /**
     * @return Vendor|\Ced\CsMarketplace\Block\Vshops\ListBlock\Mage_Eav_Model_Entity_Collection_Abstract
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getVendorCollection()
    {
        $vendor_name = $this->_request->getParam('char');
        $name_filter = $this->_request->getParam('product_list_dir');
        $zip_code = $this->_request->getParam('estimate_postcode');
        $country = $this->_request->getParam('country_id');
        $city = $this->_request->getParam('estimate_city');
        // $vendor_name = $this->_coreRegistry->registry('vendor_name');
        // $name_filter = $this->_coreRegistry->registry('name_filter');
        // $zip_code = $this->_coreRegistry->registry('zip_code');
        // $country = $this->_coreRegistry->registry('country_id');
        // echo $country;
        // die("181hypl/lis/lsi");

        if (is_null($this->_vendorCollection)) {
            $vendorIds = [0];
            $model = $this->vshopCollectionFactory->create()
                ->addFieldToFilter('shop_disable', array('eq' => \Ced\CsMarketplace\Model\Vshop::DISABLED));

            if (count($model) > 0) {
                foreach ($model as $row) {
                    $vendorIds[] = $row->getVendorId();
                }
            }

            $this->_vendorCollection = $this->vendor
                ->getCollection()->addAttributeToSelect('*')
                ->addAttributeToFilter(
                    'status',
                    ['eq' => \Ced\CsMarketplace\Model\Vendor::VENDOR_APPROVED_STATUS]
                );
            if ($name_filter == '') {
                $this->_vendorCollection->addAttributeToSort('public_name', 'asc');
            }

            if (count($vendorIds) > 0) {
                if ($vendor_name != '' || $country != '' || $zip_code != '' || $name_filter != '') {
                    if ($vendor_name != '') {
                        $this->_vendorCollection->addAttributeToFilter(
                            array(
                                array('attribute' => 'public_name', 'like' => '%' . $vendor_name . '%'),
                            ));
                    }

                    if ($country != '') {
                        $this->_vendorCollection->addAttributeToFilter(
                            array(array('attribute' => 'country_id', 'like' => '%' . $country . '%')));
                    }

                    if ($zip_code != '') {
                        $this->_vendorCollection->addAttributeToFilter(
                            array(array('attribute' => 'zip_code', 'like' => '%' . $zip_code . '%')));
                    }

                    if ($name_filter != '') {
                        $this->_vendorCollection->addAttributeToSort('public_name', $name_filter);
                    }
                    $this->_vendorCollection->addAttributeToFilter('entity_id', array('nin' => $vendorIds));
                } else {
                    $this->_vendorCollection->addAttributeToFilter('entity_id', array('nin' => $vendorIds));
                }
            }

            if ($this->marketplaceHelper->isSharingEnabled()) {
                $this->_vendorCollection->addAttributeToFilter(
                    'website_id',
                    ['eq' => $this->storeManager->getStore()->getWebsiteId()]
                );
            }

            if ($this->marketplaceHelper->getStoreConfig(HyperlocalHelperData::MODULE_ENABLE)) {
                //------------------- Custom Filter----------------[START]

                $savedLocationFromSession = $this->hyperlocalHelper->getShippingLocationFromSession();
                $filterType = $this->marketplaceHelper->getStoreConfig(HyperlocalHelperData::FILTER_TYPE);
                $radiusConfig = $this->marketplaceHelper
                    ->getStoreConfig(HyperlocalHelperData::FILTER_RADIUS);
                $distanceType = $this->marketplaceHelper
                    ->getStoreConfig(HyperlocalHelperData::DISTANCE_TYPE);
                $filterProductsBy = $this->marketplaceHelper
                    ->getStoreConfig(HyperlocalHelperData::FILTER_PRODUCTS_BY);

                if ($filterProductsBy == 'vendor_location' || $filterType == 'distance') {
                    $vendorIds = [0];
                    if ($savedLocationFromSession) {

                        /** Filter Products By Vendor Location */
                        if ($filterType == 'city_state_country') {

                            //------------------- Filter By City,country & state----------------[START]
                            $locationCollection = $this->hyperlocalHelper->getFilteredlocationByCityStateCountry($savedLocationFromSession);
                            if ($locationCollection) {
                                $vendorIds = $locationCollection->getColumnValues('vendor_id');
                            }

                            //------------------- Filter By City,country & state----------------[END]

                        } elseif ($filterType == 'zipcode' && isset($savedLocationFromSession['filterZipcode'])) {

                            //------------------- Filter By Zipcode----------------[START]
                            $resource = $this->resourceConnection;
                            $tableName = $resource->getTableName('ced_cshyperlocal_shipping_area');
                            $zipCodeCollection = $this->zipCodeCollectionFactory->create();
                            $zipCodeCollection->getSelect()->joinLeft(
                                $tableName,
                                'main_table.location_id = ' . $tableName . '.id',
                                ['status', 'is_origin_address']
                            );
                            $zipCodeCollection->addFieldToFilter(
                                'main_table.zipcode',
                                $savedLocationFromSession['filterZipcode']
                            )->addFieldToFilter(
                                'status',
                                \Ced\CsHyperlocal\Model\Shiparea::STATUS_ENABLED
                            );

                            $zipCodeCollection->getSelect()
                                ->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
                            $vendorIds = $zipCodeCollection->getColumnValues('vendor_id');
                            //------------------- Filter By Zipcode----------------[END]

                        } elseif ($filterType == 'distance') {

                            $tolat = $savedLocationFromSession['latitude'];
                            $tolong = $savedLocationFromSession['longitude'];
                            $vIds = [];
                            if ($tolat != '' && $tolong != '') {
                                $vendorCollection = $this->vendorCollection->addAttributeToSelect('*');
                                if ($vendorCollection->count()) {
                                    foreach ($vendorCollection as $vendor) {
                                        $distance = $this->hyperlocalHelper->calculateDistancebyHaversine($vendor->getLatitude(), $vendor->getLongitude(), $tolat, $tolong);
                                        if ($distance <= $radiusConfig) {
                                            $vendorIds[] = $vendor->getId();
                                        }
                                    }
                                }
                            }
                        }
                        $this->_vendorCollection->addAttributeToFilter(
                            'entity_id',
                            ['in' => $vendorIds]
                        );
                    }
                }
                //------------------- Custom Filter ----------------[END]
            }
            $this->prepareSortableFields();
        }
        return $this->_vendorCollection;
    }
}
