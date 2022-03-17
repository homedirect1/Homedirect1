<?php

namespace Ced\CsReport\Block\Adminhtml\Payment\Renderer;

/**
 * Class Status
 * @package Ced\CsReport\Block\Adminhtml\Payment\Renderer
 */
class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $status = $row->getStatus();
        switch ($status) {
            case 1:
                return 'OPEN';
            case 2:
                return 'PAID';
            case 3:
                return 'CANCELED';
            case 4:
                return 'REFUND';
            case 5:
                return 'REFUNDED';
            default:
                return '-';
        }
    }
}