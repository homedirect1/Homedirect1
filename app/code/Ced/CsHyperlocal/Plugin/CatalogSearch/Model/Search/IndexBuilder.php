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

namespace Ced\CsHyperlocal\Plugin\CatalogSearch\Model\Search;

use Magento\Framework\Search\RequestInterface;

class IndexBuilder
{

    public function __construct(
        \Ced\CsHyperlocal\Helper\Data $hyperlocalHelper,
        \Ced\CsMarketplace\Helper\Data $marketplaceHelper,
        \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory $vproductsCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $catalogProductCollectionfactory,
        \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\Collection $zipcodeCollection,
        \Ced\CsMarketplace\Model\ResourceModel\Vendor\Collection $vendorCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        $this->hyperlocalHelper = $hyperlocalHelper;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->vproductsCollection = $vproductsCollectionFactory;
        $this->catalogProductCollectionfactory = $catalogProductCollectionfactory;
        $this->zipcodeCollection = $zipcodeCollection;
        $this->vendorCollection = $vendorCollection->addFieldToFilter('status', \Ced\CsMarketplace\Model\Vendor::VENDOR_APPROVED_STATUS);
        $this->storeManager = $storeManager;
        $this->resource = $resource;
    }

    public function aroundBuild($subject, callable $proceed, RequestInterface $request)
    {
        $select = $proceed($request);
        if ($this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::MODULE_ENABLE)) {
            $savedLocationFromSession = $this->hyperlocalHelper->getShippingLocationFromSession();
            if ($savedLocationFromSession)
            {
                $storeId = $this->storeManager->getStore()->getStoreId();
                $rootCatId = $this->storeManager->getStore($storeId)->getRootCategoryId();
                $productUniqueIds = $this->getCustomCollectionQuery($savedLocationFromSession);
                if(!empty($productUniqueIds)){
                    $select->where('search_index.entity_id IN (' . join(',', $productUniqueIds) . ')');
                }
            }
        }
        return $select;
    }


    public function getCustomCollectionQuery($savedLocationFromSession){
        $finalPIds = [];
        //------------------- Custom Filter----------------[START]
        $filterType = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_TYPE);
        $resource = $this->resource;
        $locationIds = $vendorIds = [];
        if($filterType == 'city_state_country' || $filterType == 'zipcode'){

            $filterProductsBy = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_PRODUCTS_BY);
            if($filterType == 'city_state_country'){
                if($filterProductsBy == 'product_location'){
                    $locationCollection = $this->hyperlocalHelper->getFilteredlocationByCityStateCountry($savedLocationFromSession);
                    if ($locationCollection) {
                        $locationIds = $locationCollection->getColumnValues('id');
                    }
                }else{
                    $locationCollection = $this->hyperlocalHelper->getFilteredlocationByCityStateCountry($savedLocationFromSession);
                    if ($locationCollection) {
                        $vendorIds = $locationCollection->getColumnValues('vendor_id');
                    }
                }
            }elseif ($filterType == 'zipcode' && isset($savedLocationFromSession['filterZipcode'])) {
                if($filterProductsBy == 'product_location'){
                    $tableName = $resource->getTableName('ced_cshyperlocal_shipping_area');
                    $this->zipcodeCollection->getSelect()->joinLeft($tableName, 'main_table.location_id = ' . $tableName . '.id', array('status', 'is_origin_address'));
                    $shipareaCollection = $this->zipcodeCollection->addFieldToFilter('main_table.zipcode', $savedLocationFromSession['filterZipcode'])
                    ->addFieldToFilter('status', \Ced\CsHyperlocal\Model\Shiparea::STATUS_ENABLED);
                    $shipareaCollection->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
                    $locationIds = $shipareaCollection->getColumnValues('location_id');
                }else{
                    $tableName = $resource->getTableName('ced_cshyperlocal_shipping_area');
                    $this->zipcodeCollection->getSelect()->joinLeft($tableName, 'main_table.location_id = ' . $tableName . '.id', array('status', 'is_origin_address'));

                    $shipareaCollection = $this->zipcodeCollection->addFieldToFilter('main_table.zipcode', $savedLocationFromSession['filterZipcode'])
                        ->addFieldToFilter('status', \Ced\CsHyperlocal\Model\Shiparea::STATUS_ENABLED);
                    $shipareaCollection->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
                    $vendorIds = $shipareaCollection->getColumnValues('vendor_id');
                }
            }
            if(!empty($locationIds)){
                $finSetArray = [0];
                if (count($locationIds) > 0) {
                    for ($i = 0; $i < count($locationIds); $i++) {
                        $finSetArray[] = ['finset' => [$locationIds[$i]]];
                    }
                }

                $productCollection = $this->catalogProductCollectionfactory->create();
                $finalPIds = $productCollection->addAttributeToSelect('*')
                    ->addAttributeToFilter('shipping_product_location', $finSetArray)
                    ->getColumnValues('entity_id');
            }

        }else{

            $radiusConfig = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_RADIUS);
            $distanceType = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::DISTANCE_TYPE);


            $tolat = $savedLocationFromSession['latitude'];
            $tolong = $savedLocationFromSession['longitude'];
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

                /** if admin address is not null */
                $adminDefaultlocation = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::XML_LOCATION);
                $adminDefaultlat = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::XML_LATITUDE);
                $adminDefaultlong = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::XML_LONGITUDE);
                if ($adminDefaultlocation != '') {
                    $distance = $this->hyperlocalHelper->calculateDistancebyHaversine($adminDefaultlat, $adminDefaultlong, $tolat, $tolong);
                    if ($distance <= $radiusConfig) {
                        $vendorIds[] = 0;
                    }
                }
            }
        }
        $adminId = [0];
        $finalvendorIds = array_diff($vendorIds, $adminId);
        $catalogProductIds = [];
        $vendorProductIds = [];
        $vendorPids = $this->vproductsCollection->create()->addFieldToFilter('vendor_id', ['in' => $finalvendorIds])->getColumnValues('product_id');

        if (in_array(0, $vendorIds)) {
            $vendorProductIds = $this->vproductsCollection->create()->getColumnValues('product_id');
            $pCollection = $this->catalogProductCollectionfactory->create();
            if (count($vendorProductIds) > 0) {
                $catalogProductIds = $pCollection->addFieldToFilter('entity_id', ['nin' => $vendorProductIds])->getColumnValues('entity_id');
            } else {
                $catalogProductIds = $pCollection->getColumnValues('entity_id');
            }
        }
        $finalPIds = array_merge($vendorPids, $catalogProductIds);
        if (empty($finalPIds)) {
            $finalPIds = [0];
        }
        return $finalPIds;
    }
}
