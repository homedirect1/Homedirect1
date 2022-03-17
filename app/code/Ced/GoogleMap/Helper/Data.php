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
 * @package     Ced_GoogleMap
 * @author 	    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */


namespace Ced\GoogleMap\Helper;


use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Data
 * @package Ced\GoogleMap\Helper
 */
class Data extends AbstractHelper
{
    const XML_GOOGLE_SECTION_PATH = 'ced_google_map/';
    const XML_GOOGLE_GENERAL_GROUP_PATH = self::XML_GOOGLE_SECTION_PATH . 'general/';
    const GOOGLE_MAP_API_KEY = self::XML_GOOGLE_GENERAL_GROUP_PATH . 'google_map_api_key';
    const XML_SALES_SHIPPING_SECTION_PATH = 'shipping/';
    const XML_SALES_SHIPPING_ORIGIN_GROUP_PATH = self::XML_SALES_SHIPPING_SECTION_PATH . 'origin/';
    const XML_SALES_SHIPPING_ORIGIN_LOCATION = self::XML_SALES_SHIPPING_ORIGIN_GROUP_PATH . 'location';
    const XML_SALES_SHIPPING_ORIGIN_LATITUDE = self::XML_SALES_SHIPPING_ORIGIN_GROUP_PATH . 'latitude';
    const XML_SALES_SHIPPING_ORIGIN_LONGITUDE = self::XML_SALES_SHIPPING_ORIGIN_GROUP_PATH . 'longitude';
    const XML_SALES_SHIPPING_ORIGIN_CITY = self::XML_SALES_SHIPPING_ORIGIN_GROUP_PATH . 'city';
    const XML_SALES_SHIPPING_ORIGIN_REGION = self::XML_SALES_SHIPPING_ORIGIN_GROUP_PATH . 'region_id';
    const XML_SALES_SHIPPING_ORIGIN_COUNTRY = self::XML_SALES_SHIPPING_ORIGIN_GROUP_PATH . 'country_id';

    const LOCATION_LATITUDE_FIELD = 'latitude';
    const LOCATION_LONGITUDE_FIELD = 'longitude';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    protected $regionDirectoryFactory;

    /**
     * Config constructor.
     * @param \Magento\Directory\Model\RegionFactory $regionDirectoryFactory
     * @param StoreManagerInterface $storeManager
     * @param Context $context
     */
    public function __construct(
        \Magento\Directory\Model\RegionFactory $regionDirectoryFactory,
        StoreManagerInterface $storeManager,
        Context $context
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->regionDirectoryFactory = $regionDirectoryFactory;
    }

    /**
     * @param $configPath
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($configPath, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return mixed
     */
    public function getGoogleMapApiKey()
    {
        return $this->getConfigValue(
            self::GOOGLE_MAP_API_KEY
        );
    }

    /**
     * @param $fromLat
     * @param $fromLon
     * @param $toLat
     * @param $toLon
     * @return int
     */
    public function calculateDistanceByHaversine($fromLat, $fromLon, $toLat, $toLon)
    {
        $earth_radius = 6371;
        $dLat = deg2rad((float)$toLat - (float)$fromLat);
        $dLon = deg2rad((float)$toLon - (float)$fromLon);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad((float)$fromLat)) * cos(deg2rad((float)$toLat)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c * 1.43;
        return $d;
    }

    public function getAdminLocations()
    {
        $region_name = $region_id = $this->getConfigValue(self::XML_SALES_SHIPPING_ORIGIN_REGION);
        if (is_numeric($region_id))
            $region_name = $this->regionDirectoryFactory->create()->load($region_id)->getName();

        return [
            'location' => $this->getConfigValue(self::XML_SALES_SHIPPING_ORIGIN_LOCATION),
            'latitude' => $this->getConfigValue(self::XML_SALES_SHIPPING_ORIGIN_LATITUDE),
            'longitude' => $this->getConfigValue(self::XML_SALES_SHIPPING_ORIGIN_LONGITUDE),
            'city' => $this->getConfigValue(self::XML_SALES_SHIPPING_ORIGIN_CITY),
            'region_id' => $region_id,
            'state' => $region_name,
            'country_id' => $this->getConfigValue(self::XML_SALES_SHIPPING_ORIGIN_COUNTRY)
        ];
    }
}
