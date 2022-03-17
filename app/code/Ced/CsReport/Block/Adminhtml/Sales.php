<?php

namespace Ced\CsReport\Block\Adminhtml;

/**
 * Class Sales
 * @package Ced\CsReport\Block\Adminhtml
 */
class Sales extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @var string
     */
    protected $_template = 'sales/grid/container.phtml';

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vendor\CollectionFactory
     */
    protected $vendorCollectionFactory;

    /**
     * Sales constructor.
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vendor\CollectionFactory $vendorCollectionFactory
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Ced\CsMarketplace\Model\ResourceModel\Vendor\CollectionFactory $vendorCollectionFactory,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->vendorCollectionFactory = $vendorCollectionFactory;
    }

    protected function _construct()
    {
        $this->_controller = 'adminhtml_sales';
        $this->_blockGroup = 'Ced_CsReport';
        $this->_headerText = __('Product(s) Sales Report');
        parent::_construct();
        $this->removeButton('add');
        $this->addButton(
            'filter_form_submit',
            ['label' => __('Show Report'), 'onclick' => 'filter()', 'class' => 'primary']
        );
        $this->addButton(
            'export_csv',
            ['label' => __('Export Csv'), 'onclick' => 'exportCsv()', 'class' => 'primary']
        );
    }

    /**
     * @return string
     */
    public function getFilterActionUrl()
    {
        return $this->getUrl("csreport/sales/index", ["_use_rewrite" => true]);
    }

    /**
     * @return string
     */
    public function getExportCsvAction()
    {
        return $this->getUrl("csreport/sales/exportCsv", ["_use_rewrite" => true]);
    }

    /**
     * @return mixed
     */
    public function getVendorCollection()
    {
        $vModel = $this->vendorCollectionFactory->create();
        return $vModel->addAttributeToSelect('*');
    }
}