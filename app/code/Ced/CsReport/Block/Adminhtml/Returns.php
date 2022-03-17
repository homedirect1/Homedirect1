<?php

namespace Ced\CsReport\Block\Adminhtml;

/**
 * Class Returns
 * @package Ced\CsReport\Block\Adminhtml
 */
class Returns extends \Magento\Backend\Block\Widget\Grid\Container
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
     * Returns constructor.
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

    /**
     *
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_returns';
        $this->_blockGroup = 'Ced_CsReport';
        $this->_headerText = __('Product(s) Returns Report');
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
     * @return mixed
     */
    public function getVendorCollection()
    {
        $vModel = $this->vendorCollectionFactory->create();
        return $vModel->addAttributeToSelect('*');
    }

    /**
     * @return string
     */
    public function getFilterActionUrl()
    {
        return $this->getUrl("csreport/returns/index", ["_use_rewrite" => true]);
    }

    /**
     * @return string
     */
    public function getExportCsvAction()
    {
        return $this->getUrl("csreport/returns/exportCsv", ["_use_rewrite" => true]);
    }
}
