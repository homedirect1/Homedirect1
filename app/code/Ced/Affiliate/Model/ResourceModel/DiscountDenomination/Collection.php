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
namespace Ced\Affiliate\Model\ResourceModel\DiscountDenomination;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
	 
class Collection extends AbstractCollection{
    
    protected $_idFieldName = 'id';
   
	protected function _construct()
	{
	    $this->_init(
	            'Ced\Affiliate\Model\DiscountDenomination',
	            'Ced\Affiliate\Model\ResourceModel\DiscountDenomination'
	    );
	}
}