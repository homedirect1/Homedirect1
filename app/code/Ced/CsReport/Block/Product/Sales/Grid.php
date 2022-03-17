<?php

namespace Ced\CsReport\Block\Product\Sales;

/**
 * Class Grid
 * @package Ced\CsReport\Block\Product\Sales
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    protected $_vendorId;

    /**
     * @var \Ced\CsMarketplace\Model\VordersFactory
     */
    protected $_vorderFactory;

    /**
     * @var \Ced\CsOrder\Model\InvoiceFactory
     */
    protected $invoiceFactory;

    /**
     * Grid constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Ced\CsMarketplace\Model\VordersFactory $vordersFactory
     * @param \Ced\CsOrder\Model\InvoiceFactory $invoiceFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Ced\CsMarketplace\Model\VordersFactory $vordersFactory,
        \Ced\CsOrder\Model\InvoiceFactory $invoiceFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    )
    {
        parent::__construct($context, $backendHelper, $data);
        $this->_vendorId = $customerSession->getVendorId();
        $this->_vorderFactory = $vordersFactory;
        $this->invoiceFactory = $invoiceFactory;
        $this->setData('area', 'adminhtml');
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('salesgrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $vendorId = $this->_vendorId;
        if (!$vendorId) {
            $vendorId = $this->getRequest()->getParam('vendor_id');
        }
        $vorders = $this->_vorderFactory->create()
            ->getCollection()
            ->addFieldtoSelect('*');
        $vorders->addFieldToFilter('vendor_id', $vendorId);

        $joinCondition = 'main_table.order_id = shipment_table.order_id';

        $vorders->getSelect()
            ->joinLeft(
                array('shipment_table' => $vorders->getResource()->getTable('sales_shipment_track')),
                $joinCondition,
                array('title', 'track_number')
            );

        $from = $this->_request->getParam('from');
        $to = $this->_request->getParam('to');

        if (isset($from) && $from != '' && isset($to) && $to != '') {
            $from .= ' 00:00:00';
            $to .= ' 23:59:59';
            $vorders->addFieldToFilter('main_table.created_at', array('from' => $from, 'to' => $to));
        }
        $this->setCollection($vorders);
        return parent::_prepareCollection();

    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Exception
     */
    public function _prepareColumns()
    {

        $this->addColumn('order_id', array(
            'header' => 'Order ID',
            'width' => '80px',
            'type' => 'text',
            'index' => 'order_id',
            'filter' => false,
        ));
        $this->addColumn('created_at', array(
            'header' => 'Order Date',
            'width' => '80px',
            'type' => 'date',
            'index' => 'created_at',
            'filter_index' => 'main_table.created_at'
        ));

        $this->addColumn('product_qty', array(
            'header' => 'Products',
            'width' => '80px',
            'type' => 'text',
            'index' => 'product_qty',

        ));

        $this->addColumn('base_order_total', array(
            'header' => 'Selling Price',
            'width' => '80px',
            'type' => 'text',
            'index' => 'base_order_total',
            'type' => 'currency'

        ));

        $this->addColumn(
            'order_payment_state',
            [
                'header' => __('Order Payment State'),
                'index' => 'order_payment_state',
                'type' => 'options',
                'options' => $this->invoiceFactory->create()->getStates(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );

        $this->addColumn('title', array(
            'header' => 'Courier Name',
            'width' => '80px',
            'type' => 'text',
            'index' => 'title',
        ));

        $this->addColumn('track_number', array(
            'header' => 'Tracking Number',
            'width' => '80px',
            'type' => 'text',
            'index' => 'track_number',
        ));

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/salesGrid', array('_current' => true));
    }

}
