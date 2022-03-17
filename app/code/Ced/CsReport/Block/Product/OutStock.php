<?php
namespace Ced\CsReport\Block\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;
class OutStock extends \Magento\Backend\Block\Widget\Grid\Container
{
	protected $_template = 'sales/grid/outstockcontainer.phtml';
	protected function _construct()
    {
        $this->_controller = 'product_outStock';
        $this->_blockGroup = 'Ced_CsReport';
        
        parent::_construct();
        $this->_headerText = __('Out Of Stock Product(s)');
        
        $this->removeButton('add');
        
        $this->addButton(
        		'export_csv',
        		['label' => __('Export Csv'), 'onclick' => 'exportCsv()', 'class' => 'primary']
        );
        
        $this->setData('area','adminhtml');
    }
    
    public function getFilterActionUrl(){
    	return $this->getUrl("csreport/product/outStock", ["_use_rewrite" => true]);
    }
    
    public function getExportCsvAction(){
    	return $this->getUrl("csreport/product/exportOutStock", ["_use_rewrite" => true]);
    }
    
}