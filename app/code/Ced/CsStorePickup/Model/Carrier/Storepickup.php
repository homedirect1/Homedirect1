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

namespace Ced\CsStorePickup\Model\Carrier;

use Ced\CsMultiShipping\Model\Shipping;
use Ced\CsStorePickup\Helper\Data;
use Ced\StorePickup\Model\StoreHour;
use Ced\StorePickup\Model\StoreInfo;
use Ced\StorePickup\Model\StoreInfoFactory;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Storepickup
 * @package Ced\CsStorePickup\Model\Carrier
 */
class Storepickup extends \Ced\StorePickup\Model\Carrier\Storepickup
{

    /**
     * @var string
     */
    protected $_code = 'storepickupshipping';

    /**
     * @var ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeconfig;

    /**
     * @var \Ced\CsStorePickup\Model\Stores
     */
    protected $_csstoresFactory;

    /**
     * @var \Ced\CsMultiShipping\Helper\Data
     */
    protected $multishippingHelper;

    /**
     * @var Data
     */
    protected $csstorepickupHelper;

    /**
     * Storepickup constructor.
     * @param \Ced\CsMultiShipping\Helper\Data $multishippingHelper
     * @param Data $csstorepickupHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param StoreInfo $storesInfo
     * @param StoreInfoFactory $storesFactory
     * @param StoreHour $timeFactory
     * @param MethodFactory $rateMethodFactory
     * @param CollectionFactory $countryCollection
     * @param array $data
     */
    public function __construct(
        \Ced\CsMultiShipping\Helper\Data $multishippingHelper,
        Data $csstorepickupHelper,
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        StoreInfo $storesInfo,
        StoreInfoFactory $storesFactory,
        StoreHour $timeFactory,
        MethodFactory $rateMethodFactory,
        CollectionFactory $countryCollection,
        array $data = []
    ) {
        $this->_csstoresFactory = $storesFactory;
        $this->multishippingHelper = $multishippingHelper;
        $this->csstorepickupHelper = $csstorepickupHelper;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $rateResultFactory,
            $storesInfo,
            $timeFactory,
            $rateMethodFactory,
            $countryCollection,
            $data
        );
    }

    /**
     * @param RateRequest $request
     * @return bool|Result|void
     * @throws NoSuchEntityException
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->scopeconfig->getValue(
            'carriers/storepickupshipping/active',
            ScopeInterface::SCOPE_STORE
        )) {
            return;
        }

        if (!$this->multishippingHelper->isEnabled()) {
            return parent::collectRates($request);
        }
        if (!$this->csstorepickupHelper->isEnable()) {
            return parent::collectRates($request);
        }
        $vendorId = $request->getVendorId();
        if (!$vendorId) {
            return;
        }
        $csStoreSpecificConfig = [];
        $availableCountries = [];
        $vendor = [];

        if ($vendorId != "admin") {
            $allcountry = false;
            $csStoreSpecificConfig = $request->getVendorShippingSpecifics();
            if ($csStoreSpecificConfig['title']) {
                $csStoreSpecificConfig['title'] = "Sellers Store";
            }
            $availableCountries = $csStoreSpecificConfig['allowed_country'];
            $availableCountries = explode(',', $availableCountries);
            $result = $this->_rateResultFactory->create();
            $storeInfo = $this->_csstoresFactory->create()->getCollection()->addFieldToFilter(
                'is_active',
                1
            )->addFieldToFilter('vendor_id', $vendorId)->getData();

            if (!empty($storeInfo)) {
                if (in_array($request->getDestCountryId(), $availableCountries)) {
                    $method = $this->_rateMethodFactory->create();
                    $method->setVendorId($vendorId);
                    $custom_method = $this->_code . Shipping::SEPARATOR . $vendorId;
                    $method->setCarrier($this->_code);
                    $method->setCarrierTitle($csStoreSpecificConfig['title']);
                    $method->setMethod($custom_method);
                    $method->setMethodTitle($csStoreSpecificConfig['method_name']);
                    $method->setCost($csStoreSpecificConfig['store_price']);
                    $method->setPrice($csStoreSpecificConfig['store_price']);
                    $result->append($method);
                }
                return $result;
            }
        } else {
            return parent::collectRates($request);
        }
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [
            $this->_code => $this->getConfigData('name')
        ];
    }
}
