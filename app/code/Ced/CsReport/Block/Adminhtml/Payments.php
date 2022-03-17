<?php

namespace Ced\CsReport\Block\Adminhtml;

/**
 * Class Payments
 * @package Ced\CsReport\Block\Adminhtml
 */
class Payments extends \Magento\Backend\Block\Widget\Grid\Container
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
     * Payments constructor.
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

    public function _construct()
    {
        $this->_blockGroup = 'Ced_CsReport';
        $this->_controller = 'adminhtml_payment';
        parent::_construct();
        $this->buttonList->remove('add');
        $this->addButton(
            'filter_form_submit',
            ['label' => __('Show Report'), 'onclick' => 'filter()', 'class' => 'primary']
        );
        $this->addButton(
            'export_csv',
            ['label' => __('Export Csv'), 'onclick' => 'exportCsv()', 'class' => 'primary']
        );
        $this->_headerText = '';
    }

    /**
     * @return mixed
     */
    public function getVendorCollection()
    {
        $vModel = $this->vendorCollectionFactory->create();
        return $vModel->addAttributeToSelect('*');
    }

    public function getSaveUrl()
    {
    }

    public function getBackUrl()
    {
    }

    /**
     * @return string
     */
    public function getFilterActionUrl()
    {
        return $this->getUrl("csreport/payment/index", ["_use_rewrite" => true]);
    }

    /**
     * @return string
     */
    public function getExportCsvAction()
    {
        return $this->getUrl("csreport/payment/exportCsv", ["_use_rewrite" => true]);
    }
}