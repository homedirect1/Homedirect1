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
namespace Ced\CsAdvTransaction\Block\Adminhtml;
class Request extends \Magento\Backend\Block\Widget\Grid\Container
	{
	
		
		protected function _construct()
		{
			$this->_controller = 'adminhtml_request';
			$this->_blockGroup = 'Ced_CsAdvTransaction';
			$this->_headerText = __('Eligible Orders');
		    $this->_addButtonLabel = __('Continue'); 
		    parent::_construct();
			$this->buttonList->remove('add');
			
		}
		
		protected function _isAllowedAction($resourceId)
		{
			return $this->_authorization->isAllowed($resourceId);
		}
	
		
}
