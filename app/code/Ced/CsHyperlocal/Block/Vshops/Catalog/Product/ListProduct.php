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

namespace Ced\CsHyperlocal\Block\Vshops\Catalog\Product;

use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Url\Helper\Data;

/**
 * Class ListProduct
 * @package Ced\CsHyperlocal\Block\Vshops\Catalog\Product
 */
class ListProduct extends \Ced\CsMarketplace\Block\Vshops\Catalog\Product\ListProduct
{
    /**
     * Product Collection
     *
     * @var AbstractCollection
     */
    protected $_productCollection;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Ced\CsHyperlocal\Helper\Data
     */
    protected $hyperlocalHelper;

    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $marketplaceHelper;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\Collection
     */
    protected $zipcodeCollection;

    protected $_vproductsFactory;

    protected $_productCollectionFactory;

    protected $catalogConfig;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * ListProduct constructor.
     * @param \Ced\CsHyperlocal\Helper\Data $hyperlocalHelper
     * @param \Ced\CsMarketplace\Helper\Data $marketplaceHelper
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\Collection $zipcodeCollection
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param Context $context
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $urlHelper
     * @param \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param array $data
     */
    public function __construct(
        \Ced\CsHyperlocal\Helper\Data $hyperlocalHelper,
        \Ced\CsMarketplace\Helper\Data $marketplaceHelper,
        \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\Collection $zipcodeCollection,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $vproductsFactory,
            $productCollectionFactory,
            $catalogConfig,
            $data
        );

        $this->categoryRepository = $categoryRepository;
        $this->hyperlocalHelper = $hyperlocalHelper;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->zipcodeCollection = $zipcodeCollection;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Retrieve loaded category collection
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getProductCollection()
    { 
        $name_filter = $this->_coreRegistry->registry('name_filter');
               
        if ($this->_productCollection === null) {
            $cedLayer = $this->getLayer();
            if ($this->getShowRootCategory()) {
                $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
            }

            if ($this->_coreRegistry->registry('product')) {
                $cedCategories = $this->_coreRegistry->registry('product')
                    ->getCategoryCollection()->setPage(1, 1)
                    ->load();
                if ($cedCategories->count()) {
                    $this->setCategoryId(current($cedCategories->getIterator()));
                }
            }
            $origCategory = null;
            if ($this->getCategoryId()) {
                try {
                    $cedCategory = $this->categoryRepository->get($this->getCategoryId());
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $cedCategory = null;
                }
                if ($cedCategory) {
                    $origCategory = $cedLayer->getCurrentCategory();
                    $cedLayer->setCurrentCategory($cedCategory);
                }
            }
            $vendorId = $this->_coreRegistry->registry('current_vendor')->getId();
            $collection = $this->_vproductsFactory->create()
                ->getVendorProducts(\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS, $vendorId);
            $products = [];
            foreach ($collection as $productData) {
                array_push($products, $productData->getProductId());
            }
            $cedProductcollection = $this->_productCollectionFactory->create()
                ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
                ->addAttributeToFilter('entity_id', ['in' => $products])
                ->addStoreFilter($this->getCurrentStoreId())
                ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                ->addAttributeToFilter('visibility', 4);

            $nameQ = $this->getRequest()->getParam(self::SEARCH_QUERY_PARAM, false);
            if($nameQ){
              $cedProductcollection->addAttributeToFilter(
                  'name', ['like' => '%'.$nameQ.'%']
              );
            }

            if (isset($name_filter)) {
                $cedProductcollection->addAttributeToSelect('*')->setOrder('entity_id', $name_filter);
            }

            $cat_id = $this->getRequest()->getParam('cat-fil');
            if (isset($cat_id)) {
                $cedProductcollection->joinField(
                    'category_id', 'catalog_category_product', 'category_id',
                    'product_id = entity_id', null, 'left'
                )
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('category_id', array(
                        array('finset', array('in' => explode(',', $cat_id)))
                    ));
            }

            /** hyperlocal filter start */
            if ($this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::MODULE_ENABLE)) {

                //------------------- Custom Filter----------------[START]
                $vendorIds = [];
                $locationIds = [];
                $savedLocationFromSession = $this->hyperlocalHelper->getShippingLocationFromSession();
                $filterType = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_TYPE);

                $finalPIds = [];


                $vendor = $this->_coreRegistry->registry('current_vendor');
                $resource = $this->resourceConnection;

                if ($savedLocationFromSession) {
                    $locationIds = [];
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
                                    $locationCollection->addFieldToFilter('vendor_id', $vendor->getId());
                                    if (count($locationCollection->getData()) == 0) {
                                        $cedProductcollection->addFieldToFilter('entity_id', ['in' => [0]]);
                                    }
                                } else {
                                    $cedProductcollection->addFieldToFilter('entity_id', ['in' => [0]]);
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
                                $isZipcodeAvailable = $this->zipcodeCollection->addFieldToFilter('main_table.zipcode', $savedLocationFromSession['filterZipcode'])
                                    ->addFieldToFilter('status', \Ced\CsHyperlocal\Model\Shiparea::STATUS_ENABLED);
                                $isZipcodeAvailable->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
                                $isZipcodeAvailable->addFieldToFilter('main_table.vendor_id', $vendor->getId());
                                if ($isZipcodeAvailable->count() == 0) {
                                    $cedProductcollection->addFieldToFilter('entity_id', ['in' => [0]]);
                                }
                            }

                        }

                        if(!empty($locationIds)){
                            $finSetArray = [0];
                            if (count($locationIds) > 0) {
                                for ($i = 0; $i < count($locationIds); $i++) {
                                    $finSetArray[] = ['finset' => [$locationIds[$i]]];
                                }
                            }

                            $cedProductcollection->addFieldToFilter('shipping_product_location', $finSetArray);
                        }


                    }else{
                        $distanceType = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::DISTANCE_TYPE);
                        $radiusConfig = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_RADIUS);
                        $tolat = $savedLocationFromSession['latitude'];
                        $tolong = $savedLocationFromSession['longitude'];
                        $vIds = [];
                        if ($tolat != '' && $tolong != '') {
                            $distance = $this->hyperlocalHelper->calculateDistancebyHaversine($vendor->getLatitude(), $vendor->getLongitude(), $tolat, $tolong);
                            if ($distance > $radiusConfig) {
                                $cedProductcollection->addFieldToFilter('entity_id', ['in' => [0]]);
                            }
                        }
                    }
                }

                /** end of hyperlocal system */
            }
            $this->_productCollection = $cedProductcollection;
            $this->prepareSortableFieldsByCategory($cedLayer->getCurrentCategory());

            if ($origCategory) {
                $cedLayer->setCurrentCategory($origCategory);
            }
        }
        return $this->_productCollection;
    }

}
