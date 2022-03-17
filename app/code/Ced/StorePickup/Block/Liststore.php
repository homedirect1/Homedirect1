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

namespace Ced\StorePickup\Block;

/**
 * Class Liststore
 * @package Ced\StorePickup\Block
 */
class Liststore extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Ced\StorePickup\Model\StoreInfo
     */
    protected $_storesFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Directory\Block\Data
     */
    protected $_countrys;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    public $countryFactory;

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    public $moduleDataSetup;

    /**
     * @var \Ced\StorePickup\Model\StoreHourFactory
     */
    public $storeHourFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * Liststore constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Block\Data $_countrys
     * @param \Ced\StorePickup\Model\StoreInfo $storesFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     * @param \Ced\StorePickup\Model\StoreHourFactory $storeHourFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Block\Data $_countrys,
        \Ced\StorePickup\Model\StoreInfo $storesFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        \Ced\StorePickup\Model\StoreHourFactory $storeHourFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    )
    {
        $this->_request = $context->getRequest();
        $this->_storesFactory = $storesFactory;
        $this->_countrys = $_countrys;
        $this->countryFactory = $countryFactory;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeHourFactory = $storeHourFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getAllStores()
    {
        $data = $this->getRequest()->getPostValue();
        $country = '';
        $state = '';
        $city = '';
        if (isset($data['country_id'])) {
            $country = trim($data['country_id']);
        }

        if (isset($data['region_id'])) {
            $state = trim($data['region_id']);
        }

        if (isset($data['city'])) {
            $city = trim($data['city']);
        }

        $collection = $this->_storesFactory->getCollection()
            ->addFieldToFilter('is_active', '1');

        if ($country) {
            $collection->addFieldToFilter('store_country', array('like' => $country));
        }

        if ($state) {
            $collection->addFieldToFilter('store_state', array('like' => $state));
        }

        if ($city) {
            $collection->addFieldToFilter('store_city', array('like' => $city));
        }
        return $collection;
    }

    /**
     * @return mixed
     */
    public function getFullRouteInfo()
    {
        return $this->_request->getFullActionName();
    }

    /**
     * @return string
     */
    public function getFullCon()
    {
        return $this->_countrys->getCountryHtmlSelect();
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
