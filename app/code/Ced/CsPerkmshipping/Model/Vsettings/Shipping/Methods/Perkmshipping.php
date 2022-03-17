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
  * @package     Ced_CsPerkmshipping
  * @author   CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

namespace Ced\CsPerkmshipping\Model\Vsettings\Shipping\Methods;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Ced\DomesticAustralianShipping\Helper\Config;

class Perkmshipping extends \Ced\CsMultiShipping\Model\Vsettings\Shipping\Methods\AbstractModel
{
    protected $_code = 'perkmshipping';
	protected $_fields = array();
	protected $_codeSeparator = '-';
	 protected $_scopeConfig;
	 protected $_countryFactory;
	 protected $_objectManager;
	/**
	 * Retreive input fields
	 *
	 * @return array
	 */
	public function __construct(
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			\Psr\Log\LoggerInterface $logger,
			\Magento\Directory\Model\Config\Source\CountryFactory $countryFactory,
			\Magento\Framework\ObjectManagerInterface $objectManager,
			array $data = []
	) {
		$this->_countryFactory = $countryFactory;
		$this->_scopeConfig = $scopeConfig;
		$this->_objectManager = $objectManager;
	
	}
	
	   public function getFields() {
		$fields['active'] = array('type'=>'select',
								'required'=>true,
								'values'=>array(
									array('label'=>__('Yes'),'value'=>1),
									array('label'=>__('No'),'value'=>0)
								)
							);
		
		
	   $fields['title'] = array('type'=>'text');
		
		$fields['methodtitle'] = array('type'=>'text');
		
		$address_calc = $this->_objectManager->create('Ced\Perkmshipping\Model\Source\Method\Address')->toOptionArray();
		$fields['address_calc'] = array('type'=>'select',
										'values'=>$address_calc
		);
		
		$distance_metric = $this->_objectManager->create('Ced\Perkmshipping\Model\Source\Method\Metric')->toOptionArray();
		$fields['distance_metric'] = array('type'=>'select',
										'values'=>$distance_metric
		);
		
		$distance_round = $this->_objectManager->create('Ced\Perkmshipping\Model\Source\Method\Round')->toOptionArray();
		$fields['distance_round'] = array('type'=>'select',
										'values'=>$distance_round
		);
		
		$price_round = $this->_objectManager->create('Ced\Perkmshipping\Model\Source\Method\Round')->toOptionArray();
		$fields['price_round'] = array('type'=>'select',
										'values'=>$price_round
		);
	
		//$block_obj =  new Ced_Perkmshipping_Block_Zones();
		$block_obj = $this->_objectManager->create('Ced\Perkmshipping\Block\Zones');
		$fields['shipping_zones'] = array('type'=>'text',
										  'class'=>'hide',
										  'after_element_html'=>$block_obj->toHtml()
		); 
		
		$fields['minmax'] = array('type'=>'select',
				'values'=>array(
						array('label'=>__('Yes'),'value'=>1),
						array('label'=>__('No'),'value'=>0)
				)
		);
		
		$fields['price_min'] = array('type'=>'text');
		
		$fields['price_max'] = array('type'=>'text');
		
		$fields['distance_max'] = array('type'=>'text');
		
		$fields['freeshipping'] = array('type'=>'select',
				'values'=>array(
						array('label'=>__('Yes'),'value'=>1),
						array('label'=>__('No'),'value'=>0)
				)
		);
		
		$fields['free_shipping_subtotal'] = array('type'=>'text');
		
		$fields['minimum'] = array('type'=>'select',
				'values'=>array(
						array('label'=>__('Yes'),'value'=>1),
						array('label'=>__('No'),'value'=>0)
				)
		);
		
		$fields['minimum_subtotal'] = array('type'=>'text');
		
		 $alloptions = $this->_objectManager->create('Magento\Directory\Model\Config\Source\Country')->toOptionArray();		
		 if($this->_scopeConfig->getValue('carriers/perkmshipping/sallowspecific',\Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
			$availableCountries = explode(',',$this->_scopeConfig->getValue('carriers/perkmshipping/specificcountry',\Magento\Store\Model\ScopeInterface::SCOPE_STORE));
			foreach($alloptions as $key => $value){
				if(in_array($value['value'], $availableCountries)){
					$allcountry[] = $value;
				}
			}
		}else{
			$allcountry = $alloptions;
		}	 		
		$fields['allowed_country'] = array('type'=>'multiselect',
									'values'=>$allcountry
									);	

		
		return $fields;
	}
	
	/**
	 * Retreive labels
	 *
	 * @param string $key
	 * @return string
	 */
	public function getLabel($key) {
		switch($key) {
			case 'active': return __('Active');break;
			case 'label' : return __('Distance Based Shipping(Per km)');break;
			case 'title' : return __('Title');break;
			case 'methodtitle' : return __('Method Title');break;
			case 'address_calc' : return __('Address Calculation');break;
			case 'distance_metric' : return __('Metric');break;
			case 'distance_round' : return __('Distance(Round Off)');break;
			case 'price_round' : return __('Total Price(Round Off)');break;
			case 'shipping_zones' : return __('Shipping Zones');break;
			case 'minmax' : return __('Use Minimum and Maximum Price');break;
			case 'price_min' : return __('Minimal Price');break;
			case 'price_max' : return __('Maximum Price');break;
			case 'distance_max' : return __('Maximum Distance (km/miles)');break;
			case 'freeshipping' : return __('Enable Free Shipping');break;
			case 'free_shipping_subtotal' : return __('Minimum Order Amount For Free Shipping');break;
			case 'minimum' : return __('Enable Minimum Order Amount');break;
			case 'minimum_subtotal' : return __('Minimum Order Amount');break;
			case 'allowed_country': return __('Allowed Country');break;
			default : return parent::getLabel($key); break;
		}
	}
	
}
		