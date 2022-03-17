<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_StorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\StorePickup\Model\Config;

use Ced\StorePickup\Model\StoreHour;
use Ced\StorePickup\Model\StoreInfo;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class Stores
 * @package Ced\StorePickup\Model\Config
 */
class Stores extends AbstractSource
{
    /**
     * @var
     */
    protected $optionFactory;
    /**
     * @var StoreHour
     */
    protected $_storehour;
    /**
     * @var StoreInfo
     */
    protected $_storeinfo;

    /**
     * Stores constructor.
     * @param StoreInfo $storeinfo
     * @param StoreHour $storehour
     */
    public function __construct(
        StoreInfo $storeinfo,
        StoreHour $storehour
    ) {
        $this->_storeinfo = $storeinfo;
        $this->_storehour = $storehour;
    }

    /**
     * @return array|null
     */
    public function getAllOptions()
    {
        $stores = [];
        $storeCollection = $this->_storeinfo->getCollection()
            ->addFieldToFilter('is_active', '1')
            ->getData();
        $this->_options=[];
        if (isset($storeCollection)) {
            foreach ($storeCollection as $value) {
                $stores['label'] = $value['store_name'];
                $stores['value'] = $value['pickup_id'];
                array_push($this->_options, $stores);
            }
        }
        return $this->_options;
    }

    /**
     * @param $storeId
     * @param $day
     * @return array
     */
    public function getStoreTimings($storeId, $day)
    {
        $storehours = $this->_storehour->getCollection()
            ->addFieldToFilter('pickup_id', '1')
            ->addFieldToFilter('days', 'Monday')
            ->getData();
        $storetiming = [];
        if (isset($storehours)) {
            foreach ($storehours as $storetmng) {
                $storetiming['start'] = $storetmng['start'];
                $storetiming['end'] = $storetmng['end'];
            }
        }
        return $storetiming;
    }
}
