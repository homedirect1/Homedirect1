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
namespace Ced\CsAdvTransaction\Model;

class Fee extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
	const STATE_OPEN       = 1;
	const STATE_PAID       = 2;
	const STATE_CANCELED   = 3;
	const STATE_REFUND     = 4;
	const STATE_REFUNDED   = 5;
	
    protected function _construct()
    {
        $this->_init('Ced\CsAdvTransaction\Model\ResourceModel\Fee');
    }
}