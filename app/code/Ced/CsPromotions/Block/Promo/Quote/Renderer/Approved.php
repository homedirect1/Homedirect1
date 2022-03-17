<?php 

namespace Ced\CsPromotions\Block\Promo\Quote\Renderer;
 
class Approved extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {
 
	/**
	 * Render approval link in each vendor row
	 * @param Varien_Object $row
	 * @return String
	 */
	public function render(\Magento\Framework\DataObject $row) {
		
		//print_r($row->getData()); die("cckjb");
		$html = '';

		if($row->getRuleId()!='' && $row->getIsApprove() != 1) {	
			$url =  $this->getUrl('*/*/massStatus', array('rule_id' => $row->getRuleId() , 'status'=>'approved', 'inline'=>1));
			$html .= '<a href="javascript:void(0);" onclick="deleteConfirm(\''.__('Are you sure you want to Approve?').'\', \''. $url . '\');" >'.__('Pending').'</a>';  
		} 
			
		if($row->getRuleId()!='' && $row->getIsApprove() != 0) {
			if(strlen($html) > 0) $html .= ' | ';
			$url =  $this->getUrl('*/*/massStatus', array('vendor_id' => $row->getVendorId(), 'status'=>'disapproved', 'inline'=>1));
			$html .= '<a href="javascript:void(0);" onclick="deleteConfirm(\''.__('Are you sure you want to Disapprove?').'\', \''. $url . '\');" >'.__('Approved')."</a>";
		}
		
		return $html;
	}
}