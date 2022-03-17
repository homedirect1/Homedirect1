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
namespace Ced\Affiliate\Model\Total\Invoice;

class Discount extends \Magento\Sales\Model\Order\Total\AbstractTotal {
	protected $_storeManager;
	public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager, array $data = []) {
		parent::__construct ( $data );
		$this->_storeManager = $storeManager;
	}
	public function collect(\Magento\Sales\Model\Order\Invoice $invoice) {
		$order = $invoice->getOrder ();
		$baseDiscount = $order->getCustomdiscount();
		$discount = $order->getCustomdiscount();
		
			if (floatval ( $baseDiscount )) {
				$baseDiscount = $invoice->roundPrice ( $baseDiscount );
				$discount = $invoice->roundPrice ( $discount );
				
				$invoice->setDiscountAmount ( $baseDiscount );
				$invoice->setBaseDiscountAmount ( $baseDiscount );
				$invoice->setDiscountDescription ( "Affiliate Discount" );
				$invoice->setBaseGrandTotal ( $invoice->getBaseGrandTotal () + $baseDiscount );
				$invoice->setGrandTotal ( $invoice->getGrandTotal () + $discount );
			}
		
		return $this;
	}
}