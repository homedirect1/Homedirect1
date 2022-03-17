<?php 

namespace Ced\CsPromotions\Block\Adminhtml\Promo\Renderer;
 
class Approve extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {
 
	/**
	 * Render approval link in each vendor row
	 * @param Varien_Object $row
	 * @return String
	 */
	public function render(\Magento\Framework\DataObject $row) {
		
		$html = '';
		if($row->getVendorId()){
			if($row->getRuleId()!='' && $row->getIsApprove() != 1) {	
				$url =  $this->getUrl('cspromotion/*/massStatus', array('rule_id' => $row->getRuleId() , 'status'=>'approved'));
				
				$html .= '<a href="javascript:void(0);" onclick="deleteConfirm(\''.__('Are you sure you want to Approve?').'\', \''. $url . '\');" >'.__('Approve').'</a>';  
			} 
				
			if($row->getRuleId()!='' && $row->getIsApprove() != 0) {
				if(strlen($html) > 0) $html .= ' | ';
				$url =  $this->getUrl('cspromotion/*/massStatus', array('rule_id' => $row->getRuleId(), 'status'=>'disapproved'));
				$html .= '<a href="javascript:void(0);" onclick="deleteConfirm(\''.__('Are you sure you want to Disapprove?').'\', \''. $url . '\');" >'.__('Disapprove')."</a>";
			}
		}else{
			$html .= __('Not required');
		}

		
		return $html;
	}
}