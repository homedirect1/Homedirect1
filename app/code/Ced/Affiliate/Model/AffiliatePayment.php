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
namespace Ced\Affiliate\Model;
class AffiliatePayment extends \Magento\Payment\Model\Method\AbstractMethod
{
	const METHOD_CODE = 'storecredit';
	protected $_code = 'storecredit';
	
	protected $_isOffline = true;
	
	
	public function assignData(\Magento\Framework\DataObject $data)
	{
		$this->getInfoInstance()->setPoNumber($data->getPoNumber());
		return $this;
	}
}