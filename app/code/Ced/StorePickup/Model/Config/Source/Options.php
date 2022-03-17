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

namespace Ced\StorePickup\Model\Config\Source;

use Ced\StorePickup\Model\StoreInfo;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class Options
 * @package Ced\StorePickup\Model\Config\Source
 */
class Options extends AbstractSource
{
    /**
     * @var
     */
    protected $optionFactory;
    /**
     * @var StoreInfo
     */
    protected $_storeinfo;

    /**
     * Options constructor.
     * @param StoreInfo $storeinfo
     */
    public function __construct(
        StoreInfo $storeinfo
    ) {
        $this->_storeinfo = $storeinfo;
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
        /* your Attribute options list*/
        return $this->_options;
    }
}
