<?php

namespace Knowband\Mobileappbuilder\Block\Adminhtml\Layout\Renderers;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class BannerSquareImage extends AbstractRenderer {
    
    public function render(DataObject $row) {
        $image_url = $row->getImageUrl();
        $html = '<img src="'.$image_url.'" style="max-width: 55px;max-height: 45px;">';
        return $html;
        
    }

}
