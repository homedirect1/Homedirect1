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

/**
 * Class ExportGrid
 * @package Ced\CsHyperlocal\Block\Shiparea
 */
class ExportGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory
     */
    protected $zipcodeCollectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * ExportGrid constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    )
    {
        $this->registry = $registry;
        $this->zipcodeCollectionFactory = $zipcodeCollectionFactory;
        parent::__construct($context, $backendHelper, $data);

    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('cshyperlocalshiparea');
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
        $VendorId = $this->registry->registry('vendor_id');
        $locationId = $this->registry->registry('location_id');
        $collection = $this->zipcodeCollectionFactory->create()
            ->addFieldToFilter('vendor_id', $VendorId)
            ->addFieldToFilter('location_id', $locationId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended|void
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();
        $statusArray = ['' => ' ', \Ced\CsHyperlocal\Model\Shiparea::STATUS_ENABLED => __('Enabled'),
            \Ced\CsHyperlocal\Model\Shiparea::STATUS_DISABLED => __('Disabled')];

        $this->addColumn('zipcode', ['header' => __('zipcode'), 'index' => 'zipcode']);
    }
}
