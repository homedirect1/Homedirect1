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
namespace Ced\Affiliate\Block\Adminhtml\Sales;

class OrderTotal extends \Magento\Sales\Block\Order\Totals
{
    protected $_order = '';
    
    /**
     * Init totals
     */
    public function initTotals(){
    	
        $amount = floatval($this->getOrder()->getBaseDiscountAmount());
        if($this->getOrder()->getDiscountDescription()=="Affiliate Discount"){
	        if($amount){
	            $total = new \Magento\Framework\DataObject(
	                [
	                    'code' => 'customdiscount',
	                    'field' => 'customdiscount',
	                    'value' => $amount,
	                    'base_value'=> $this->getOrder()->getBaseDiscountAmount(),
	                    'label' => __('Affiliate Discount')
	                ]
	            );
	            $parent = $this->getParentBlock();
	            $parent->addTotal($total,'subtotal');
	        }
        }
    }

   
    /**
     * get Order
     *
     * @return mixed
     */
    public function getOrder(){
        if(!$this->_order){
            $parent = $this->getParentBlock();
            if ($parent instanceof \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals) {
                $order = $parent->getInvoice();
            } elseif ($parent instanceof Magento\Sales\Block\Adminhtml\Order\Creditmemo\Totals) {
                $order = $parent->getCreditmemo();
            } else {
                $order = $this->getParentBlock()->getOrder();
            }
            $this->_order = $order;
        }
        return $this->_order;
    }
}
