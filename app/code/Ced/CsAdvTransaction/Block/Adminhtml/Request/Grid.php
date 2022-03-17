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

namespace Ced\CsAdvTransaction\Block\Adminhtml\Request;

/**
 * Class Grid
 * @package Ced\CsAdvTransaction\Block\Adminhtml\Request
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Ced\CsAdvTransaction\Model\ResourceModel\Request\CollectionFactory
     */
    protected $requestCollection;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vendor\CollectionFactory
     */
    protected $vendorCollection;

    /**
     * Grid constructor.
     * @param \Ced\CsAdvTransaction\Model\ResourceModel\Request\CollectionFactory $requestCollection
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vendor\CollectionFactory $vendorCollection
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Ced\CsAdvTransaction\Model\ResourceModel\Request\CollectionFactory $requestCollection,
        \Ced\CsMarketplace\Model\ResourceModel\Vendor\CollectionFactory $vendorCollection,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    )
    {
        $this->requestCollection = $requestCollection;
        $this->vendorCollection = $vendorCollection;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('requestGrid');
        $this->setDefaultSort('vendor_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->requestCollection->create();
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('Id'),
                'type' => 'hidden',
                'index' => 'id',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display'
            ]
        );


        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'type' => 'date',
                'index' => 'created_at',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'vendor_id',
            [
                'header' => __('Vendor Name'),
                'type' => 'text',
                'index' => 'vendor_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'renderer' => 'Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid\Renderer\Vendorname',
                'filter_condition_callback' => array($this, '_vendornameFilter'),

            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'type' => 'options',
                'index' => 'status',
                'options' => ['0' => 'Pending', '1' => 'Approved']

            ]
        );

        $this->addColumn(
            'amount',
            [
                'header' => __('Amount'),
                'type' => 'text',
                'index' => 'amount',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'pay',
            [
                'header' => __('Pay'),
                'type' => 'text',
                'index' => 'vendor_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'renderer' => 'Ced\CsAdvTransaction\Block\Adminhtml\Vorders\Grid\Renderer\Request',
                'filter_condition_callback' => array($this, '_vendornameFilter'),

            ]
        );

        return parent::_prepareColumns();
    }


    /**
     * After load collection
     *
     * @return void
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * Filter store condition
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \Magento\Framework\DataObject $column
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _filterStoreCondition($collection, \Magento\Framework\DataObject $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('csadvtransaction/pay/request/', ['_current' => true]);
    }


    /**
     * @param $collection
     * @param $column
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _vendornameFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $vendorIds = $this->vendorCollection->create()
            ->addAttributeToFilter('name', array('like' => '%' . $column->getFilter()->getValue() . '%'))
            ->getAllIds();

        if (count($vendorIds) > 0)
            $this->getCollection()->addFieldToFilter('vendor_id', array('in', $vendorIds));
        else {
            $this->getCollection()->addFieldToFilter('vendor_id');
        }
        return $this;
    }

}
