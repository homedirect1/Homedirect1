<?php

namespace Knowband\Mobileappbuilder\Block\Adminhtml\Renderers;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class BannerTitle extends AbstractRenderer {


    public function render(DataObject $row) {
        $html = '';
        $data = $row->getData();


        if ($data['type'] == 'slider') {
            $html .= __('Slider');
        } else {
            $html .= __('Banner');
        }


        $html .= '#';
        $html .= $data['kb_banner_id'];
        return $html;
    }

}
