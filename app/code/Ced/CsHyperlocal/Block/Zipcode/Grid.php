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

namespace Ced\CsHyperlocal\Block\Zipcode;

use Magento\Customer\Model\Session;

/**
 * Class Grid
 * @package Ced\CsHyperlocal\Block\Zipcode
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

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory
     */
    protected $zipcodeCollectionFactory;

    /**
     * Grid constructor.
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param Session $customerSession
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        Session $customerSession,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->session = $customerSession;
        $this->registry = $registry;
        $this->zipcodeCollectionFactory = $zipcodeCollectionFactory;
        parent::__construct($context, $backendHelper, $data);

    }

    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('cshyperlocalzipcode');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setData('area', 'adminhtml');
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended|void
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');
        $locationId = $this->registry->registry('location_id');
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('*/zipcode/delete', array('location_id' => $locationId)),
                'confirm' => __('Are you sure you want to delete?')
            ]
        );
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $VendorId = $this->session->getVendorId();
        $locationId = $this->getRequest()->getParam('id');
        $collection = $this->zipcodeCollectionFactory->create()->addFieldToFilter('vendor_id', $VendorId)
            ->addFieldToFilter('location_id', $locationId);
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
        $this->addColumn('zipcode', ['header' => __('Zipcode'), 'index' => 'zipcode']);

        $this->addColumn('action',
            [
                'header' => __('Action'),
                'width' => '50px',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => __('Edit'),
                        'url' => array(
                            'base' => '*/zipcode/edit',
                            'params' => array('location_id' => $this->getRequest()->getParam('id'))
                        ),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
            ]);


        return parent::_prepareColumns();
    }


    /**
     * Row click url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/zipcode/grid', array('id' => $this->getRequest()->getParam('id')));
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/zipcode/edit', array('id' => $row->getId(), 'location_id' => $this->getRequest()->getParam('id')));
    }
}
