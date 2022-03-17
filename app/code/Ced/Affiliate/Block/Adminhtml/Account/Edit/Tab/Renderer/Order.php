<?php

namespace Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab\Renderer;



use Magento\Backend\Block\Context;

class Order  extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    protected $orderFactory;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        Context $context,
        array $data = []
    )
    {
        $this->orderFactory = $orderFactory;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\DataObject $row) {
        $order = $this->orderFactory->create()->loadByIncrementId($row->getIncrementId());
        return '<a title="' . $row->getIncrementId() . '" href="' .$this->getUrl('sales/order/view/order_id/'. (($order && $order->getId())? $order->getId() : '#')). '">' . $row->getIncrementId() . '</a>';
    }
}