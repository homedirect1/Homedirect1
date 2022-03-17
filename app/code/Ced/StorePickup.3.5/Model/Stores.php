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

namespace Ced\StorePickup\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class Stores
 * @package Ced\StorePickup\Model
 */
class Stores implements ConfigProviderInterface
{
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
     * @return array
     */
    public function getConfig()
    {
        $config = [];
        foreach ($this->getDays() as $id =>$days) {
            $config['shipping']['days'][$id] = $this->getStoreDays($id);
        }
        $config = array_merge_recursive($config, [
            'shipping' => [
                'storepickup' => [
                    'storelist' => $this->getStores()
                ]
            ]
        ]);

        return $config;
    }
    /**
     * @return array
     */
    public function getStores()
    {
        $stores = [];
        $storeCollection = $this->_storeinfo->getCollection()
            ->addFieldToFilter('is_active', '1')
            ->getData();
        $this->_options = [];
        if (isset($storeCollection)) {
            foreach ($storeCollection as $value) {
                $stores['id'] = $value['pickup_id'];
                $stores['title'] = $value['store_name'];
                array_push($this->_options, $stores);
            }
        }

        return $this->_options;
    }

    /**
     * @return array
     */
    public function getDays()
    {
        $storeInfos = [];
        $daysCollection = $this->_storehour->getCollection()
            ->addFieldToFilter('status', '0')
            ->getData();

        if (isset($daysCollection)) {
            foreach ($daysCollection as $value) {
                $storeInfos[$value['pickup_id']] = $value['days'];
            }
        }

        return $storeInfos;
    }

    /**
     * @param $storeId
     * @return array
     */
    public function getStoreDays($storeId)
    {
        $storeInfos = [];
        $this->_options=[];
        $daysCollection = $this->_storehour->getCollection()
            ->addFieldToFilter('status', '0')
            ->addFieldToFilter('pickup_id', $storeId)
            ->getData();

        if (isset($daysCollection)) {
            foreach ($daysCollection as $value) {
                $daycode = $this->getDaysCode($value['days']);
                $storeInfos[] = $daycode;
            }
        }

        return $storeInfos;
    }

    /**
     * @param $storeId
     * @param $day
     * @return array
     */
    public function getInterval($storeId, $day)
    {
        $storeInfo = [];
        $daysCollection = $this->_storehour->getCollection()
            ->addFieldToFilter('pickup_id', $storeId)
            ->addFieldToFilter('status', '1')
            ->addFieldToFilter('days', $day)
            ->getData();
        $this->_interval = [];
        if (isset($daysCollection)) {
            foreach ($daysCollection as $value) {
                $storeInfo['id'] = $value['start'];
                $storeInfo['title'] = $value['end'];
                array_push($this->_interval, $storeInfo);
            }
        }
        return $this->_interval;
    }

    /**
     * @param $day
     * @return string
     */
    public function getDaysCode($day)
    {
        $days = [
            'MONDAY'=>'1',
            'TUESDAY'=>'2',
            'WEDNESDAY'=>'3',
            'THURSDAY'=> '4',
            'FRIDAY'=>'5',
            'SATURDAY' => '6',
            'SUNDAY'=>'0'
        ];
        return $days[strtoupper($day)];
    }
}
