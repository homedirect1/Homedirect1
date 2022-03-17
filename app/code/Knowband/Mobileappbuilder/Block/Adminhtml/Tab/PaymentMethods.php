<?php

namespace Knowband\Mobileappbuilder\Block\Adminhtml\Tab;

class PaymentMethods extends \Magento\Backend\Block\Widget\Grid\Extended implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    private $sp_helper;
    
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Knowband\Mobileappbuilder\Helper\Data $helper,
        \Knowband\Mobileappbuilder\Model\Payment $sp_paymentModel,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->sp_helper = $helper;
        $this->sp_paymentModel = $sp_paymentModel;
        parent::__construct($context, $backendHelper, $data);
    }
    
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setId('kb_payments_grid');
        $this->setDefaultSort('kb_payment_id');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setDefaultLimit(50);
    }

    protected function _prepareCollection()
    {
        $collection = $this->sp_paymentModel->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
    
    protected function _prepareColumns() {
        
        $this->addColumn('paymentmethod_code', array(
                'header' => __('Code'),
                'index' => 'kb_payment_code'
            )
        );

        $this->addColumn('name', array(
                'header' => __('Name'),
                'index' => 'kb_payment_name'
            )
        );
        $options = array(
            '0' => __('Disable'),
            '1' => __('Enable')
        );
        $this->addColumn('status', array(
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $options,
            )
        );
        
        $this->addColumn('action', array(
            'header' => __('Action'),
            'type' => 'action',
            'renderer' => 'Knowband\Mobileappbuilder\Block\Adminhtml\Renderers\PaymentMethodAction',
            'filter' => false,
            'sortable' => false
        ));
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row){
        return false;
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/paymentAjax', ['_current' => true]);
    }
    
    public function getTabLabel()
    {
        return __('Payment Method Settings');
    }

    public function getTabTitle()
    {
        return __('Payment Method Settings');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getSettings($key)
    {
        return $this->sp_helper->getSavedSettings($key);
    }
    
}
