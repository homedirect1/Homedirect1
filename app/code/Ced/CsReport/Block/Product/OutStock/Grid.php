<?php

namespace Ced\CsReport\Block\Product\OutStock;

/**
 * Class Grid
 * @package Ced\CsReport\Block\Product\OutStock
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Ced\CsMarketplace\Model\Session
     */
    protected $session;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory
     */
    protected $vproductsCollectionFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Grid constructor.
     * @param \Ced\CsMarketplace\Model\Session $session
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory $vproductsCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Ced\CsMarketplace\Model\Session $session,
        \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory $vproductsCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    )
    {
        parent::__construct($context, $backendHelper, $data);
        $this->session = $session;
        $this->vproductsCollectionFactory = $vproductsCollectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->setData('area', 'adminhtml');
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('outstockproducts');
        $this->setDefaultSort('id');
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

        $vendorId = $this->session->getVendorId();

        $collection = $this->vproductsCollectionFactory->create();
        $collection->addFieldToFilter('vendor_id', $vendorId);
        $collection->addFieldToFilter('is_in_stock', 0);
        $vProductIds = $collection->getColumnValues('product_id');


        /*$coreResource = $this->resourceConnection;
        $inventoryTable = $coreResource->getTableName('cataloginventory_stock_item');

        $prefix = str_replace("cataloginventory_stock_item", "", $inventoryTable);
        $collection->getSelect()->joinLeft($prefix . 'cataloginventory_stock_item', '`main_table`.`product_id` = ' . $prefix . 'cataloginventory_stock_item.product_id',
            array('quantity' => $prefix . 'cataloginventory_stock_item.qty'));

        $collection->addFieldToFilter($prefix . 'cataloginventory_stock_item.is_in_stock', 0);
        $collection->addFilterToMap('quantity', $prefix . 'cataloginventory_stock_item.qty' );*/
        $this->setCollection($collection);
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


        $this->addColumn('name', array(
            'header' => __('Product Name'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'name',
        ));


        $this->addColumn('type', array(
            'header' => __('Product Type'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'type',
        ));

        $this->addColumn('qty', array(
            'header' => __('Quantity'),
            'width' => '80px',
            'type' => 'range',
            'index' => 'qty',
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
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}
