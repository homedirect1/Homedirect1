<?php

namespace Knowband\Mobileappbuilder\Block\Adminhtml\Tab;

class PushNotificationHistory extends \Magento\Backend\Block\Widget\Grid\Extended implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    private $sp_helper;
    
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Knowband\Mobileappbuilder\Helper\Data $helper,
        \Knowband\Mobileappbuilder\Model\PushNotification $sp_pushNotificationModel,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->sp_helper = $helper;
        $this->sp_pushNotificationModel = $sp_pushNotificationModel;
        parent::__construct($context, $backendHelper, $data);
    }
    
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setId('AutoswitchGrid');
        $this->setDefaultSort('kb_notification_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setDefaultLimit(50);
    }

    protected function _prepareCollection()
    {
        $collection = $this->sp_pushNotificationModel->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
    
    protected function _prepareColumns() {
        
        $this->addColumn('kb_notification_id', array(
            'header' => __('ID'),
            'align' => 'right',
            'width' => '40px',
            'index' => 'kb_notification_id',
            'type' => 'number'
        ));

        $this->addColumn('title', array(
            'header' => __('Title'),
            'align' => 'left',
            'index' => 'title',
            'type' => 'text',
            'sortable' => false,
            'escape' => true
        ));
        
        $this->addColumn('image_type', array(
            'header' => __('Image Type'),
            'align' => 'left',
            'index' => 'image_type',
            'type' => 'options',
            'options' => array(
                'url' => __('URL'),
                'image' => __('Upload')
            )
        ));
        
        $this->addColumn('device_type', array(
            'header' => __('Device Type'),
            'align' => 'left',
            'width' => '160px',
            'index' => 'device_type',
            'type' => 'options',
            'options' => array(
                'both' => __('Both Android/iOS'),
                'android' => __('Android'),
                'ios' => __('iOS')
            ),
        ));
        
        $this->addColumn('redirect_activity', array(
            'header' => __('Redirect Activity'),
            'align' => 'left',
            'index' => 'redirect_activity',
            'type' => 'options',
            'options' => array(
                'home' => __('Home'),
                'category' => __('Category'),
                'product' => __('Product')
            )
        ));
        
        $this->addColumn('category_name', array(
            'header' => __('Category'),
            'align' => 'left',
            'index' => 'category_name',
            'type' => 'text',
            'escape' => true
        ));
        
        $this->addColumn('product_name', array(
            'header' => __('Product'),
            'align' => 'left',
            'index' => 'product_name',
            'type' => 'text',
            'escape' => true
        ));
        
        $this->addColumn('date_add', array(
            'header' => __('Sent Date'),
            'align' => 'left',
            'index' => 'date_add',
            'type' => 'datetime',
        ));
        
        $this->addColumn('action', array(
            'header' => __('Action'),
            'type' => 'action',
            'getter' => 'getEtsyOrderId',
            'renderer' => 'Knowband\Mobileappbuilder\Block\Adminhtml\Renderers\PushAction',
            'filter' => false,
            'sortable' => false
        ));
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row){
        return false;
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/pushNotificationAjax', ['_current' => true]);
    }
    
    public function getTabLabel()
    {
        return __('Push Notification History');
    }

    public function getTabTitle()
    {
        return __('Push Notification History');
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
