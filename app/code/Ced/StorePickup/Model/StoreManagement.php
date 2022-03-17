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

use Ced\StorePickup\Api\Data\StoreInterface;

/**
 * Class StoreManagement
 * @package Ced\StorePickup\Model
 */
class StoreManagement
{
    /**
     * @var
     */
    protected $storeFactory;
    /**
     * @var StoreInfo
     */
    protected $_storeinfo;

    /**
     * StoreManagement constructor.
     * @param StoreInterfaceFactory $storeInterfaceFactory
     */
    public function __construct(
        StoreInfo $storeinfo
    ) {
        $this->_storeinfo = $storeinfo;
    }

    /**
     * Get stores for the given postcode and city
     *
     * @param string $postcode
     * @param $city
     * @return StoreInterface[]
     */
    public function fetchNearestStores($postcode, $city)
    {
        $result = [];
        $stores = [];
        $storeCollection = $this->_storeinfo->getCollection()
            ->addFieldToFilter('is_active', '1')->getData();
        if (isset($storeCollection)) {
            foreach ($storeCollection as $value) {
                $stores['label'] = $value['store_name'];
                $stores['value'] = $value['pickup_id'];
                array_push($result, $stores);
            }
        }
        return $result;
    }
}
