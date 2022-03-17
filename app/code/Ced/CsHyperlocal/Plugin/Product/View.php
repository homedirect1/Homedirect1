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

namespace Ced\CsHyperlocal\Plugin\Product;

/**
 * Class View
 * @package Magento\Catalog\Controller\Product
 */
class View
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Ced\CsHyperlocal\Helper\Data
     */
    protected $hyperlocalHelper;

    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $marketplaceHelper;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory
     */
    protected $zipcodeCollection;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

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
     * View constructor.
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ced\CsHyperlocal\Helper\Data $hyperlocalHelper
     * @param \Ced\CsMarketplace\Helper\Data $marketplaceHelper
     * @param \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ced\CsHyperlocal\Helper\Data $hyperlocalHelper,
        \Ced\CsMarketplace\Helper\Data $marketplaceHelper,
        \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory,
        \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        array $data = []
    )
    {
        $this->messageManager = $messageManager;
        $this->_storeManager = $storeManager;
        $this->hyperlocalHelper = $hyperlocalHelper;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->zipcodeCollection = $zipcodeCollection;
        $this->request = $request;
        $this->productFactory = $productFactory;
        $this->vproductsFactory = $vproductsFactory;
        $this->vendorFactory = $vendorFactory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param \Magento\Catalog\Controller\Product\View $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundExecute(\Magento\Catalog\Controller\Product\View $subject, \Closure $proceed)
    {

        if ($this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::MODULE_ENABLE)) {

            $savedLocationFromSession = $this->hyperlocalHelper->getShippingLocationFromSession();
            $filterType = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_TYPE);
            $radiusConfig = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_RADIUS);
            $distanceType = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::DISTANCE_TYPE);
            $filterProductsBy = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_PRODUCTS_BY);
            $baseUrl = $this->_storeManager->getStore()->getBaseUrl();


            if ($savedLocationFromSession) {

                $productId = $this->request->getParam('id');
                $product = $this->productFactory->create()->load($productId);
                $vendorId = $this->vproductsFactory->create()->getVendorIdByProduct($productId);
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
                        $fromlat = $this->marketplaceHelper->getStoreConfig('ced_cshyperlocal/admin_default_address/latitude');
                        $fromlong = $this->marketplaceHelper->getStoreConfig('ced_cshyperlocal/admin_default_address/longitude');
                    }
                    $distance = $this->hyperlocalHelper->calculateDistancebyHaversine($fromlat, $fromlong, $tolat, $tolong);
                    if ($distance > $radiusConfig) {
                        $subject->getResponse()->setRedirect($baseUrl);
                        $this->messageManager->addErrorMessage(__('Product is not in range'));
                    }
                } elseif ($filterProductsBy == 'vendor_location') {
                    /** Filter Products By Vendor Location */
                    if ($filterType == 'city_state_country') {

                        //------------------- Filter By City,country & state----------------[START]
                        $locationCollection = $this->hyperlocalHelper->getFilteredlocationByCityStateCountry($savedLocationFromSession);
                        if ($locationCollection) {
                            $locationCollection->addFieldToFilter('vendor_id', $vendorId);
                            if (count($locationCollection->getData()) == 0) {
                                $subject->getResponse()->setRedirect($baseUrl);
                                $this->messageManager->addErrorMessage(__('Product is not avaiable for the selected location.'));
                            }
                        } else {
                            $subject->getResponse()->setRedirect($baseUrl);
                            $this->messageManager->addErrorMessage(__('Product is not avaiable for the selected location.'));
                        }
                        //------------------- Filter By City,country & state----------------[END]

                    } elseif ($filterType == 'zipcode' && isset($savedLocationFromSession['filterZipcode'])) {

                        //------------------- Filter By Zipcode----------------[START]
                        $resource = $this->resourceConnection;
                        $tableName = $resource->getTableName('ced_cshyperlocal_shipping_area');
                        $zipcodeCollection = $this->zipcodeCollection->create();
                        $zipcodeCollection->getSelect()->joinLeft($tableName, 'main_table.location_id = ' . $tableName . '.id', array('status', 'is_origin_address'));

                        $isVendorExist = $zipcodeCollection->addFieldToFilter('main_table.zipcode', $savedLocationFromSession['filterZipcode'])
                            ->addFieldToFilter('status', \Ced\CsHyperlocal\Model\Shiparea::STATUS_ENABLED);
                        $isVendorExist->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
                        $isVendorExist->addFieldToFilter('main_table.vendor_id', $vendorId);
                        if ($isVendorExist->count() == 0) {
                            $subject->getResponse()->setRedirect($baseUrl);
                            $this->messageManager->addErrorMessage(__('Product is not avaiable for the selected zipcode'));
                        }
                        //------------------- Filter By Zipcode----------------[END]
                    }

                } elseif ($filterProductsBy == 'product_location') {

                    $pLocationIds = explode(',', $product->getShippingProductLocation());
                    if (count($pLocationIds) > 0) {
                        $exist = false;
                        foreach ($pLocationIds as $locationId) {
                            if ($filterType == 'zipcode' && isset($savedLocationFromSession['filterZipcode'])) {

                                $zipcodeCollection = $this->zipcodeCollection->create();
                                $resource = $this->resourceConnection;
                                $tableName = $resource->getTableName('ced_cshyperlocal_shipping_area');
                                $zipcodeCollection->getSelect()->joinLeft($tableName, 'main_table.location_id = ' . $tableName . '.id', array('status', 'is_origin_address'));
                                $zipcodeFilterCollection = $zipcodeCollection->addFieldToFilter('main_table.zipcode', $savedLocationFromSession['filterZipcode'])
                                    ->addFieldToFilter('status', \Ced\CsHyperlocal\Model\Shiparea::STATUS_ENABLED)
                                    ->addFieldToFilter('main_table.location_id', $locationId);

                                if ($zipcodeCollection->count()) {
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
                            $this->messageManager->addErrorMessage(__('Product is not available for the selected address/zipcode.'));
                        }
                    }
                }
            }
        }
        return $proceed();
    }
}
