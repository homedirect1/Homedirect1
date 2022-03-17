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
 * @package   Ced_CsFreeshipping
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
 
namespace Ced\CsFreeshipping\Model\Carrier;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;

class Free extends \Magento\OfflineShipping\Model\Carrier\Freeshipping
{

    protected $_code = 'freeshipping';

    protected $_isFixed = true;


    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var \Ced\Advancedmatrix\Model\Resource\Carrier\AdvancedrateFactory
     */
    protected $_tablerateFactory;
    protected $dataHelper;
    protected $configHelper;
    protected $_objectManager;
    protected $_vendorFactory;
    protected $scopeConfig;


    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_vendorFactory = $vendorFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_objectManager = $objectManager;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $rateResultFactory, $rateMethodFactory, $data);

    }

    /**
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result
     */
    public function collectRates(RateRequest $request)
    {
        if(!$this->scopeConfig->getValue('carriers/freeshipping/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return; 
        }
        
        if(!$this->_objectManager->get('Ced\CsMultiShipping\Helper\Data')->isEnabled()) {
            return parent::collectRates($request);
        }
        if(!$this->_objectManager->get('Ced\CsFreeshipping\Helper\Data')->isEnabled()) {
            return parent::collectRates($request); 
        }
        
        $vendorId = $request->getVendorId();
        if(!$vendorId) {
            return; 
        }

        $items = $request->getVendorItems();            
        $freeSpecificConfig = array();
        $availableCountries = array();
        $vendor = array();
            
        if($vendorId!="admin") {
            
            $allcountry = false;
            $freeSpecificConfig = $request->getVendorShippingSpecifics();
            $setting=$freeSpecificConfig['min_order_amount'];
            $availableCountries=$freeSpecificConfig['allowed_country'];
            $availableCountries=explode(',', $availableCountries);
            $result =  $this->_rateResultFactory->create();
            $rate =  $this->_rateMethodFactory->create();
            if(($request->getFreeShipping()) || ($request->getBaseSubtotalInclTax() >= $setting)) {
                    
                if(in_array($request->getDestCountryId(), $availableCountries)) {
                    
                    $rate->setVendorId($vendorId);
                    $rate->setCarrier($this->_code);
                    $rate->setCarrierTitle($this->_code);
                    $custom_method = $this->_code.\Ced\CsMultiShipping\Model\Shipping::SEPARATOR.$vendorId;
                    
                    $rate->setMethod($custom_method);
                    $rate->setMethodTitle('Free Shipping');
                    $rate->setCost('0.00');
                    $rate->setPrice('0.00');
                    $result->append($rate);
                }
            }
            return $result;
        }
        else{
            return parent::collectRates($request);
        }
      
    }
    

    
   
}
