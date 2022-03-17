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
 * @package     Ced_CsHyperlocal
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsHyperlocal\Block\Shiparea;

use Magento\Customer\Model\Session;

/**
 * Class Grid
 * @package Ced\CsHyperlocal\Block\Shiparea
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * Massaction block name
     *
     * @var string
     */
    protected $_massactionBlockName = 'Ced\CsHyperlocal\Block\Widget\Grid\Massaction';

    /**
     * @var Session
     */
    protected $session;

    protected $_type;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory
     */
    protected $shipareaCollectionFactory;

    /**
     * Grid constructor.
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        Session $customerSession,
        array $data = []
    )
    {
        $this->session = $customerSession;
        $this->shipareaCollectionFactory = $shipareaCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('cshyperlocalshiparea');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setData('area', 'adminhtml');
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended|void
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('*/*/delete'),
                'confirm' => __('Are you sure you want to delete.?')
            ]
        );
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $VendorId = $this->session->getVendorId();
        $collection = $this->shipareaCollectionFactory->create()
            ->addFieldToFilter('vendor_id', $VendorId);
        $collection->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
        $this->setCollection($collection);

        return parent::_prepareCollection();

    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();
        $statusArray = ['' => ' ', \Ced\CsHyperlocal\Model\Shiparea::STATUS_ENABLED => __('Enabled'),
            \Ced\CsHyperlocal\Model\Shiparea::STATUS_DISABLED => __('Disabled')];

        $this->addColumn('id', ['header' => __('Id'), 'index' => 'id']);
        $this->addColumn('location', ['header' => __('Location'), 'index' => 'location']);
        $this->addColumn('city', ['header' => __('City'), 'index' => 'city']);
        $this->addColumn('state', ['header' => __('State'), 'index' => 'state']);
        $this->addColumn('country', ['header' => __('Country'), 'index' => 'country']);
        $this->addColumn('status', ['header' => __('Status'), 'index' => 'status', 'type' => 'options',
            'options' => $statusArray]);

        $this->addColumn('custom_action', ['header' => __('Action'), 'type' => 'text', 'filter' => false, 'sortable' => false,
            'renderer' => 'Ced\CsHyperlocal\Block\Shiparea\Renderer\ColumnAction']);

        return parent::_prepareColumns();
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
