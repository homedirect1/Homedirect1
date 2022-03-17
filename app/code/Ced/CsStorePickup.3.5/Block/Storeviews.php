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
 * @package   Ced_CsStorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsStorePickup\Block;

use Ced\StorePickup\Model\StoreHourFactory;
use Ced\StorePickup\Model\StoreInfoFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Storeviews
 * @package Ced\CsStorePickup\Block
 */
class Storeviews extends Template
{

    protected $countryFactory;

    /**
     * @var StoreHourFactory
     */
    protected $_timeFactory;

    /**
     * @var StoreInfoFactory
     */
    protected $_storesFactory;

    /**
     * @var mixed
     */
    protected $storeId;

    protected $store;

    /**
     * Storeviews constructor.
     * @param Context $context
     * @param CountryFactory $countryFactory
     * @param StoreHourFactory $storeHourFactory
     * @param StoreInfoFactory $storeInfoFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        CountryFactory $countryFactory,
        StoreHourFactory $storeHourFactory,
        StoreInfoFactory $storeInfoFactory,
        array $data = []
    )
    {
        $this->_countryFactory = $countryFactory;
        $this->_request = $context->getRequest();

        $this->_timeFactory = $storeHourFactory;
        $this->_storesFactory = $storeInfoFactory;
        $this->storeId = $this->getRequest()->getParam('storeId');
        $this->store = $this->_storesFactory->create()->load($this->storeId);

        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getStoreManagerName()
    {

        return $this->store->getStoreManagerName();
    }

    /**
     * @return mixed
     */
    public function getStoreName()
    {

        return $this->store->getStoreName();
    }

    /**
     * @return mixed
     */
    public function getStoreAddress()
    {

        return $this->store->getStoreAddress();
    }

    /**
     * @return mixed
     */
    public function getStoreCity()
    {

        return $this->store->getStoreCity();
    }

    /**
     * @return mixed
     */
    public function getStoreState()
    {

        return $this->store->getStoreState();
    }

    /**
     * @return mixed
     */
    public function getStorePincode()
    {

        return $this->store->getStoreZcode();
    }

    /**
     * @return string
     */
    public function getStoreCountryName()
    {

        $store_country = $this->store->getStoreCountry();

        $country = $this->_countryFactory->create()->loadByCode($store_country);
        return $country->getName();
    }

    /**
     * @return array
     */
    public function getStoreLocation()
    {
        $location = [];
        $location['latitude'] = $this->store->getLatitude();
        $location['longitude'] = $this->store->getLongitude();
        return $location;
    }

    /**
     * @return mixed
     */
    public function getContactNumber()
    {

        return $this->store->getStorePhone();
    }

    /**
     * @return array
     */
    public function getStoreOffDays()
    {
        $storeInfos = [];
        $this->_options = [];
        $daysCollection = $this->_timeFactory->create()->getCollection()
            ->addFieldToFilter('status', '0')
            ->addFieldToFilter('pickup_id', $this->storeId)->getData();

        if (isset($daysCollection)) {
            foreach ($daysCollection as $value) {
                $daycode = $this->getDaysCode($value['days']);
                $storeInfos[] = $daycode;
            }
        }
        return $storeInfos;
    }

    /**
     * @param $day
     * @return mixed
     */
    public function getDaysCode($day)
    {

        $days = [
            'MONDAY' => '1',
            'TUESDAY' => '2',
            'WEDNESDAY' => '3',
            'THURSDAY' => '4',
            'FRIDAY' => '5',
            'SATURDAY' => '6',
            'SUNDAY' => '0'
        ];
        return $days[strtoupper($day)];
    }
}