<?php

namespace Ced\CsReport\Block\Adminhtml\Returns;

/**
 * Class Grid
 * @package Ced\CsReport\Block\Adminhtml\Returns
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Ced\CsRma\Model\ResourceModel\Request\CollectionFactory
     */
    protected $requestCollectionFactory;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Ced\CsRma\Model\ResourceModel\Request\CollectionFactory $requestCollectionFactory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\App\Request\Http $request,
        \Ced\CsReport\Model\Rma $rmaFactory

    )
    {
        parent::__construct($context, $backendHelper);
        $this->_request = $request;
        $this->rmaFactory = $rmaFactory;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setFilterVisibility(false);
        $this->setId('vreturnsreport.grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
    }

    /**
     * @return $this|\Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $vendorId = $this->_request->getParam('vendor_id');
        $from = $this->_request->getParam('from');
        $to = $this->_request->getParam('to');
        /*$requestCollection = $this->requestCollectionFactory->create()
            ->addFieldToFilter('vendor_id', $vendorId);*/
         $requestCollection = $this->rmaFactory->create()
             ->addFieldToFilter('vendor_id', $vendorId);

        if (isset($to) && isset($from)) {
            $from .= ' 00:00:00';
            $to .= ' 23:59:59';
            $requestCollection->addFieldToFilter('main_table.created_at', array('from' => $from, 'to' => $to));
        }
        $this->setCollection($requestCollection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {

        $this->addColumn('order_id', array(
            'header' => __('Order ID'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'order_id',
        ));


        $this->addColumn('created_at', array(
            'header' => __('Order Date'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'created_at',
        ));

        $this->addColumn('status', array(
            'header' => __('Delivery Status'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'status',
        ));

        return parent::_prepareColumns();
    }
}
