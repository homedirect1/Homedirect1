<?php

namespace Ced\CsReport\Block\Product\Views;

/**
 * Class Grid
 * @package Ced\CsReport\Block\Product\Views
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var mixed
     */
    protected $_vendorId;

    /**
     * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
     */
    protected $prodCollectionFactory;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory
     */
    protected $vproductsCollectionFactory;

    /**
     * Grid constructor.
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory $vproductsCollectionFactory
     * @param \Ced\CsMarketplace\Model\Session $customerSession
     * @param \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $prodCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory $vproductsCollectionFactory,
        \Ced\CsMarketplace\Model\Session $customerSession,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $prodCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    )
    {
        parent::__construct($context, $backendHelper, $data);
        $this->_vendorId = $customerSession->getVendorId();
        $this->prodCollectionFactory = $prodCollectionFactory;
        $this->vproductsCollectionFactory = $vproductsCollectionFactory;
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
     * @return $this|\Magento\Backend\Block\Widget\Grid\Extended
     */
    public function _prepareCollection()
    {

        $vendorId = $this->_vendorId;
        $vproductsCollection = $this->vproductsCollectionFactory->create();
        $vproductsCollection->addFieldToFilter('vendor_id', $vendorId);
        $vproductIds = $vproductsCollection->getColumnValues('product_id');
        $params = $this->_request->getParams();
        $from = '';
        $to = '';
        if (isset($params['from'])) {
            $from = $params['from'];
        }
        if (isset($params['to'])) {
            $to = $params['to'];
            $to = date('Y-m-d', strtotime($params['to'] . ' +1 day'));
        }

        $productViewsCollection = $this->prodCollectionFactory->create()
            ->addAttributeToSelect('*');
        if($from && $to){
            $productViewsCollection->addViewsCount($from, $to);
        }else{
            $productViewsCollection->addViewsCount();
        }

        $productViewsCollection->addAttributeToFilter('entity_id', array('in' => $vproductIds));

        $productViewsCollection->addAttributeToSelect('product_name');
        $this->setCollection($productViewsCollection);

        parent::_prepareCollection();
        return $this;
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


        $this->addColumn('product_name', array(
            'header' => __('Product Name'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'name',
        ));


        $this->addColumn('type_id', array(
            'header' => __('Product Type'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'type_id',
        ));

        $this->addColumn('views', array(
            'header' => __('Views'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'views',
            'filter_condition_callback' => array($this, '_filterviews'),
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
        return $this->getUrl('*/*/viewsGrid', array('_current' => true));
    }

    /**
     * @param $collection
     * @param $column
     * @return $this|void
     */
    public function _filterviews($collection, $column)
    {
        if ($column->getFilter()->getValue() === null) {
            return;
        }
        $count = $column->getFilter()->getValue();

        $this->getCollection()->getSelect()->having('COUNT(report_table_views.event_id) = ?',
            $count);

        return $this;
    }
}
