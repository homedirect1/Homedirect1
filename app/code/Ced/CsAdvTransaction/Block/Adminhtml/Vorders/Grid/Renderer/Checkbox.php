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
 * Class Checkbox
 * @package Ced\CsAdvTransaction\Block\Adminhtml\Vorders\Grid\Renderer
 */
class Checkbox extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Ced\CsAdvTransaction\Helper\Data
     */
    protected $advHelper;

    /**
     * Checkbox constructor.
     * @param \Ced\CsAdvTransaction\Helper\Data $advHelper
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct
    (
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
            $id = $row->getId();
            $type = $this->advHelper->getOrderPaymentType($row->getOrderId());

            if ($type == __('PostPaid')) {
                $pay = "<input style='display:none' name='in_orders' value=$id class='csmarketplace_relation_id checkbox' type='checkbox' checked>";
            } else {
                $pay = "<input name='in_orders' value=$id class='csmarketplace_relation_id checkbox' type='checkbox'>";
            }
            return $pay;
        } else {
            return '';
        }

    }
}
