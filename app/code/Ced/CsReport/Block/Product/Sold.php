<?php
namespace Ced\CsReport\Block\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;
class Sold extends \Magento\Backend\Block\Widget\Grid\Container
{
	protected $_template = 'sales/grid/vcontainer.phtml';
	protected function _construct()
    {
    
        $this->_controller = 'product_sold';
        $this->_blockGroup = 'Ced_CsReport';
        $this->_headerText = __('Sold Product(s)');
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
        return $this->getUrl("csreport/product/sold", ["_use_rewrite" => true]);
    }

    public function getExportCsvAction(){
        return $this->getUrl("csreport/product/exportSales", ["_use_rewrite" => true]);
    }
}