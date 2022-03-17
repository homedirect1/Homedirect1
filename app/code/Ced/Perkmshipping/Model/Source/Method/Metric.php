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
 
namespace Ced\Perkmshipping\Model\Source\Method;
  
class Metric {

	public function toOptionArray() {
		$metric = array();
		$metric[] = array('value'=>'metric', 'label'=> __('Kilometer'));
		$metric[] = array('value'=>'imperial', 'label'=> __('Miles'));
		return $metric;
	}

}