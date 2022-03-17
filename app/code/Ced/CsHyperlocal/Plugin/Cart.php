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

namespace Ced\CsHyperlocal\Plugin;

use Ced\CsHyperlocal\Helper\Data;
use Magento\Framework\Exception\InputException;

/**
 * Class Cart
 * @package Ced\CsHyperlocal\Plugin
 */
class Cart
{
    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $marketplaceHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Data
     */
    protected $hyperlocalHelper;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory
     */
    protected $zipCodeCollectionFactory;

    /**
     * @var \Ced\CsMarketplace\Model\VproductsFactory
     */
    protected $vproductsFactory;

    /**
     * @var \Ced\CsMarketplace\Model\VendorFactory
     */
    protected $vendorFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Cart constructor.
     * @param \Ced\CsMarketplace\Helper\Data $marketplaceHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Data $hyperlocalHelper
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipCodeCollectionFactory
     * @param \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        \Ced\CsMarketplace\Helper\Data $marketplaceHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Data $hyperlocalHelper,
        \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipCodeCollectionFactory,
        \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    )
    {
        $this->marketplaceHelper = $marketplaceHelper;
        $this->_storeManager = $storeManager;
        $this->hyperlocalHelper = $hyperlocalHelper;
        $this->zipCodeCollectionFactory = $zipCodeCollectionFactory;
        $this->vproductsFactory = $vproductsFactory;
        $this->vendorFactory = $vendorFactory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param $subject
     * @param $productInfo
     * @param null $requestInfo
     * @return array
     * @throws InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeAddProduct(
        $subject, $productInfo, $requestInfo = null
    )
    {
        if ($productInfo && $this->hyperlocalHelper->isModuleEnabled() &&
            strpos(
                $productInfo->getSku(),
                Data::SKIPPED_PRODUCT_SKU
            ) === false
        ) {
            $savedLocationFromSession = $this->hyperlocalHelper->getShippingLocationFromSession();
            if (!$savedLocationFromSession) {
                throw new InputException(__('Please select location.'));
            }

            $filterType = $this->marketplaceHelper->getStoreConfig(Data::FILTER_TYPE);
            $radiusConfig = $this->marketplaceHelper->getStoreConfig(Data::FILTER_RADIUS);
            $distanceType = $this->marketplaceHelper->getStoreConfig(Data::DISTANCE_TYPE);
            $filterProductsBy = $this->marketplaceHelper->getStoreConfig(Data::FILTER_PRODUCTS_BY);
            $baseUrl = $this->_storeManager->getStore()->getBaseUrl();

            if ($savedLocationFromSession) {
                $vendorId = $this->vproductsFactory->create()->getVendorIdByProduct($productInfo->getId());
                if (!$vendorId) {
                    $vendorId = 0;
                }

                if ($filterType == 'distance') {
                    $tolat = $savedLocationFromSession['latitude'];
                    $tolong = $savedLocationFromSession['longitude'];
                    if ($vendorId != 0) {
                        $vendor = $this->vendorFactory->create()->load($vendorId);
                        $fromlat = $vendor->getLatitude();
                        $fromlong = $vendor->getLongitude();
                    } else {
                        $fromlat = $this->marketplaceHelper->getStoreConfig(Data::XML_LATITUDE);
                        $fromlong = $this->marketplaceHelper->getStoreConfig(Data::XML_LONGITUDE);
                    }
                    $distance = $this->hyperlocalHelper->calculateDistancebyHaversine($fromlat, $fromlong, $tolat, $tolong);
                    if ($distance > $radiusConfig) {
                        throw new InputException(__('Product is not in range.'));
                    }

                } elseif ($filterProductsBy == 'vendor_location') {
                    /** Filter Products By Vendor Location */
                    if ($filterType == 'city_state_country') {

                        //------------------- Filter By City,country & state----------------[START]
                        $locationCollection = $this->hyperlocalHelper->getFilteredlocationByCityStateCountry($savedLocationFromSession);
                        if ($locationCollection) {
                            $locationCollection->addFieldToFilter('vendor_id', $vendorId);
                            if (count($locationCollection->getData()) == 0) {
                                throw new InputException(
                                    __('Product is not available for the selected location.')
                                );
                            }
                        } else {
                            throw new InputException(
                                __('Product is not available for the selected location.')
                            );
                        }
                        //------------------- Filter By City,country & state----------------[END]

                    } elseif ($filterType == 'zipcode' && isset($savedLocationFromSession['filterZipcode'])) {

                        //------------------- Filter By Zipcode----------------[START]
                        $resource = $this->resourceConnection;
                        $tableName = $resource->getTableName('ced_cshyperlocal_shipping_area');

                        $zipCodeCollection = $this->zipCodeCollectionFactory->create();
                        $zipCodeCollection->getSelect()
                            ->joinLeft(
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
                        $zipCodeCollection->addFieldToFilter('main_table.vendor_id', $vendorId);
                        if ($zipCodeCollection->count() == 0) {
                            throw new InputException(
                                __('Product is not available for the selected zipcode')
                            );
                        }
                        //------------------- Filter By Zipcode----------------[END]
                    }

                } elseif ($filterProductsBy == 'product_location') {

                    $pLocationIds = [];
                    if ($productInfo->getShippingProductLocation()) {
                        $pLocationIds = explode(',', $productInfo->getShippingProductLocation());
                    }
                    if (count($pLocationIds) > 0) {
                        $exist = false;
                        foreach ($pLocationIds as $locationId) {
                            if ($filterType == 'zipcode' && isset($savedLocationFromSession['filterZipcode'])) {

                                $zipCodeCollection = $this->zipCodeCollectionFactory->create();
                                $resource = $this->resourceConnection;
                                $tableName = $resource->getTableName('ced_cshyperlocal_shipping_area');
                                $zipCodeCollection->getSelect()
                                    ->joinLeft(
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
                                )->addFieldToFilter(
                                    'main_table.location_id',
                                    $locationId
                                );

                                if ($zipCodeCollection->count()) {
                                    $exist = true;
                                }
                            } elseif ($filterType == 'city_state_country') {

                                //------------------- Filter By City,country & state----------------[START]
                                $locationCollection = $this->hyperlocalHelper->getFilteredlocationByCityStateCountry($savedLocationFromSession);
                                if ($locationCollection) {
                                    $locationCollection->addFieldToFilter('id', $locationId);
                                    if (count($locationCollection->getData()) > 0) {
                                        $exist = true;
                                    }
                                }
                                //------------------- Filter By City,country & state----------------[END]
                            }
                        }
                        if (!$exist) {
                            $subject->getResponse()->setRedirect($baseUrl);
                            throw new InputException(__('Product is not available for the selected address/zipcode.'));
                        }
                    }
                }
            }
        }
        return [$productInfo, $requestInfo];
    }
}
