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

namespace Ced\StorePickup\Model\Carrier;

use Ced\StorePickup\Model\StoreHour;
use Ced\StorePickup\Model\StoreInfo;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Storepickup
 * @package Ced\StorePickup\Model\Carrier
 */
class Storepickup extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'storepickupshipping';
    /**
     * @var
     */
    protected $_logger;
    /**
     * @var bool
     */
    protected $_isFixed = true;
    /**
     * @var ResultFactory
     */
    protected $_rateResultFactory;
    /**
     * @var StoreInfo
     */
    protected $_storesFactory;
    /**
     * @var StoreHour
     */
    protected $_timeFactory;
    /**
     * @var MethodFactory
     */
    protected $_rateMethodFactory;
    /**
     * @var CollectionFactory
     */
    protected $_countryCollection;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        StoreInfo $storesFactory,
        StoreHour $timeFactory,
        MethodFactory $rateMethodFactory,
        CollectionFactory $countryCollection,
        array $data = []
    ) {
        $this->_countryCollection = $countryCollection;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_storesFactory = $storesFactory;
        $this->_timeFactory = $timeFactory;
        $this->scopeconfig = $scopeConfig;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $result = $this->_rateResultFactory->create();
        $options = $this->_countryCollection->create()->loadByStore()
            ->setForegroundCountries($this->getTopDestinations())
            ->toOptionArray();
        $availableCountries = $this->scopeconfig->getValue(
            'carriers/storepickupshipping/specificcountry'
        );
        $title = $this->scopeconfig->getValue('carriers/storepickupshipping/title');
        $method_name = $this->scopeconfig->getValue('carriers/storepickupshipping/name');
        if ($availableCountries != '') {
            $availableCountries=explode(',', $availableCountries);
        } else {
            foreach ($options as $value) {
                $availableCountries[] = $value['value'];
            }
        }

        $result =  $this->_rateResultFactory->create();
        $savedrates =$this->scopeconfig->getValue('carriers/storepickupshipping/shipping_price');
        if (empty($savedrates)) {
            $savedrates = 0;
        }
        $destCountryId = $request->getDestCountryId();
        $storeInfo = $this->_storesFactory->getCollection()
            ->addFieldToFilter('is_active', 1)
            ->getData();

        if (!empty($storeInfo)) {
            if (in_array($request->getDestCountryId(), $availableCountries)) {
                $method = $this->_rateMethodFactory->create();
                $custom_method = $this->_code;
                $method->setCarrier($this->_code);
                $method->setCarrierTitle(__($title));
                $method->setMethod($custom_method);
                $method->setMethodTitle(__($method_name));
                $method->setCost($savedrates);
                $method->setPrice($savedrates);
                $result->append($method);
            } else {
                $error = $this->_rateErrorFactory->create();
                $error->setCarrier($this->_code);
                $error->setCarrierTitle($this->getConfigData('title'));
                $error->setErrorMessage($this->getConfigData('specificerrmsg'));
                $result->append($error);
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['storepickupshipping' => $this->getConfigData('name')];
    }
}
