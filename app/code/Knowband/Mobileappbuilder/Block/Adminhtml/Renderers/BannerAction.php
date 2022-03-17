<?php

namespace Knowband\Mobileappbuilder\Block\Adminhtml\Renderers;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class BannerAction extends AbstractRenderer {


    public function render(DataObject $row) {
        $title_edit = __('Edit');
        $html = '<a class="btn btn-success" href="javascript:void(0)" onclick="editBannerSlider(\'' . $this->getUrl('*/*/bannerAjax/id/' . $row['kb_banner_id'] . '/') . '\');" title="'.$title_edit.'">'
                . $title_edit . '</a>';
        return $html;
        
    }

}
