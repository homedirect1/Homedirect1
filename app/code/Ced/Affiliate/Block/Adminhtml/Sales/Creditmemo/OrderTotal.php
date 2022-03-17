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
namespace Ced\Affiliate\Block\Adminhtml\Sales\Creditmemo;

class OrderTotal extends \Magento\Sales\Block\Adminhtml\Order\Creditmemo\Totals {
	/**
	 * Creditmemo
	 *
	 * @var Creditmemo|null
	 */
	protected $_creditmemo;
	
	/**
	 * Retrieve creditmemo model instance
	 *
	 * @return Creditmemo
	 */
	public function _construct() {
		if ($this->getSource ()->getOrder ()->getCustomdiscount () != 0) {
			$this->getSource ()->getOrder ()->setGrandTotal ( $this->getSource ()->getOrder ()->getGrandTotal () + $this->getSource ()->getOrder ()->getCustomdiscount () );
		}
	}
}
