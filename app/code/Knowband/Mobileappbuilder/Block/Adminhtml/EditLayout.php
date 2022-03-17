<?php
namespace Knowband\Mobileappbuilder\Block\Adminhtml;

class EditLayout extends \Magento\Backend\Block\Template
{
    const DEFAULT_SECTION_BLOCK = 'Magento\Config\Block\System\Config\Form';
    private $storeManager;
    private $urlInterface;
    private $request;
    private $scopeConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Knowband\Mobileappbuilder\Model\Layoutcomponent $layoutComponent,
        \Knowband\Mobileappbuilder\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeManager = $context->getStoreManager();
        $this->urlInterface = $context->getUrlBuilder();
        $this->sp_helper = $helper;
        $this->scopeConfig = $context->getScopeConfig();
        $this->mab_layoutComponent = $layoutComponent;
        $this->request = $request;
    }

    protected function _construct()
    {
        $this->_controller = 'adminhtml_mobileappbuilder';
        $this->_blockGroup = 'Knowband_Mobileappbuilder';
        parent::_construct();
    }
    
    protected function _prepareLayout()
    {
        $this->_formBlockName = self::DEFAULT_SECTION_BLOCK;
        $back_url = $this->getUrl("*/*/index");
        $this->getToolbar()->addChild(
            'back_button', 'Magento\Backend\Block\Widget\Button', [
                'id' => 'mobileappbuilder-back',
                'label' => __('Back'),
                'class' => 'back',
                'on_click' => "location.href='".$back_url."';",
//                'data_attribute' => [
//                    'mage-init' => ['button' => []],
//                ]
            ]
        );
        $block = $this->getLayout()->createBlock($this->_formBlockName);
        $this->setChild('form', $block);
        return parent::_prepareLayout();
    }
    
    public function getLayoutData(){
        $id_layout = $this->getRequest()->getParam("id");
        $component_col = $this->mab_layoutComponent->getCollection()
                ->addFieldToFilter("id_layout", ['eq' => (int)$id_layout])
                ->setOrder("position", "ASC");
        $component_col->getSelect()->join(['ct' => $component_col->getTable('kb_mobileapp_component_types')], 'main_table.id_component_type=ct.id');
        $layout_data = $component_col->getData();
        unset($component_col);
        $result = [];
        foreach ($layout_data as $data){
            $result[] = ['id' => $data['id_component'], 'type' => $data['component_name']];
        }
        return json_encode($result);
    }
    
    public function getSettings($key = 'knowband/mobileappbuilder/settings')
    {
        return $this->sp_helper->getSavedSettings($key);
    }
    
    /**
     * Function to get media base url
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->sp_helper->getMediaUrl();
    }
}
