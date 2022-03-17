<?php

/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_Perkmshipping
  * @author   CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

namespace Ced\Perkmshipping\Model\Carrier;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;

class Perkmshipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{

    protected $_code = 'perkmshipping';
    
    protected $_url = 'https://maps.googleapis.com/maps/api/distancematrix/';

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    
    protected $_rateMethodFactory;
    
    protected $_request;


    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
    	$this->scopeConfig = $scopeConfig;
    	$this->_request = $request;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

     }
    
 public function collectRates(RateRequest $request)
    {
    	if(!$this->getConfigData('active'))
    		return false;
    	
    	try 
    	{
	    	$country = $this->_scopeConfig->getValue('shipping/origin/country_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	    	$city = $this->_scopeConfig->getValue('shipping/origin/city', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	    	$state = $this->_scopeConfig->getValue('shipping/origin/region_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	    	$postcode = $this->_scopeConfig->getValue('shipping/origin/postcode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	    	$strt = $this->_scopeConfig->getValue('shipping/origin/street_line1', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	    	$adres2 = $strt.','.$postcode.','.$city.','.$country;
	    	
	    	$distMetric		    = $this->getConfigData('metric');
	    	$distRound    		= $this->getConfigData('dist_roundoff');
	    	$priceRound 	    = $this->getConfigData('total_price');
	    	$shipZones	    = json_decode($this->getConfigData('shipping_zones'),true);
	    	$priceMin 	 	    = $this->getConfigData('price_min');
	    	$priceMax 	 	    = $this->getConfigData('price_max');
	    	$minMax				= $this->getConfigData('minmax');
	    	$freeShip	  		= $this->getConfigData('freeshipping');
	    	$freeTotal	    	= $this->getConfigData('free_shipping_subtotal');
	    	$min	   			= $this->getConfigData('minimum');
	    	$minTotal 			= $this->getConfigData('minimum_subtotal');
	    	$specificerrmsg		= $this->getConfigData('specificerrmsg');
	    	$adres1 	 	    = $this->getConfigData('address');
	    	$addCalculation		= $this->getConfigData('address_calc');
	    	$distMax 	   	    = $this->getConfigData('distance_max');
			$api_key            = $this->getConfigData('apikey');
	    	$distance_text      ='';
	    	if($adres1){
	    		$address = $adres1;
	    	}else{
	    		$address = $adres2;
	    	}
	    	
	    	// Minumum Value
	    	// if($min && ($minTotal > $request->getBaseSubtotalInclTax())) {
	    	// 	if($specificerrmsg) {
	    	// 		$error = $this->_rateErrorFactory->create();
	    	// 		$error->setCarrier($this->_code);
	    	// 		$error->setCarrierTitle($this->getConfigData('title'));
	    	// 		$error->setErrorMessage($specificerrmsg);
	    	// 	} else {
	    	// 		return false;
	    	// 	}
	    	// }
	    	
	    	if($distMetric == 'metric') {
	    		$distance_m = 'km';
	    	} else {
	    		$distance_m = 'mi';
	    	}
	    	
	    	if(($request->getDestCity() && $request->getDestCountryId()) || $request->getDestPostcode()) {
	    		if($request->getDestPostcode()) {
	    			$postcode = $request->getDestPostcode();
	    		} else {
	    			$postcode = '';
	    		}
	    		$shipfrom = str_replace(' ', '+', $address);
	    	
	    		$street ='';
	    			
	    		if($addCalculation == 'full') {
	    				
	    			if($request->getDestStreet()) {
	    	
	    				$street = $request->getDestStreet();
	    			} else {
	    				$street = '';
	    			}
	    			if($this->_request->getControllerName() == 'cart'){
	    				$street = '';
	    			}
	    			$shipto = str_replace(' ', '+',($street . ',+' . $postcode . ',+' . $request->getDestCity() . ',+' . $request->getDestCountryId()));
	    			
	    		} else {
	    			$shipto = str_replace(' ', '+',($postcode . ',+' . $request->getDestCity() . ',+' . $request->getDestCountryId()));

	    			
	    				
	    		}
	    		$url = empty($api_key) ? $this->_url.'json?origins=' . $shipfrom . '&destinations=' . $shipto . '&mode=driving&sensor=false&units=' . $distMetric : $this->_url.'json?origins=' . $shipfrom . '&destinations=' . $shipto . '&mode=driving&sensor=false&units=' . $distMetric.'&key='.$api_key;
	    		$url = str_replace("#", "",$url);
	    		

	    		$result = @file_get_contents($url);
	    		if(!$result) {
	    			$ch = curl_init();
	    			curl_setopt($ch, CURLOPT_URL, $url);
	    			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    			$data = json_decode(curl_exec($ch), true);
	    		} else {
	    			$data = json_decode(utf8_encode($result), true);
	    		}
	    	  
			
			//API-KEY Error Message For User
	    			if(isset($data['error_message'])) {
	    			$error = $this->_rateErrorFactory->create();
	    			$error->setCarrier($this->_code);
	    			$error->setCarrierTitle($this->getConfigData('title'));
	    			$error->setErrorMessage($data['error_message']);
	    			return $error;
	    		}
			
	    		$status = $data['status'];
	    			
	    		if(isset($data['rows'][0]['elements'][0]['distance']['value'])) {
	    			$distance = $data['rows'][0]['elements'][0]['distance']['value'];
	    				
	    			if($distMetric == 'metric') {
	    				$distkm = ($distance / 1000);
	    			} else {
	    				$distkm = ($distance / 1609);
	    			}
	    		} else {
	    			$distance = '';
	    			$distkm = '';
	    		}
	    	
	    		switch ($distRound) {
	    			case '2':
	    				$distkm = round($distkm, 2);
	    				break;
	    			case '1':
	    				$distkm = round($distkm, 1);
	    				break;
	    			case '0':
	    				$distkm = round($distkm, 0);
	    				break;
	    			case 'C':
	    				$distkm = ceil($distkm);
	    				break;
	    			case 'F':
	    				$distkm = floor($distkm);
	    				break;
	    		}
	    			
	    		// Format distance
	    		if($distMetric == 'metric') {
	    			$distance_text = number_format($distkm, 2, '.', '') . ' ' . $distance_m;
	    		} else {
	    			$distance_text = number_format($distkm, 2, '.', '') . ' ' . $distance_m;
	    		}
	    	
	    			
	    	} else {
	    		$status = 'OK';
	    		$distkm = '0';
	    	}
	    		
	    	
	    	// Google down
	    	if($status != 'OK')
	    		return false;
	    		
	    	// No Distcance
	    	if(!isset($data['rows'][0]['elements'][0]['distance']['value'])) {
	    		if($specificerrmsg) {
	    			$error = $this->_rateErrorFactory->create();
	    			$error->setCarrier($this->_code);
	    			$error->setCarrierTitle($this->getConfigData('title'));
	    			$error->setErrorMessage($specificerrmsg);
	    		} else {
	    			return false;
	    		}
	    	}
	    		
	    	// Distance > Max distance
	    	if(($distMax) && ($distkm > $distMax)) {
	    		if($specificerrmsg) {
	    			$error = $this->_rateErrorFactory->create();
	    			$error->setCarrier($this->_code);
	    			$error->setCarrierTitle($this->getConfigData('title'));
	    			$error->setErrorMessage($specificerrmsg);
	    		} else {
	    			return false;
	    		}
	    	}
	    		
	    	// PRICE
	    	$price = '0.00';
	    		
	    	// Loop Through Zones
	    	$distance_left = $distkm;
	    	foreach($shipZones as $shipping_zone) {
	    		if($shipping_zone['from'] <= $distkm) {
	    			$zone = ($shipping_zone['to'] - $shipping_zone['from']);
	    			//echo $distance_left;die;
	    			if($distance_left > $zone) {
	    				if($shipping_zone['type'] == 'fixed') {
	    					$price += $shipping_zone['price'];
	    				} else {
	    					$price += ($shipping_zone['price'] * $zone);
	    				}
	    			} else {
	    				if($shipping_zone['type'] == 'fixed') {
	    					$price += (($shipping_zone['price']/$zone)*$distance_left);
	    				} else {
	    					$price += ($shipping_zone['price'] * $distance_left);
	    				}
	    			}
	    			$distance_left = ($distance_left - $zone);
	    			if($distance_left < 0) {
	    				break;
	    			}
	    		}
	    	}
	    		
	    	// Rounding
	    	switch($priceRound) {
	    		case '2':
	    			$price = round($price, 2);
	    			break;
	    		case '1':
	    			$price = round($price, 1);
	    			break;
	    		case '0':
	    			$price = round($price, 0);
	    			break;
	    		case 'C':
	    			$price = ceil($price);
	    			break;
	    		case 'F':
	    			$price = floor($price);
	    			break;
	    	}
	    		
	    	// Min & Max
	    	if($minMax) {
	    		if($priceMax) {
	    			if($price > $priceMax) { $price = $priceMax; }
	    		}
	    		if($priceMin) {
	    			if($price < $priceMin) { $price = $priceMin; }
	    		}
	    	}
	    		
	    	// Freeshipment
	    	if($freeShip) {
	    		if($request->getBaseSubtotalInclTax() >= $freeTotal) {
	    			$price = '0';
	    		}
	    	}
		
		if($request->getFreeShipping()){
	            $price = 0;
	        }
	    	
	    	
	    	$result = $this->_rateResultFactory->create();
	    	$method = $this->_rateMethodFactory->create();
	    	// save carrier information
	    	$method->setCarrier($this->_code);
	    	$method->setCarrierTitle($this->getConfigData('title'));
	    	//print_r($method);die;
	    	
	    	// save method information
	    	$method->setMethod($this->_code);
	    	$method->setMethodTitle($this->getConfigData('method_title') . ' (' . $distance_text . ')');
	    	$method->setCost($price);
	    	$method->setPrice($price);
	    	if(isset($error)) {
	    		$result->append($error);
	    	} else {
	    		$result->append($method);
	    	}
	    	return $result;
	    	}catch(Exception $e){
	    		return $e->getMessage();
	    	}
    		
    	
    }
    
    
    public function getAllowedMethods()
    {
    	return array(
    			$this->_code => $this->getConfigData('name')
    	);
    }
}
