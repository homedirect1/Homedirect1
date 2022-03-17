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

namespace Ced\Affiliate\Block\Transaction;

class TransactionList extends \Magento\Backend\Block\Widget\Grid\Container
{
	protected $_template = 'Ced_Affiliate::transaction/transactiongrid.phtml';
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'transaction_transactionList';
        $this->_blockGroup = 'Ced_Affiliate';
        $this->_headerText = __('Transaction History');
        
        parent::_construct();
        $this->removeButton('add');
        
    }
    
    protected function _prepareLayout()
    {
    	 
    	$this->setChild(
    			'grid',
    			$this->getLayout()->createBlock('Ced\Affiliate\Block\Transaction\TransactionList\Grid', 'ced.transaction.grid')
    	);
    
    	return parent::_prepareLayout();
    	$this->buttonList->remove('add_new');
    }
    
    
    
}
