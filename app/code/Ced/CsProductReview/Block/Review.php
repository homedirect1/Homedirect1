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
 * @package     Ced_CsProductReview
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsProductReview\Block;

class Review extends \Magento\Backend\Block\Widget\Grid\Container
{
	protected $_template = 'Ced_CsProductReview::review/grid.phtml';
	
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'review';
        $this->_blockGroup = 'Ced_CsProductReview';
        $this->_headerText = __('Manage Review');
        
        parent::_construct();
        $this->removeButton('add');
    }
    
    
    protected function _prepareLayout()
    {
    	
    	$this->setChild(
    			'grid',
    			$this->getLayout()->createBlock('Ced\CsProductReview\Block\Review\Grid', 'ced.csproductreview.grid')
    	);
    
    	return parent::_prepareLayout();
    	$this->buttonList->remove('add_new');
    }
}
