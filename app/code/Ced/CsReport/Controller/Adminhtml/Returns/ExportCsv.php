<?php
namespace Ced\CsReport\Controller\Adminhtml\Returns;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
class ExportCsv extends \Magento\Backend\App\Action{

	protected $_request;
	protected $_fileFactory;
	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\App\Response\Http\FileFactory $fileFactory
	) {
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
		$this->_request = $request;
		$this->_fileFactory = $fileFactory;
	}


	public function execute(){
		
		$vendor_id = $this->_request->getParam('vendor_id');
		$from = $this->_request->getParam('from');
		$to = $this->_request->getParam('to');
		$fileName   = 'vendor-'.$vendor_id.'-'.$from.'-sales.csv';
		$gridBlock = $this->_view->getLayout()->createBlock( 
                'Ced\CsReport\Block\Adminhtml\Returns\Grid'
        );
        $content = $gridBlock->getCsvFile();
        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
	}
}
