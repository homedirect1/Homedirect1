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
 * @package     Ced_StorePickup
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\StorePickup\Helper;

use Ced\StorePickup\Model\ResourceModel\StoreHour\CollectionFactory;
use Ced\StorePickup\Model\ResourceModel\StoreInfo;
use Ced\StorePickup\Model\Source\Updates\Type;
use Ced\StorePickup\Model\StoreHour;
use Ced\StorePickup\Model\StoreInfoFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\ResourceModel\Country;
use Magento\Framework\App\Config\ValueInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Data
 * @package Ced\StorePickup\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $_allowedFeedType = [];
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfigManager;
    /**
     * @var ValueInterface
     */
    protected $_configValueManager;

    /**
     * @var Transaction
     */
    protected $_transaction;
    /**
     * @var Context
     */
    protected $_context;
    /**
     * @var ProductMetadataInterface
     */
    protected $_productMetadata;
    /**
     * @var int
     */
    protected $_storeId = 0;
    /**
     * @var StoreInfoFactory
     */
    protected $_storesFactory;
    /**
     * @var
     */
    protected $storehour;
    /**
     * @var StoreHour
     */
    protected $_storehour;
    /**
     * @var CountryFactory
     */
    protected $country;
    /**
     * @var Country
     */
    protected $countryResource;
    /**
     * @var StoreInfo
     */
    protected $storeInfo;
    /**
     * @var CollectionFactory
     */
    protected $storeHourCollection;

    /**
     * Data constructor.
     * @param Context $context
     * @param ProductMetadataInterface $productMetadata
     * @param StoreManagerInterface $storeManager
     * @param ValueInterface $_configValueManager
     * @param Transaction $transaction
     * @param StoreInfoFactory $_storesFactory
     * @param StoreHour $storehour
     * @param Country $countryResource
     * @param CountryFactory $country
     */
    public function __construct(
        Context $context,
        ProductMetadataInterface $productMetadata,
        StoreManagerInterface $storeManager,
        ValueInterface $_configValueManager,
        Transaction $transaction,
        StoreInfoFactory $_storesFactory,
        StoreHour $storehour,
        Country $countryResource,
        CountryFactory $country,
        StoreInfo $storeInfo,
        CollectionFactory $storeHourCollection
    ) {
        $this->_context = $context;
        $this->_productMetadata = $productMetadata;
        $this->_storeManager = $storeManager;
        $this->_scopeConfigManager = $context->getScopeConfig();
        $this->_configValueManager = $_configValueManager;
        $this->_transaction = $transaction;
        $this->_storehour = $storehour;
        $this->_storesFactory = $_storesFactory;
        $this->country = $country;
        $this->countryResource = $countryResource;
        $this->storeInfo = $storeInfo;
        $this->storeHourCollection = $storeHourCollection;
        parent::__construct($context);
    }

    /**
     * Set a specified store ID value
     *
     * @param int $store
     * @return $this
     */
    public function setStoreId($store)
    {
        $this->_storeId = $store;
        return $this;
    }

    /**
     * Get current store
     *
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStore()
    {
        if ($this->_storeId) {
            $storeId = (int)$this->_storeId;
        } else {
            $storeId = isset($_REQUEST['store']) ? (int)$_REQUEST['store'] : null;
        }
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getCustomCSS()
    {
        return $this->_scopeConfigManager->getValue(
            'ced_csmarketplace/vendor/theme_css',
            ScopeInterface::SCOPE_STORE,
            $this->getStore()->getId()
        );
    }

    /**
     * Check if current url is url for home page
     *
     * @return true
     */
    public function getIsDashboard()
    {
        return $this->getVendorUrl() == $this->_getUrl('*/*/*')
            ||
            $this->getVendorUrl() . '/index' == $this->_getUrl('*/*/*')
            ||
            $this->getVendorUrl() . '/index/' == $this->_getUrl('*/*/*')
            ||
            $this->getVendorUrl() . 'index' == $this->_getUrl('*/*/*')
            ||
            $this->getVendorUrl() . 'index/' == $this->_getUrl('*/*/*');
    }

    /**
     * @param $logo_src
     * @param $logo_alt
     * @return $this
     */
    public function setLogo($logo_src, $logo_alt)
    {
        $this->setLogoSrc($logo_src);
        $this->setLogoAlt($logo_alt);
        return $this;
    }

    /**
     * @return string
     */
    public function getMarketplaceVersion()
    {
        return trim((string)$this->getReleaseVersion('Ced_CsMarketplace'));
    }

    /**
     * @param $module
     * @return bool|string
     */
    public function getReleaseVersion($module)
    {
        $modulePath = $this->moduleRegistry->getPath(self::XML_PATH_INSTALLATED_MODULES, $module);
        $filePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, "$modulePath/etc/module.xml");
        $source = new \Magento\Framework\Simplexml\Config($filePath);
        if ($source->getNode(self::XML_PATH_INSTALLATED_MODULES)->attributes()->release_version) {
            return $source->getNode(self::XML_PATH_INSTALLATED_MODULES)->attributes()->release_version->__toString();
        }
        return false;
    }

    /**
     * Url encode the parameters
     *
     * @param string | array
     * @return string | array | boolean
     */
    public function prepareParams($data)
    {
        if (!is_array($data) && strlen($data)) {
            return urlencode($data);
        }
        if ($data && is_array($data) && count($data) > 0) {
            foreach ($data as $key => $value) {
                $data[$key] = urlencode($value);
            }
            return $data;
        }
        return false;
    }

    /**
     * Url decode the parameters
     *
     * @param string | array
     * @return string | array | boolean
     */
    public function extractParams($data)
    {
        if (!is_array($data) && strlen($data)) {
            return urldecode($data);
        }
        if ($data && is_array($data) && count($data) > 0) {
            foreach ($data as $key => $value) {
                $data[$key] = urldecode($value);
            }
            return $data;
        }
        return false;
    }

    /**
     * Add params into url string
     *
     * @param string $url (default '')
     * @param array $params (default array())
     * @param boolean $urlencode (default true)
     * @return string | array
     */
    public function addParams($url = '', $params = [], $urlencode = true)
    {
        if (count($params) > 0) {
            foreach ($params as $key => $value) {
                if (parse_url($url, PHP_URL_QUERY)) {
                    if ($urlencode) {
                        $url .= '&' . $key . '=' . $this->prepareParams($value);
                    } else {
                        $url .= '&' . $key . '=' . $value;
                    }
                } else {
                    if ($urlencode) {
                        $url .= '?' . $key . '=' . $this->prepareParams($value);
                    } else {
                        $url .= '?' . $key . '=' . $value;
                    }
                }
            }
        }
        return $url;
    }

    /**
     * Retrieve all the extensions name and version developed by CedCommerce
     *
     * @param boolean $asString (default false)
     * @return array|string
     */
    public function getCedCommerceExtensions($asString = false)
    {
        if ($asString) {
            $cedCommerceModules = '';
        } else {
            $cedCommerceModules = [];
        }
        $allModules = $this->_context->getScopeConfig()->getValue(\Ced\StorePickup\Model\Feed::XML_PATH_INSTALLATED_MODULES);
        $allModules = json_decode(json_encode($allModules), true);
        foreach ($allModules as $name => $module) {
            $name = trim($name);
            if (preg_match('/ced_/i', $name) && isset($module['release_version'])) {
                if ($asString) {
                    $cedCommerceModules .= $name . ':' . trim($module['release_version']) . '~';
                } else {
                    $cedCommerceModules[$name] = trim($module['release_version']);
                }
            }
        }
        if ($asString) {
            trim($cedCommerceModules, '~');
        }
        return $cedCommerceModules;
    }

    /**
     * Retrieve environment information of magento
     * And installed extensions provided by CedCommerce
     *
     * @return array
     */
    public function getEnvironmentInformation()
    {
        $info = [];
        $info['domain_name'] = $this->_productMetadata->getBaseUrl();
        $info['magento_edition'] = 'default';
        if (method_exists('Mage', 'getEdition')) {
            $info['magento_edition'] = $this->_productMetadata->getEdition();
        }
        $info['magento_version'] = $this->_productMetadata->getVersion();
        $info['php_version'] = phpversion();
        $info['feed_types'] = $this->getStoreConfig(\Ced\StorePickup\Model\Feed::XML_FEED_TYPES);
        $info['installed_extensions_by_cedcommerce'] = $this->getCedCommerceExtensions(true);

        return $info;
    }

    /**
     * Retrieve admin interest in current feed type
     *
     * @param SimpleXMLElement $item
     * @return boolean $isAllowed
     */
    public function isAllowedFeedType(SimpleXMLElement $item)
    {
        $isAllowed = false;
        if (is_array($this->_allowedFeedType) && count($this->_allowedFeedType) > 0) {
            $cedModules = $this->getCedCommerceExtensions();
            switch (trim((string)$item->update_type)) {
                case Type::TYPE_NEW_RELEASE:
                case Type::TYPE_INSTALLED_UPDATE:
                    if (in_array(Type::TYPE_INSTALLED_UPDATE, $this->_allowedFeedType) && strlen(trim($item->module)) > 0 && isset($cedModules[trim($item->module)]) && version_compare($cedModules[trim($item->module)], trim($item->release_version), '<') === true) {
                        $isAllowed = true;
                        break;
                    }
                // no break
                case Type::TYPE_UPDATE_RELEASE:
                    if (in_array(Type::TYPE_UPDATE_RELEASE, $this->_allowedFeedType) && strlen(trim($item->module)) > 0) {
                        $isAllowed = true;
                        break;
                    }
                    if (in_array(Type::TYPE_NEW_RELEASE, $this->_allowedFeedType)) {
                        $isAllowed = true;
                    }
                    break;
                case Type::TYPE_PROMO:
                    if (in_array(Type::TYPE_PROMO, $this->_allowedFeedType)) {
                        $isAllowed = true;
                    }
                    break;
                case Type::TYPE_INFO:
                    if (in_array(Type::TYPE_INFO, $this->_allowedFeedType)) {
                        $isAllowed = true;
                    }
                    break;
            }
        }
        return $isAllowed;
    }

    /**
     * Function for setting Config value of current store
     *
     * @param string $path ,
     * @param string $value ,
     */
    public function setStoreConfig($path, $value, $storeId = null)
    {
        $store = $this->_storeManager->getStore($storeId);
        $data = [
            'path' => $path,
            'scope' => 'stores',
            'scope_id' => $storeId,
            'scope_code' => $store->getCode(),
            'value' => $value,
        ];
        $this->_configValueManager->addData($data);
        $this->_transaction->addObject($this->_configValueManager);
        $this->_transaction->save();
    }

    /**
     * Function for getting Config value of current store
     *
     * @param string $path ,
     */
    public function getStoreConfig($path, $storeId = null)
    {
        $store = $this->_storeManager->getStore($storeId);
        return $this->_scopeConfigManager->getValue($path, 'store', $store->getCode());
    }

    /**
     * @param $pickupId
     * @return mixed
     */
    public function getStoreDetail($pickupId)
    {
        return $this->_storesFactory->create()->load($pickupId);
    }

    /**
     * @param $storeId
     * @param $day
     * @return array
     */
    public function getStoreTimings($storeId, $day)
    {
        $storehours = $this->storeHourCollection->create()
            ->addFieldToFilter('pickup_id', $storeId)
            ->addFieldToFilter('days', $day)
            ->getData();
        $storetiming = [];
        if (isset($storehours)) {
            foreach ($storehours as $storetmng) {
                $storetiming['start'] = $storetmng['start'];
                $storetiming['end'] = $storetmng['end'];
                $storetiming['status'] = $storetmng['status'];
            }
        }
        return $storetiming;
    }

    /**
     * @param $countryId
     * @return string
     */
    public function getCountryName($countryId)
    {
        if ($countryId) {
            $country = $this->country->create();
            $this->countryResource->load($country, $countryId);
            return $country->getName();
        }
    }

    /**
     * @param $order
     * @return array
     */
    public function getStorePickupData($order)
    {
        $itemArray = [];
        $storedata = $order->getStorePickupData();
        foreach ($order->getAllVisibleItems() as $item) {
            if ($item->getVendorId()) {
                $itemArray[$item->getVendorId()] = $item->getName();
            } else {
                $itemArray[0] = $item->getName();
            }
        }
        $storesData = [];
        if ($storedata) {
            $storedata = explode('#', $storedata);
            $storeData = array_filter($storedata);
            foreach ($storeData as $_storedata) {
                $data = explode(':', $_storedata);
                $storepickupdata['item_name'] = reset($itemArray);
                $storepickupdata['vendor_id'] = $data[0];
                $storepickupdata['pickup_id'] = $data[1];
                $storepickupdata['pickup_date'] = $data[2];
                $storesData[] = $storepickupdata;
            }
        }
        return $storesData;
    }

    /**
     * @param $storepickupdata
     * @return \Ced\StorePickup\Model\StoreInfo
     */
    public function getPickupStore($storepickupdata)
    {
        $store = $this->_storesFactory->create();
        if ($store && isset($storepickupdata['pickup_id'])) {
            $this->storeInfo->load($store, $storepickupdata['pickup_id'], 'pickup_id');
        }
        return $store;
    }

    /**
     * @param $order
     * @return bool
     */
    public function isStorePickupShipping($order)
    {
        $string = 'storepickupshipping_storepickupshipping';
        $shippingMethod = $order->getShippingMethod();
        $pos = strpos($shippingMethod, $string);
        if ($pos) {
            return true;
        }
        return false;
    }
}
