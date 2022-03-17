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

namespace Ced\CsHyperlocal\Helper;

use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Data
 * @package Ced\CsHyperlocal\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const API_KEY = \Ced\GoogleMap\Helper\Data::GOOGLE_MAP_API_KEY;
    const XML_SECTION_PATH = 'ced_cshyperlocal/';

    const XML_GENERAL_GROUP_PATH = self::XML_SECTION_PATH . 'general/';
    const MODULE_ENABLE = self::XML_GENERAL_GROUP_PATH . 'activation';
    const FILTER_TYPE = self::XML_GENERAL_GROUP_PATH . 'filter_type';
    const POPUP_TITLE = self::XML_GENERAL_GROUP_PATH . 'popup_title';
    const FILTER_RADIUS = self::XML_GENERAL_GROUP_PATH . 'radius';
    const DISTANCE_TYPE = self::XML_GENERAL_GROUP_PATH . 'distance_type';
    const FILTER_PRODUCTS_BY = self::XML_GENERAL_GROUP_PATH . 'filter_products_location';

    const XML_ADMIN_ADDRESS_GROUP_PATH = self::XML_SECTION_PATH . 'admin_default_address/';
    const XML_LOCATION = self::XML_ADMIN_ADDRESS_GROUP_PATH . 'location';
    const XML_LATITUDE = self::XML_ADMIN_ADDRESS_GROUP_PATH . 'latitude';
    const XML_LONGITUDE = self::XML_ADMIN_ADDRESS_GROUP_PATH . 'longitude';

    const SKIPPED_PRODUCT_SKU = 'skp_virtual_';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Ced\CsHyperlocal\Model\ZipcodeFactory
     */
    protected $zipcode;

    /**
     * @var \Ced\CsHyperlocal\Cookie\Savelocation
     */
    protected $savelocation;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory
     */
    protected $shipareaCollectionFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    protected $countryCollection;

    protected $jsonSerializer;

    /**
     * Data constructor.
     * @param Json $jsonSerializer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcode
     * @param \Ced\CsHyperlocal\Cookie\Savelocation $savelocation
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollection
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        Json $jsonSerializer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcode,
        \Ced\CsHyperlocal\Cookie\Savelocation $savelocation,
        \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollection,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->zipcode = $zipcode;
        $this->savelocation = $savelocation;
        $this->shipareaCollectionFactory = $shipareaCollectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->countryCollection = $countryCollection;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @return mixed
     */
    public function isModuleEnabled()
    {
        return $this->getStoreConfig(self::MODULE_ENABLE);
    }

    /**
     * @return mixed
     */
    public function getShippingLocationFromSession()
    {
        $location = $this->savelocation->get();
        if ($location) {
            return $this->jsonSerializer->unserialize($location);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function unsetSessionData()
    {
        $this->savelocation->delete();
        return true;
    }

    /**
     * @param $path
     * @param null $storeId
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreConfig($path, $storeId = null)
    {
        $store = $this->_storeManager->getStore($storeId);
        return $this->scopeConfig->getValue($path, 'store', $store->getCode());
    }

    /**
     * @param $locationId
     */
    public function getZipcodeByLocationId($locationId)
    {
        return $this->zipcode->create()->load($locationId, 'location_id')->getZipcode();
    }

    /** get locations for filter type city,country and state */
    public function getFilteredlocationByCityStateCountry($sessionData)
    {
        if ($sessionData['country'] != '' && $sessionData['city'] == '' && $sessionData['state'] == '') { 
            $collection1 = $this->shipareaCollectionFactory->create();
            $collection1->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
            $collection1->addFieldToFilter('status', \Ced\CsHyperlocal\Model\Shiparea::STATUS_ENABLED);
            if (count($collection1->getData()) > 0) {
                $collection1 = $this->shipareaCollectionFactory->create();
                $collection1->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
                $collection1->addFieldToFilter('status', \Ced\CsHyperlocal\Model\Shiparea::STATUS_ENABLED);
                $collection1->addFieldToFilter('country', ['like' => '%' . $sessionData['country'] . '%'])
                    ->addFieldToFilter('state', ['eq' => null])
                    ->addFieldToFilter('city', ['eq' => null]);

                return $collection1;
            } else {
                return false;
            }
        } elseif ($sessionData['country'] != '' && $sessionData['state'] != '' && $sessionData['city'] == '') {

            $coreResource = $this->resourceConnection;
            $shipareaTable = $coreResource->getTableName('ced_cshyperlocal_shipping_area');
            $collection2 = $this->shipareaCollectionFactory->create();
            $collection2->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
            $collection2->addFieldToFilter('status', \Ced\CsHyperlocal\Model\Shiparea::STATUS_ENABLED);
            $collection2->getSelect()
                    ->where("(main_table.country = '{$sessionData['country']}' AND main_table.state = '') OR 
                    (main_table.country = '{$sessionData['country']}' AND main_table.state = '{$sessionData['state']}') AND (main_table.city = '')");
            if ($collection2)
            {
                return $collection2;
            } else {
                return false;
            }
        } elseif ($sessionData['country'] != '' && $sessionData['city'] != '' && $sessionData['state'] != '') {

            $coreResource = $this->resourceConnection;
            $shipareaTable = $coreResource->getTableName('ced_cshyperlocal_shipping_area');
            $collection3 = $this->shipareaCollectionFactory->create();
            $collection3->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
            $collection3->addFieldToFilter('status', \Ced\CsHyperlocal\Model\Shiparea::STATUS_ENABLED);
            $collection3->getSelect()
                    ->where("(main_table.country = '{$sessionData['country']}' AND main_table.state = '' AND main_table.city = '') 
                OR (main_table.country = '{$sessionData['country']}' AND main_table.state = '{$sessionData['state']}' AND main_table.city = '') 
                OR (main_table.country = '{$sessionData['country']}' AND main_table.state = '{$sessionData['state']}' AND main_table.city = '{$sessionData['city']}') ");
            if ($collection3)
            {
                return $collection3;
            } else {
                return false;
            }
            
        } elseif ($sessionData['country'] != '' && $sessionData['city'] != '' && $sessionData['state'] == '') {

            $coreResource = $this->resourceConnection;
            $shipareaTable = $coreResource->getTableName('ced_cshyperlocal_shipping_area');
            $collection3 = $this->shipareaCollectionFactory->create();
            $collection3->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
            $collection3->addFieldToFilter('status', \Ced\CsHyperlocal\Model\Shiparea::STATUS_ENABLED);
            $collection3->getSelect()
                ->where("(main_table.country = '{$sessionData['country']}' AND main_table.state = '' AND main_table.city = '') 
                OR (main_table.country = '{$sessionData['country']}' AND main_table.city = '{$sessionData['city']}') ");
            if ($collection3) {
                return $collection3;
            } else {
                return false;
            }
        }
    }

    /**
     * @param $string
     * @return string
     */
    public function getLimitedText($string)
    {
        $strLength = 25;
        if(strlen($string)<=$strLength)
        {
            return $string;
        }
        else
        {
            return substr($string,0,$strLength) . '...';
        }
    }

    /**
     * @param $fromLat
     * @param $fromLon
     * @param $toLat
     * @param $toLon
     * @return int
     */
    public function calculateDistancebyHaversine($fromLat, $fromLon, $toLat, $toLon)
    {
        $earth_radius = 6371;
        $dLat = deg2rad((float)$toLat - (float)$fromLat);
        $dLon = deg2rad((float)$toLon - (float)$fromLon);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad((float)$fromLat)) * cos(deg2rad((float)$toLat)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c * 1.43;
        return $d;
    }

    public function getCountryId($countryName){
        $countryCollection = $this->countryCollection->create();

        foreach ($countryCollection as $country) {
            if ( $countryName == $country->getName()) {
                $countryId = $country->getCountryId();
                break;
            }
        }

        return $countryId;

}
}
