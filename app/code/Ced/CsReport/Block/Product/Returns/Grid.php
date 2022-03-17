<?php

namespace Ced\CsReport\Block\Product\Returns;

/**
 * Class Grid
 * @package Ced\CsReport\Block\Product\Returns
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    protected $_vendorId;

    /**
     * @var \Ced\CsRma\Model\ResourceModel\Request\CollectionFactory
     */
    protected $requestCollectionFactory;

    /**
     * Grid constructor.
     * @param \Ced\CsReport\Model\Rma $rmaFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Ced\CsReport\Model\Rma $rmaFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    )
    {
        parent::__construct($context, $backendHelper, $data);
        $this->_vendorId = $customerSession->getVendorId();
        $this->rmaFactory = $rmaFactory;
        $this->setData('area', 'adminhtml');
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('vreturngrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @param $vid
     */
    public function setVendorId($vid)
    {
        $this->vendorId = $vid;
    }

    /**
     * @return mixed
     */
    public function getVendorId()
    {
        return $this->vendorId;
    }

    /**
     * @return $this|\Magento\Backend\Block\Widget\Grid\Extended
     */
    public function _prepareCollection()
    {

        $vendorId = $this->_vendorId;
        if (is_null($vendorId)) {
            $vendorId = $this->vendorId;
        }

        $from = $this->_request->getParam('from');
        $to = $this->_request->getParam('to');

        $requestCollection = $this->rmaFactory->create()
            ->addFieldToFilter('vendor_id', $vendorId);

        if (isset($from) && $from != '' && isset($to) && $to != '') {
            $requestCollection->addFieldToFilter('main_table.created_at', array('from' => $from, 'to' => $to));
        }

        $this->setCollection($requestCollection);

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
            'header' => __('Status'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'status',
        ));

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/ReturnGrid', array('_current' => true));
    }
}
