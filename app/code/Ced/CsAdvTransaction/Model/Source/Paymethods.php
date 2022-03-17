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
 * @category    Ced
 * @package     Ced_CsAdvTransaction
 * @author   	 CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsAdvTransaction\Model\Source;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Payment\Model\Config;

class Paymethods implements \Magento\Framework\Option\ArrayInterface
{
	protected $_appConfigScopeConfigInterface;
	
	protected $_paymentModelConfig;
	
	protected $_scope;
	
	public function __construct(
	ScopeConfigInterface $appConfigScopeConfigInterface,
	Config $paymentModelConfig,
    \Magento\Framework\App\Config\ScopeConfigInterface $scope)
	{
		$this->_scope = $scope;
		$this->_appConfigScopeConfigInterface = $appConfigScopeConfigInterface;
		$this->_paymentModelConfig = $paymentModelConfig;
	}
	
	public function toOptionArray()
	{
		
		$methods = array();
		$payments  = $this->_scope->getValue('payment');
		
		 foreach ($payments as $paymentCode=>$paymentModel)
		{
			$paymentTitle = $this->_appConfigScopeConfigInterface->getValue('payment/'.$paymentCode.'/title');
			$methods[$paymentCode] = array(
			'label' => $paymentTitle,
			'value' => $paymentCode
			);
		} 
		return $methods;
	}
}
