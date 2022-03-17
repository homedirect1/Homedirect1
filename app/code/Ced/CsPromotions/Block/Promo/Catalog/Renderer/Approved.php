<?php 

namespace Ced\CsPromotions\Block\Promo\Catalog\Renderer;
 
class Approved extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {
 
	/**
	 * Render approval link in each vendor row
	 * @param Varien_Object $row
	 * @return String
	 */
	public function render(\Magento\Framework\DataObject $row) {
		
		$html = '';
		if($row->getRuleId()!='' && $row->getIsApprove() != 1) {	
			$html .= __('Disapproved');
		} 
			
		if($row->getRuleId()!='' && $row->getIsApprove() != 0) {
			$html .= __('Approved');
		}
		return $html;
	}
}
