<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsAdvTransaction
 * @author     CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Block\Adminhtml\Vorders\Grid\Renderer;

/**
 * Class Orderid
 * @package Ced\CsAdvTransaction\Block\Adminhtml\Vorders\Grid\Renderer
 */
class Orderid extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Return the Order Id Link
     *
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getOrderId() != '') {

            $url = $this->getUrl("csadvtransaction/pay/view", ['order_id' => $row->getOrderId(), 'vendor_id' => $row->getVendorId()]);
            $html = '<a target="_blank" title="Show Calculation Details" href=' . $url . '>' . $row->getOrderId() . '</a>';
            return $html;
        } else
            return '';
    }
}
