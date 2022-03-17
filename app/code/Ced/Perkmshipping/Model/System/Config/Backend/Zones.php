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

namespace Ced\Perkmshipping\Model\System\Config\Backend;

class Zones extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized {    

 	protected function _beforeSave() {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
            if (count($value)){            	            	
            	
            	$value = $this->orderData($value, 'from');
            	$keys = array();
            	
            	for($i=0; $i < count($value); $i++){
            		$keys[] = 'shipping_' . $i;				
            	}  
				
				foreach($value as $key => $field){
					$from = str_replace(',','.',$field['from']);
					$to = str_replace(',','.',$field['to']);
					$price = str_replace(',','.',$field['price']);														
					$value[$key]['from'] = number_format($from, 2, '.', '');
					$value[$key]['to'] = number_format($to, 2, '.', '');
					$value[$key]['price'] = number_format($price, 2, '.', '');					
				}

				$value = array_combine($keys, array_values($value));  
				          	
            }
        }

        
        $this->setValue($value);
        parent::_beforeSave();
    }

	function orderData($data, $sort) { 
		$code = "return strnatcmp(\$a['$sort'], \$b['$sort']);"; 
		usort($data, create_function('$a,$b', $code)); 
		return $data; 
	} 

  public function afterLoad()
    {
        $value = $this->getValue();
        $arr   = json_decode($value,true);
        $this->setValue($arr);
	return $this;
    }


}
