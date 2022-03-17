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
class Status
{
	const STATUS_PENDING = 'pending' ;
	const STATUS_PROCESSING = 'processing' ;
	const STATUS_CREDITLIMIT = 'ced_wallet_limit';
	
	public function toOptionArray()
	{
		return [
				[
				'value'=>self::STATUS_CREDITLIMIT,
				'label'=>_('Order Placed By Store Credit')
				],
		         
				[
					'value'=>self::STATUS_PENDING,
					'label'=>_('Pending')
				],
				[
				'value'=>self::STATUS_PROCESSING,
				'label'=>_('Processing')
				],
				
		];			
	}
}