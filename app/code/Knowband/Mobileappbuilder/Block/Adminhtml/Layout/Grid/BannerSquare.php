<?php

namespace Knowband\Mobileappbuilder\Block\Adminhtml\Layout\Grid;

class BannerSquare extends \Magento\Backend\Block\Widget\Grid\Extended implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    private $sp_helper;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Knowband\Mobileappbuilder\Helper\Data $helper,
        \Knowband\Mobileappbuilder\Model\Banners $sp_bannersModel,
        \Knowband\Mobileappbuilder\Model\Layoutcomponent $sp_layoutComponent,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->sp_helper = $helper;
        $this->sp_bannersModel = $sp_bannersModel;
        $this->sp_layoutComponent = $sp_layoutComponent;
        parent::__construct($context, $backendHelper, $data);
    }
    
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setId('BannerSquareGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setDefaultLimit(10);
    }

    protected function _prepareCollection()
    {
        $id_component = $this->getRequest()->getParam("id_component", 0);
        
        $collection = $this->sp_bannersModel->getCollection()
                ->addFieldToFilter("id_component", ['eq' => (int) $id_component]);
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
    
    protected function _prepareColumns() {
        
        
        $id_component = $this->getRequest()->getParam("id_component", 0);
        $component_type = "";
        $type_col = $this->sp_layoutComponent->getCollection()
                ->addFieldToFilter("id_component", ['eq' => (int) $id_component]);
        $type_col->getSelect()->join(['ct' => $type_col->getTable("kb_mobileapp_component_types")], "main_table.id_component_type = ct.id");
        if($type_col->getSize()){
            $data = $type_col->getFirstItem();
            $component_type = $data->getComponentName();
            unset($data);
        }
        unset($type_col);
        
        $this->addColumn('id', [
                'header' => __('ID'),
                'index' => 'id',
                'filter' => false,
                'sortable' => false,
            ]
        );
        
        $this->addColumn('image', [
                'header' => __('Image'),
                'renderer' => 'Knowband\Mobileappbuilder\Block\Adminhtml\Layout\Renderers\BannerSquareImage',
                'filter' => false,
                'sortable' => false,
            ]
        );
        
        if($component_type == 'banners_countdown'){
            $this->addColumn('countdown', [
                    'header' => __('Upto Time'),
                    'index' => 'countdown',
                    'filter' => false,
                    'sortable' => false,
                ]
            );
            
            $this->addColumn('background_color', [
                    'header' => __('Background Color'),
                    'index' => 'background_color',
                    'filter' => false,
                    'sortable' => false,
                ]
            );
            
            $this->addColumn('text_color', [
                    'header' => __('Text Color'),
                    'index' => 'text_color',
                    'filter' => false,
                    'sortable' => false,
                ]
            );
        }
        
        $this->addColumn('redirect', [
                'header' => __('Redirect'),
                'index' => 'redirect_activity',
                'filter' => false,
                'sortable' => false,
            ]
        );
        
        $this->addColumn('category_id', [
                'header' => __('Category ID'),
                'index' => 'category_id',
                'filter' => false,
                'sortable' => false,
            ]
        );
        
        $this->addColumn('product_id', [
                'header' => __('Product ID'),
                'index' => 'product_id',
                'filter' => false,
                'sortable' => false,
            ]
        );
        
        $this->addColumn('product_name', [
                'header' => __('Product Name'),
                'index' => 'product_name',
                'filter' => false,
                'sortable' => false,
            ]
        );
        
        $this->addColumn('position', [
                'header' => __('Sort Order'),
                'index' => 'position',
                'filter' => false,
                'sortable' => false,
            ]
        );
        
        $this->addColumn('action', [
            'header' => __('Action'),
            'type' => 'action',
            'renderer' => 'Knowband\Mobileappbuilder\Block\Adminhtml\Layout\Renderers\BannerSquareAction',
            'filter' => false,
            'sortable' => false
        ]);
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row){
        return false;
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/bannerSquareAjax', ['_current' => true, 'id_component' => (int) $this->getRequest()->getParam("id_component", 0)]);
    }
    
    public function getTabLabel()
    {
        return __('Layout Settings');
    }

    public function getTabTitle()
    {
        return __('Layout Settings');
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
