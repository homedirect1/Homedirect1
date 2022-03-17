<?php
namespace Ced\CsReport\Block\Product\Payments ;
class ExportGrid extends \Magento\Backend\Block\Widget\Grid\Extended{
	protected $_resource;
	protected $_request;
	protected $_paymentFactory;
	protected $_vendorId ;
	public function __construct(
			\Magento\Backend\Block\Template\Context $context,
			\Magento\Backend\Helper\Data $backendHelper,
			\Magento\Framework\App\ResourceConnection $resource,
			\Magento\Framework\App\Request\Http $request,
			\Ced\CsMarketplace\Model\VpaymentFactory $vpaymentFactory,
			\Magento\Customer\Model\Session $customerSession
	) {
		parent::__construct($context,$backendHelper);
		$this->_resource = $resource;
		$this->_request = $request;
		$this->_paymentFactory = $vpaymentFactory;
		$this->_vendorId = $customerSession->getVendorId();
		$this->setData('area','adminhtml');
	}
	
	protected function _construct(){
		parent::_construct();
		$this->setFilterVisibility(false);
		$this->setId('vpaymentreport.grid');
		$this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
	}
	
	protected function _prepareCollection()
	{
		$vendor_id = $this->_vendorId; 
		if (!$vendor_id) {
            		$vendor_id = $this->getRequest()->getParam('vendor_id');
	}
		$collection = $this->_paymentFactory->create()->getCollection();
		$collection->addFieldToFilter('vendor_id',array('eq'=>$vendor_id));
		$from = $this->_request->getParam('from');
        $to = $this->_request->getParam('to');
        
        if(isset($to) && isset($from)){
        	if($to != '' && $from != '') {
        		$from .= ' 00:00:00';
            	$to .= ' 23:59:59';
        		$collection->addFieldToFilter('main_table.created_at', array('from'=>$from, 'to'=>$to));
        	}
        }
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
	}
	

	public function _prepareColumns() {
		
		
		$this->addColumn('amount_desc',
				array(
						'header'=> __('Order Description'),
						'index' => 'amount_desc',
						'type'          => 'text',
						'renderer'=> 'Ced\CsReport\Block\Product\Payments\Renderer\ExportOrderdesc',
				));
		$this->addColumn('created_at', array(
				'header'=> __('Transaction Date'),
				'width' => '80px',
				'type'  => 'date',
				'index' => 'created_at',
				'sortable' => false,
				
		));
		
		$this->addColumn('transaction_id', array(
				'header'    => __('Transaction ID#'),
				'align'     => 'left',
				'index'     => 'transaction_id',

		
		));
		
		$this->addColumn('base_amount',
				array(
						'header'=> __('Credit Amount'),
						'index' => 'base_amount',
						'type'          => 'currency',
						'currency' => 'base_currency'
		));
		
		$this->addColumn('status',
				array(
						'header'=> __('Status'),
						'index' => 'status',
						'align'     => 'left',
						'filter' => false,
						'renderer'=> 'Ced\CsReport\Block\Adminhtml\Payment\Renderer\Status',
		));
		
		
		return parent::_prepareColumns();
	}
}
