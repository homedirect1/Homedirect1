<?php

namespace Knowband\Mobileappbuilder\Block\Adminhtml\Renderers;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class LayoutAction extends AbstractRenderer {
    public function render(DataObject $row) {
        $title_edit = __('Edit');
        $edit_url = $this->getUrl('*/*/editLayout/id/' . $row['id_layout'] . '/');
        $html = '<a class="btn btn-success layout-action-btn" href="'.$edit_url.'" title="'.$title_edit.'">'. $title_edit . '</a>';
        
        $edit_name_url = $this->getUrl('*/*/layoutAjaxAction/', ['getAddLayoutForm' => true, 'layout_id' => $row['id_layout']]);
        $html .= '<a class="btn btn-warning layout-action-btn" href="javascript:void(0)" onclick="openAddLayoutForm('."'". $edit_name_url. "')" .'" '.'title="'.__("Edit Name").'">'. __("Edit Name") . '</a>';
        
        $html .= '<a class="btn btn-danger layout-action-btn" href="javascript:void(0)" onclick="openDeleteLayoutForm('."'". $row['id_layout']. "')" .'" '.'title="'.__("Delete").'">'. __("Delete") . '</a>';
        return $html;
        
    }

}
