<?php

namespace Knowband\Mobileappbuilder\Block\Adminhtml\Tab;

class SliderSettings extends \Magento\Backend\Block\Widget\Grid\Extended implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    private $sp_helper;
    
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Knowband\Mobileappbuilder\Helper\Data $helper,
        \Knowband\Mobileappbuilder\Model\Banner $sp_bannerModel,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->sp_helper = $helper;
        $this->sp_bannerModel = $sp_bannerModel;
        parent::__construct($context, $backendHelper, $data);
    }
    
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setId('slider_grid');
        $this->setDefaultSort('kb_banner_id');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setDefaultLimit(50);
    }

    protected function _prepareCollection()
    {
        $collection = $this->sp_bannerModel->getCollection();
        $collection->addFieldToFilter( 'type', 'slider' );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
    
    protected function _prepareColumns() {
        
        $this->addColumn('title', array(
                'header' => __('Title'),
                'index' => 'kb_banner_id',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'Knowband\Mobileappbuilder\Block\Adminhtml\Renderers\BannerTitle'
            )
        );

        $options = array(
            'url' => __('URL'),
            'image' => __('Upload')
        );
        $this->addColumn('image_type', array(
                'header' => __('Image Type'),
                'index' => 'image_type',
                'type' => 'options',
                'options' => $options,
            )
        );
        $options = array(
            'home' => __('Home'),
            'category' => __('Category'),
            'product' => __('Product')
        );
        $this->addColumn('redirect_activity', array(
                'header' => __('Redirect Activity'),
                'index' => 'redirect_activity',
                'type' => 'options',
                'options' => $options,
            )
        );

        $this->addColumn('category', array(
                'header' => __('Category'),
                'index' => 'category_name',
                'type' => 'text',
            )
        );
        $this->addColumn('product', array(
                'header' => __('Product'),
                'index' => 'product_name'
            )
        );
        $options = array(
            0 => __('Disable'),
            1 => __('Enable')
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
            'renderer' => 'Knowband\Mobileappbuilder\Block\Adminhtml\Renderers\BannerAction',
            'filter' => false,
            'sortable' => false
        ));
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row){
        return false;
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/sliderAjax', ['_current' => true]);
    }
    
    public function getTabLabel()
    {
        return __('Slider Settings');
    }

    public function getTabTitle()
    {
        return __('Slider Settings');
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
