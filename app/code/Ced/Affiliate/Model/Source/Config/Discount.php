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

namespace Ced\Affiliate\Model\Source\Config;

class Discount implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
    {
    	
    	 return [
			    	['value' => 'cartaffiliate',
			    	 'label' => __('Both Cart Discount and Affiliate Discount')
					],
		    	 	[
			    	 'value' => 'cart',
			    	 'label' => __('Only Cart')
					],
					[
					'value' => 'affiliate',
					'label' => __('Only Affiliate')
					]
    		 ];
    }
}