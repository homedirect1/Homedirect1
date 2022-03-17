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
use Magento\Directory\Block\Data;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ListStore
 * @package Ced\CsStorePickup\Block
 */
class ListStore extends Template
{

    /**
     * @var StoreFactory
     */
    protected $_storesFactory;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var Data
     */
    protected $_countrys;

    /**
     * @var CountryFactory
     */
    public $countryFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    public $moduleDataSetup;

    /**
     * @var StoreHourFactory
     */
    public $storeHourFactory;

    /**
     * ListStore constructor.
     * @param Context $context
     * @param Data $_countrys
     * @param StoreInfoFactory $storesFactory
     * @param CountryFactory $countryFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param StoreHourFactory $storeHourFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $_countrys,
        StoreInfoFactory $storesFactory,
        CountryFactory $countryFactory,
        ModuleDataSetupInterface $moduleDataSetup,
        StoreHourFactory $storeHourFactory,
        array $data = []
    )
    {
        $this->_request = $context->getRequest();
        $this->_storesFactory = $storesFactory;
        $this->_countrys = $_countrys;
        $this->countryFactory = $countryFactory;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeHourFactory = $storeHourFactory;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
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

        $collection = $this->_storesFactory->create()->getCollection()
            ->addFieldToFilter('is_active', '1')
            ->addFieldToFilter('vendor_id', $this->getRequest()->getParam('shop_id'));

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
        return $this->_scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

}
