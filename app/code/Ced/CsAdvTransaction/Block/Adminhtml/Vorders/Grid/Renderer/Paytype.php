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
 * Class Paytype
 * @package Ced\CsAdvTransaction\Block\Adminhtml\Vorders\Grid\Renderer
 */
class Paytype extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Ced\CsAdvTransaction\Helper\Data
     */
    protected $advHelper;

    /**
     * Paytype constructor.
     * @param \Ced\CsAdvTransaction\Helper\Data $advHelper
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Ced\CsAdvTransaction\Helper\Data $advHelper,
        \Magento\Backend\Block\Context $context,
        array $data = []
    )
    {
        $this->advHelper = $advHelper;
        parent::__construct($context, $data);
    }

    /**
     * Return the Order Id Link
     *
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getOrderId() != '') {
            $type = $this->advHelper->getOrderPaymentType($row->getOrderId());

            if ($type == "PrePaid") {
                $html = '<a target="_blank" title="Order Amount is Paid to Admin" href="javascript:void(0)">' . $type . '</a>';
            } else {
                $html = '<a target="_blank" title="Order Amount is Paid to Vendor" href="javascript:void(0)">' . $type . '</a>';
            }
            return $html;
        } else
            return '';
    }
}
