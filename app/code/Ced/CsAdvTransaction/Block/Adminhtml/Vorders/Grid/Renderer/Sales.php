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
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Block\Adminhtml\Vorders\Grid\Renderer;

/**
 * Class Sales
 * @package Ced\CsAdvTransaction\Block\Adminhtml\Vorders\Grid\Renderer
 */
class Sales extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * Sales constructor.
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Backend\Block\Context $context,
        array $data = []
    )
    {
        $this->orderFactory = $orderFactory;
        parent::__construct($context, $data);
    }

    /**
     * Return the Order Id Link
     *
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getOrderId() != '') {

            $order = $this->orderFactory->create()->loadByIncrementId($row->getOrderId());

            $orderId = $order->getId();
            $url = $this->getUrl("sales/order/view/", array('order_id' => $orderId));
            $html = '<a target="_blank" href=' . $url . '>' . $row->getOrderId() . '</a>';
            return $html;
        } else
            return '';
    }
}
