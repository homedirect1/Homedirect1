<?php

namespace Knowband\Mobileappbuilder\Block\Adminhtml\Renderers;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class PaymentMethodAction extends AbstractRenderer {


    public function render(DataObject $row) {
        $title_edit = __('Edit');
        $html = '<a class="btn btn-success" href="javascript:void(0)" onclick="editPaymentMethods(\'' . $this->getUrl('*/*/paymentAjax/id/' . $row['kb_payment_id'] . '/') . '\');" title="'.$title_edit.'">'
               . $title_edit . ' </a>';
        return $html;
        
    }

}
