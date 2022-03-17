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

namespace Ced\Affiliate\Block\Withdrawl\Renderer;
 
class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {
 
	/**
	 * Render approval link in each vendor row
	 * @param Varien_Object $row
	 * @return String
	 */
	public function render(\Magento\Framework\DataObject $row) {
		
		$html ='';
		if($row->getStatus()==\Ced\Affiliate\Model\AffiliateWithdrawl::PENDING)
			$html ="Pending";
		elseif($row->getStatus()==\Ced\Affiliate\Model\AffiliateWithdrawl::COMPLETE)
		$html ="Complete";
			else 
				$html ="Cancelled";
		return $html;
	}
}