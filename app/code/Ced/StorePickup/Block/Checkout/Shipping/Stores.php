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
 * @category  Ced
 * @package   Ced_StorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\StorePickup\Block\Checkout\Shipping;

/**
 * Class Stores
 * @package Ced\StorePickup\Block\Checkout\Shipping
 */
class Stores extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ced\StorePickup\Model\StoreInfo
     */
    protected $storeinfo;

    /**
     * @var \Ced\StorePickup\Model\StoreHour
     */
    protected $storehour;

    /**
     * Stores constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Ced\StorePickup\Model\StoreInfo $storeinfo
     * @param \Ced\StorePickup\Model\StoreHour $storehour
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Ced\StorePickup\Model\StoreInfo $storeinfo,
        \Ced\StorePickup\Model\StoreHour $storehour,
        array $data = []

    )
    {
        parent::__construct($context, $data);
        $this->_storeinfo = $storeinfo;
        $this->_storehour = $storehour;
    }

    /**
     * @return array
     */
    public function getStores()
    {
        return $this->_storeinfo->getCollection()->addFieldToFilter('is_active', '1')->getData();
    }

    /**
     * @param $storeId
     * @param $day
     * @return array
     */
    public function getStoreTimings($storeId, $day)
    {
        $storehours = $this->_storehour->getCollection()->addFieldToFilter('pickup_id', '1')->addFieldToFilter('days', 'Monday')->getData();
        $storetiming = array();
        if (isset($storehours)) {
            foreach ($storehours as $storetmng) {
                $storetiming['start'] = $storetmng['start'];
                $storetiming['end'] = $storetmng['end'];
            }
        }
        return $storetiming;
    }
}