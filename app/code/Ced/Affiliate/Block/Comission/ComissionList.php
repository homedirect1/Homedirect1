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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Block\Comission;

class ComissionList extends \Magento\Backend\Block\Widget\Grid\Container
{
	protected $_template = 'Ced_Affiliate::comission/comissiongrid.phtml';
	
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'comission_comissionList';
        $this->_blockGroup = 'Ced_Affiliate';
        $this->_headerText = __('Quotation List');
        
        parent::_construct();
        $this->removeButton('add');
       // $this->setData('area','adminhtml');
    }
    
    
    protected function _prepareLayout()
    {
    	
    	$this->setChild(
    			'grid',
    			$this->getLayout()->createBlock('Ced\Affiliate\Block\Comission\ComissionList\Grid', 'ced.comission.grid')
    	);
    
    	return parent::_prepareLayout();
    	$this->buttonList->remove('add_new');
    }
    
    
    
   
}
