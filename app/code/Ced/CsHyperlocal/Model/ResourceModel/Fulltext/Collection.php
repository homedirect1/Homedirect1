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

namespace Ced\CsHyperlocal\Model\ResourceModel\Fulltext;

use Magento\CatalogSearch\Model\Search\RequestGenerator;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorage;
use Magento\Framework\Search\Request\EmptyRequestDataException;
use Magento\Framework\Search\Request\NonExistingRequestNameException;
use Magento\Framework\Search\Response\QueryResponse;

class Collection extends \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
{
    /**
     * @var  QueryResponse
     * @deprecated
     */
    protected $queryResponse;

    /**
     * Catalog search data
     *
     * @var \Magento\Search\Model\QueryFactory
     * @deprecated
     */
    protected $queryFactory = null;

    /**
     * @var \Magento\Framework\Search\Request\Builder
     * @deprecated
     */
    private $requestBuilder;

    /**
     * @var \Magento\Search\Model\SearchEngine
     * @deprecated
     */
    private $searchEngine;

    /**
     * @var string
     */
    private $queryText;

    /**
     * @var string|null
     */
    private $order = null;

    /**
     * @var string
     */
    private $searchRequestName;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory
     */
    private $temporaryStorageFactory;

    /**
     * @var \Magento\Search\Api\SearchInterface
     */
    private $search;

    /**
     * @var \Magento\Framework\Api\Search\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\Search\SearchResultInterface
     */
    private $searchResult;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Url $catalogUrl
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Customer\Api\GroupManagementInterface $groupManagement
     * @param \Magento\Search\Model\QueryFactory $catalogSearchData
     * @param \Magento\Framework\Search\Request\Builder $requestBuilder
     * @param \Magento\Search\Model\SearchEngine $searchEngine
     * @param \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory
     * @param \Ced\CsHyperlocal\Helper\Data $hyperlocalHelper
     * @param \Ced\CsMarketplace\Helper\Data $marketplaceHelper
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory $vproductsCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $catalogProductCollectionfactory
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\Collection $zipcodeCollection
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vendor\Collection $vendorCollection
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param string $searchRequestName
     * @param SearchResultFactory|null $searchResultFactory
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Magento\Search\Model\QueryFactory $catalogSearchData,
        \Magento\Framework\Search\Request\Builder $requestBuilder,
        \Magento\Search\Model\SearchEngine $searchEngine,
        \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory,
        \Ced\CsHyperlocal\Helper\Data $hyperlocalHelper,
        \Ced\CsMarketplace\Helper\Data $marketplaceHelper,
        \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory $vproductsCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $catalogProductCollectionfactory,
        \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\Collection $zipcodeCollection,
        \Ced\CsMarketplace\Model\ResourceModel\Vendor\Collection $vendorCollection,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        $searchRequestName = 'catalog_view_container',
        SearchResultFactory $searchResultFactory = null
    )
    {
        $this->queryFactory = $catalogSearchData;
        if ($searchResultFactory === null) {
            $this->searchResultFactory = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Framework\Api\Search\SearchResultFactory');
        }
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $catalogSearchData,
            $requestBuilder,
            $searchEngine,
            $temporaryStorageFactory,
            $connection,
            $searchRequestName,
            $searchResultFactory
        );
        $this->temporaryStorageFactory = $temporaryStorageFactory;
        $this->searchRequestName = $searchRequestName;
        $this->hyperlocalHelper = $hyperlocalHelper;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->vproductsCollection = $vproductsCollectionFactory;
        $this->catalogProductCollectionfactory = $catalogProductCollectionfactory;
        $this->zipcodeCollection = $zipcodeCollection;
        $this->vendorCollection = $vendorCollection->addFieldToFilter('status', \Ced\CsMarketplace\Model\Vendor::VENDOR_APPROVED_STATUS);
        $this->resource = $resource;
    }

    /**
     * @return \Magento\Search\Api\SearchInterface
     * @deprecated
     */
    private function getSearch()
    {
        if ($this->search === null) {
            $this->search = ObjectManager::getInstance()->get('\Magento\Search\Api\SearchInterface');
        }
        return $this->search;
    }

    /**
     * @return \Magento\Framework\Api\Search\SearchCriteriaBuilder
     * @deprecated
     */
    private function getSearchCriteriaBuilder()
    {
        if ($this->searchCriteriaBuilder === null) {
            $this->searchCriteriaBuilder = ObjectManager::getInstance()
                ->get('\Magento\Framework\Api\Search\SearchCriteriaBuilder');
        }
        return $this->searchCriteriaBuilder;
    }

    /**
     * @return \Magento\Framework\Api\FilterBuilder
     * @deprecated
     */
    private function getFilterBuilder()
    {
        if ($this->filterBuilder === null) {
            $this->filterBuilder = ObjectManager::getInstance()->get('\Magento\Framework\Api\FilterBuilder');
        }
        return $this->filterBuilder;
    }

    /**
     * @inheritdoc
     */
    protected function _renderFiltersBefore()
    {
        $this->getSearchCriteriaBuilder();
        $this->getFilterBuilder();
        $this->getSearch();

        if ($this->queryText) {
            $this->filterBuilder->setField('search_term');
            $this->filterBuilder->setValue($this->queryText);
            $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());

            $skus = array(1);//array('24-MB04', '24-MB03', '24-MB02');

            $this->filterBuilder->setField('entity_id');
            $this->filterBuilder->setValue($skus);
            $this->filterBuilder->setConditionType('in');
            $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
        }

        $priceRangeCalculation = $this->_scopeConfig->getValue(
            \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory::XML_PATH_RANGE_CALCULATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($priceRangeCalculation) {
            $this->filterBuilder->setField('price_dynamic_algorithm');
            $this->filterBuilder->setValue($priceRangeCalculation);
            $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
        }

        if ($this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::MODULE_ENABLE)) {
            //------------------- Custom Filter----------------[START]

            $savedLocationFromSession = $this->hyperlocalHelper->getShippingLocationFromSession();
            $filterType = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_TYPE);
            $radiusConfig = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_RADIUS);
            $distanceType = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::DISTANCE_TYPE);
            $filterProductsBy = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_PRODUCTS_BY);
            if (!empty($savedLocationFromSession)) {
                $vendorIds = [];
                $locationIds = [];
                if ($filterProductsBy == 'product_location' && $filterType != 'distance') {

                    /** Filter Products By Product Location */
                    if ($filterType == 'city_state_country') {

                        //------------------- Filter By City,country & state----------------[START]
                        $locationCollection = $this->hyperlocalHelper->getFilteredlocationByCityStateCountry($savedLocationFromSession);
                        if ($locationCollection) {
                            $locationIds = $locationCollection->getColumnValues('id');
                        }
                        //------------------- Filter By City,country & state----------------[END]

                    } elseif ($filterType == 'zipcode' && isset($savedLocationFromSession['filterZipcode'])) {

                        //------------------- Filter By Zipcode----------------[START]
                        $resource = $this->resource;
                        $tableName = $resource->getTableName('ced_cshyperlocal_shipping_area');
                        $this->zipcodeCollection->getSelect()->joinLeft($tableName, 'main_table.location_id = ' . $tableName . '.id', array('status', 'is_origin_address'));
                        $shipareaCollection = $this->zipcodeCollection->addFieldToFilter('main_table.zipcode', $savedLocationFromSession['filterZipcode'])
                            ->addFieldToFilter('status', \Ced\CsHyperlocal\Model\Shiparea::STATUS_ENABLED);
                        $shipareaCollection->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
                        $locationIds = $shipareaCollection->getColumnValues('location_id');
                        //------------------- Filter By Zipcode----------------[END]
                    }
                    
                    $finSetArray = [0];
                    if (count($locationIds) > 0) {
                        for ($i = 0; $i < count($locationIds); $i++) {
                            $finSetArray[] = ['finset' => [$locationIds[$i]]];
                        }
                    }
                    $productCollection = $this->catalogProductCollectionfactory->create();
                    $finalPIds = $productCollection->addAttributeToSelect('*')->addAttributeToFilter('shipping_product_location', $finSetArray)->getColumnValues('entity_id');
                    /** End of Filter products by Product Location */

                } else {

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
                        $resource = $this->resource;
                        $tableName = $resource->getTableName('ced_cshyperlocal_shipping_area');
                        $this->zipcodeCollection->getSelect()->joinLeft($tableName, 'main_table.location_id = ' . $tableName . '.id', array('status', 'is_origin_address'));

                        $shipareaCollection = $this->zipcodeCollection->addFieldToFilter('main_table.zipcode', $savedLocationFromSession['filterZipcode'])
                            ->addFieldToFilter('status', \Ced\CsHyperlocal\Model\Shiparea::STATUS_ENABLED);
                        $shipareaCollection->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
                        $vendorIds = $shipareaCollection->getColumnValues('vendor_id');
                        //------------------- Filter By Zipcode----------------[END]

                    } elseif ($filterType == 'distance') {
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
                    /** End of Filter products by Vendor Location */
                }


                if (empty($finalPIds)) {
                    $finalPIds = [0];
                }
                $this->filterBuilder->setField('entity_id');
                $this->filterBuilder->setValue($finalPIds);
                $this->filterBuilder->setConditionType('in');
                $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
            }
            //------------------- Custom Filter ----------------[END]
        }


        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setRequestName($this->searchRequestName);
        try {
            $this->searchResult = $this->getSearch()->search($searchCriteria);
        } catch (EmptyRequestDataException $e) {
            /** @var \Magento\Framework\Api\Search\SearchResultInterface $searchResult */
            $this->searchResult = $this->searchResultFactory->create()->setItems([]);
        } catch (NonExistingRequestNameException $e) {
            $this->_logger->error($e->getMessage());
            throw new LocalizedException(__('Sorry, something went wrong. You can find out more in the error log.'));
        }

        $temporaryStorage = $this->temporaryStorageFactory->create();
        $table = $temporaryStorage->storeApiDocuments($this->searchResult->getItems());

        $this->getSelect()->joinInner(
            [
                'search_result' => $table->getName(),
            ],
            'e.entity_id = search_result.' . TemporaryStorage::FIELD_ENTITY_ID,
            []
        );

        $this->_totalRecords = $this->searchResult->getTotalCount();

        if ($this->order && 'relevance' === $this->order['field']) {
            $this->getSelect()->order('search_result.' . TemporaryStorage::FIELD_SCORE . ' ' . $this->order['dir']);
        }
        //return parent::_renderFiltersBefore();
        return \Magento\Catalog\Model\ResourceModel\Product\Collection::_renderFiltersBefore();
    }

    /**
     * Return field faceted data from faceted search result
     *
     * @param string $field
     * @return array
     * @throws StateException
     */
    public function getFacetedData($field)
    {
        $this->_renderFilters();
        $result = [];
        $aggregations = $this->searchResult->getAggregations();
        // This behavior is for case with empty object when we got EmptyRequestDataException
        if (null !== $aggregations) {
            $bucket = $aggregations->getBucket($field . RequestGenerator::BUCKET_SUFFIX);
            if ($bucket) {
                foreach ($bucket->getValues() as $value) {
                    $metrics = $value->getMetrics();
                    $result[$metrics['value']] = $metrics;
                }
            } else {
                throw new StateException(__('Bucket does not exist'));
            }
        }
        return $result;
    }


}
