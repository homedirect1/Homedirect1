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
namespace Ced\Affiliate\Block\Adminhtml\Account\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('account_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Affiliate Account Information'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
    	
       $this->addTab(
            'account_tab',
            [
                'label' => __('Account Details'),
                'title' => __('Account Details'),
                'content' => $this->getLayout()->createBlock('Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab\Main')->toHtml(),
                'active' => true
            ]
        );
      if($this->getRequest()->getParam('id')):
	       $this->addTab(
	       		'order_tab',
	       		[
	       		'label' => __('Affiliate Orders'),
	       		'title' => __('Affiliate Orders'),
	       		'content' => $this->getLayout()
					->createBlock('Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab\Balance')->setTemplate('Ced_Affiliate::affiliate/amount.phtml')->toHtml().$this->getLayout()->createBlock('Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab\Orders')->toHtml(),
	       		'active' => false
	       		]
	       );
	       
	       $this->addTab(
	       		'withdrawl_tab',
	       		[
	       		'label' => __('Withdrawal Request History'),
	       		'title' => __('Withdrawal Request History'),
	       		'content' => $this->getLayout()->createBlock('Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab\Withdrawl')->toHtml(),
	       		'active' => false
	       		]
	       );
	        
	       
	       $this->addTab(
	       		'transaction_tab',
	       		[
	       		'label' => __('Affiliate Transaction'),
	       		'title' => __('Affiliate Transaction'),
	       		'content' => $this->getLayout()->createBlock('Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab\Transactions')->toHtml(),
	       		'active' => false
	       		]
	       );
       endif;
       $this->addTab(
       		'payment_methods_tab',
       		[
       		'label' => __('Payment Methods'),
       		'title' => __('Payment Methods'),
       		'content' => $this->getLayout()->createBlock('Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab\PaymentMethods')->toHtml(),
       		'active' => false
       		]
       );
        return parent::_beforeToHtml();
    }
}
