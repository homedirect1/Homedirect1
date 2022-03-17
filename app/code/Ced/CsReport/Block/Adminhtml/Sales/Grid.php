<?php

namespace Ced\CsReport\Block\Adminhtml\Sales;

/**
 * Class Grid
 * @package Ced\CsReport\Block\Adminhtml\Sales
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vorders\CollectionFactory
     */
    protected $vordersCollectionFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vorders\CollectionFactory $vordersCollectionFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Ced\CsMarketplace\Model\ResourceModel\Vorders\CollectionFactory $vordersCollectionFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\ResourceConnection $resourceConnection

    )
    {
        parent::__construct($context, $backendHelper);
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->vordersCollectionFactory = $vordersCollectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->_request = $request;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setFilterVisibility(false);
        $this->setId('vsalesreport.grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {

        $orders = $this->orderCollectionFactory
            ->create()
            ->addAttributeToSelect('*');
        $vendor_id = $this->_request->getParam('vendor_id');

        $vorders = $this->vordersCollectionFactory->create()->addFieldtoSelect('order_id');
        $vorders->addFieldToFilter('vendor_id', $vendor_id);

        $vorderIds = array();
        foreach ($vorders as $vorder) {
            $vorderIds[] = $vorder->getOrderId();
        }

        $joinCondition = 'main_table.entity_id = shipment_table.order_id';

        $orders->addAttributeToSelect('increment_id');
        $orders->addAttributeToSelect('entity_id');
        $orders->addAttributeToSelect('created_at');
        $orders->addAttributeToSelect('total_item_count');
        $orders->addAttributeToSelect('grand_total');
        $orders->addAttributeToSelect('status');
        $orders->addAttributeToFilter('increment_id', array('in' => $vorderIds));

        $resource = $this->resourceConnection;
        $trackTable = $resource->getTableName('sales_shipment_track');

        $orders->getSelect()
            ->joinLeft(
                array('shipment_table' => $trackTable),
                $joinCondition,
                array('title', 'track_number')
            );

        $from = $this->_request->getParam('from');
        $to = $this->_request->getParam('to');

        if (isset($from) && isset($to)) {
            $from .= ' 00:00:00';
            $to .= ' 23:59:59';
            $orders->addAttributeToFilter('main_table.created_at', array('from' => $from, 'to' => $to));
        }

        $this->setCollection($orders);
        return parent::_prepareCollection();

    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Exception
     */
    public function _prepareColumns()
    {

        $this->addColumn('increment_id', array(
            'header' => 'Order ID',
            'width' => '80px',
            'type' => 'text',
            'index' => 'increment_id',
            'sortable' => false,
            'filter' => false,
        ));
        $this->addColumn('created_at', array(
            'header' => 'Order Date',
            'width' => '80px',
            'type' => 'date',
            'sortable' => false,
            'index' => 'created_at',
            'filter' => false,
        ));

        $this->addColumn('total_item_count', array(
            'header' => 'Products',
            'width' => '80px',
            'type' => 'text',
            'index' => 'total_item_count',
            'sortable' => false,
            'filter' => false,
        ));

        $this->addColumn('grand_total', array(
            'header' => 'Selling Price',
            'width' => '80px',
            'type' => 'text',
            'index' => 'grand_total',
            'sortable' => false,
            'filter' => false,
        ));

        $this->addColumn('status', array(
            'header' => 'Delivery Status',
            'width' => '80px',
            'type' => 'text',
            'index' => 'status',
            'sortable' => false,
            'filter' => false,
        ));

        $this->addColumn('title', array(
            'header' => 'Courier Name',
            'width' => '80px',
            'type' => 'text',
            'index' => 'title',
            'sortable' => false,
            'filter' => false,
        ));

        $this->addColumn('track_number', array(
            'header' => 'Tracking Number',
            'width' => '80px',
            'type' => 'text',
            'index' => 'track_number',
            'sortable' => false,
            'filter' => false,
        ));

        return parent::_prepareColumns();
    }
}
