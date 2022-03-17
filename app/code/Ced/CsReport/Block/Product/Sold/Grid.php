<?php

namespace Ced\CsReport\Block\Product\Sold;

/**
 * Class Grid
 * @package Ced\CsReport\Block\Product\Sold
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var mixed
     */
    protected $_vendorId;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * @var \Ced\CsReport\Model\ResourceModel\Product\Sold\CollectionFactory
     */
    protected $soldCollectionFactory;

    /**
     * Grid constructor.
     * @param \Ced\CsMarketplace\Model\Session $customerSession
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollectionFactory
     * @param \Ced\CsReport\Model\ResourceModel\Product\Sold\CollectionFactory $soldCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Ced\CsMarketplace\Model\Session $customerSession,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollectionFactory,
        \Ced\CsReport\Model\ResourceModel\Product\Sold\CollectionFactory $soldCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    )
    {
        parent::__construct($context, $backendHelper, $data);
        $this->_vendorId = $customerSession->getVendorId();
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->soldCollectionFactory = $soldCollectionFactory;
        $this->setData('area', 'adminhtml');
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('soldproducts');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended|void
     */
    public function _prepareCollection()
    {

        $vendorId = $this->_vendorId;
        $itemsCollection = $this->itemCollectionFactory->create();
        $itemsCollection->addFieldtoFilter('vendor_id', $vendorId);
        $products = array();
        if (count($itemsCollection) > 0) {
            foreach ($itemsCollection as $data) {
                array_push($products, $data->getSku());
            }
        }
        $products = array_unique($products);
        $soldproduct = $this->soldCollectionFactory->create();

        $params = $this->_request->getParams();

        if ($params && isset($params['to']) && null != $params['to'] && isset($params['from']) && null != $params['from']) {
            $soldProductCollection = $soldproduct->addOrderedQty($params['to'], $params['from']);

            if (count($products) > 0)
                $soldproduct->addAttributeToFilter('sku', array('in' => $products));
        } else {
            $soldProductCollection = $soldproduct->addOrderedQty();
            if (count($products) > 0)
                $soldproduct->addAttributeToFilter('sku', array('in' => $products));
        }

        $soldProductCollection->addFilterToMap('ordered_qty', 'order_items.qty_ordered');
        $soldProductCollection->addFilterToMap('order_items_name', 'order_items.name');

        $this->setCollection($soldproduct);
        parent::_prepareCollection();

    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareFilterButtons()
    {
        $this->setChild(
            'reset_filter_button',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Button'
            )->setData(
                [
                    'label' => __('Reset Filter'),
                    'onclick' => $this->getJsObjectName() . '.resetFilter()',
                    'class' => 'action-reset action-tertiary',
                    'area' => 'adminhtml'
                ]
            )->setDataAttribute(['action' => 'grid-filter-reset'])
        );
        $this->setChild(
            'search_button',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Button'
            )->setData(
                [
                    'label' => __('Search'),
                    'onclick' => $this->getJsObjectName() . '.doFilter()',
                    'class' => 'action-secondary',
                    'area' => 'adminhtml'
                ]
            )->setDataAttribute(['action' => 'grid-filter-apply'])
        );
    }

    /**
     * @return $this|\Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Exception
     */
    public function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('sku', array(
            'header' => __('SKU'),
            'sortable' => true,
            'index' => 'sku',
            'type' => 'text',
            'align' => 'center'
        ));

        $this->addColumn('order_items_name', array(
            'header' => __('Product Name'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'order_items_name',
        ));

        $this->addColumn('product_type', array(
            'header' => __('Product Type'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'product_type',
        ));

        $this->addColumn('ordered_qty', array(
            'header' => __('Quantity'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'ordered_qty',
            'filter'    => false,
        ));
        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'csmarketplace/vproducts/edit',
            ['id' => $row->getId()]
        );
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/soldGrid', array('_current' => true));
    }
}