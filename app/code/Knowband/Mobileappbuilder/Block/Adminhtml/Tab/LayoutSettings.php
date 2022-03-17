<?php

namespace Knowband\Mobileappbuilder\Block\Adminhtml\Tab;

class LayoutSettings extends \Magento\Backend\Block\Widget\Grid\Extended implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    private $sp_helper;
    
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Knowband\Mobileappbuilder\Helper\Data $helper,
        \Knowband\Mobileappbuilder\Model\Layouts $sp_layoutModel,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->sp_helper = $helper;
        $this->sp_layoutModel = $sp_layoutModel;
        parent::__construct($context, $backendHelper, $data);
    }
    
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setId('AutoswitchLayoutGrid');
        $this->setDefaultSort('id_layout');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setDefaultLimit(50);
    }

    protected function _prepareCollection()
    {
        $collection = $this->sp_layoutModel->getCollection();
//        $collection->addFieldToFilter( 'type', 'slider' );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
    
    protected function _prepareColumns() {
        
        $this->addColumn('id_layout', [
                'header' => __('Layout ID'),
                'index' => 'id_layout',
            ]
        );
        
        $this->addColumn('title', [
                'header' => __('Layout Name'),
                'index' => 'layout_name',
            ]
        );
        
        $this->addColumn('action', [
            'header' => __('Action'),
            'type' => 'action',
            'renderer' => 'Knowband\Mobileappbuilder\Block\Adminhtml\Renderers\LayoutAction',
            'filter' => false,
            'sortable' => false
        ]);
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row){
        return false;
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/layoutAjax', ['_current' => true]);
    }
    
    public function getTabLabel()
    {
        return __('Home Page Layout Settings');
    }

    public function getTabTitle()
    {
        return __('Home Page Layout Settings');
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
