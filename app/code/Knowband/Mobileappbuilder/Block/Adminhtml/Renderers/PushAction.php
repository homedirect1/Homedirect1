<?php

namespace Knowband\Mobileappbuilder\Block\Adminhtml\Renderers;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class PushAction extends AbstractRenderer {


    public function render(DataObject $row) {
        $title_edit = __('View Details');
        $url = $this->getUrl('*/*/saveAndSendPushNotification/id/' . $row['kb_notification_id'] . '/');
        $html = "";
        $html .= '<a class="btn btn-success" href="javascript:void(0)" onclick="viewNotificationDetials(\'' . $url . '\');" title="'.$title_edit.'">' . $title_edit . '</a>';

        return $html;
        
    }

}
