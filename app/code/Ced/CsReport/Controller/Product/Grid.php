<?php 
namespace Ced\CsReport\Controller\Product;
use Magento\Framework\View\Result\PageFactory;
 
class Grid extends \Ced\CsMarketplace\Controller\Vendor
{
    
    /**
     * Grid action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}