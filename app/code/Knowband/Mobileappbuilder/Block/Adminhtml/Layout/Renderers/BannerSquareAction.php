<?php

namespace Knowband\Mobileappbuilder\Block\Adminhtml\Layout\Renderers;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class BannerSquareAction extends AbstractRenderer {
    
    public function render(DataObject $row) {
        $title_edit = __("Edit");
        $title_delete = __("Delete");
        $banner_id = (int) $row->getId();
        $html = '<button type="button" class="btn btn-success" style="margin: 2px;" onclick="edit_banner_slider('."'".$banner_id."'".');" title="'.$title_edit.'">'. $title_edit . '</button>';
        $html .= '<button type="button" class="btn btn-danger" onclick="delete_banner_slider('."'".$banner_id."'".');" title="'.$title_delete.'">'. $title_delete . '</button>';
        return $html;
    }

}
