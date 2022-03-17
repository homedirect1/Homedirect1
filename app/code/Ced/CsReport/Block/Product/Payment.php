<?php
namespace Ced\CsReport\Block\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;
class Payment extends \Magento\Backend\Block\Widget\Grid\Container
{
	protected $_template = 'sales/grid/vcontainer.phtml';
	protected function _construct()
    {
    
        $this->_controller = 'product_payments';
        $this->_blockGroup = 'Ced_CsReport';
        $this->_headerText = __('Payment Report');
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
        $this->setData('area','adminhtml');
    }
    
    public function getFilterActionUrl(){
        return $this->getUrl("csreport/product/payment", ["_use_rewrite" => true]);
    }

    public function getExportCsvAction(){
        return $this->getUrl("csreport/product/exportPaymentReport", ["_use_rewrite" => true]);
    }
}