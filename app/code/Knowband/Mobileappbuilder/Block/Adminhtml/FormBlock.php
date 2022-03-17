<?php

namespace Knowband\Mobileappbuilder\Block\Adminhtml;

class FormBlock extends \Magento\Backend\Block\Template {

    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \Magento\Framework\Registry $registry,
            \Magento\Store\Model\Website $website,
            \Knowband\Mobileappbuilder\Model\Layouts $mabLayoutsModel,
            \Knowband\Mobileappbuilder\Model\Topcategory $mabTopCategory,
            \Knowband\Mobileappbuilder\Model\Productdata $mabProductComponent,
            \Knowband\Mobileappbuilder\Model\Layoutcomponent $mabLayoutComponent,
            \Knowband\Mobileappbuilder\Model\Componenttypes $mabComponentTypes,
            \Knowband\Mobileappbuilder\Helper\Data $mab_dataHelper,
            \Knowband\Mobileappbuilder\Helper\Components $mab_componentHelper
    ) {
        $this->_coreRegistry = $registry;
        $this->website = $website;
        $this->mab_layoutsModel = $mabLayoutsModel;
        $this->mab_topCategory = $mabTopCategory;
        $this->mab_productComponent = $mabProductComponent;
        $this->mab_layoutComponent = $mabLayoutComponent;
        $this->mab_componentTypes = $mabComponentTypes;
        $this->mab_dataHelper = $mab_dataHelper;
        $this->mab_componentHelper = $mab_componentHelper;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context);
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();
        return $this;
    }
    
    /**
     * Function to get all the categories of store
     * @return array
     */
    public function getCategories(){
        return $this->mab_dataHelper->getCategories();
    }
    
    /**
     * Function to get to category data
     * @return array
     */
    public function getTopCategoryData(){
        return $this->mab_componentHelper->getTopCategoryData();
    }
    
    /**
     * Function to get media base url
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->mab_dataHelper->getMediaUrl();
    }
    
    /**
     * Function to get all products
     * @return array
     */
    public function getAllProducts(){
        return $this->mab_componentHelper->getAllProducts();
    }
    
    /**
     * Function to get Product type
     * @return array
     */
    public function getProductTypes(){
       return $this->mab_componentHelper->getProductTypes();
    }
    
    /**
     * Function to get product form data
     * @return  array 
     */
    
    public function getProductFormData(){
       return $this->mab_componentHelper->getProductFormData();
    }
    
    /**
     * Function to get Component Type
     * @return array
     */
    public function getComponentTypeData() {
        return $this->mab_componentHelper->getComponentTypeData();
    }
    
    /**
     * Function to get current store id
     * @return int
     */
    public function getStoreId() {
        return $this->mab_componentHelper->getStoreId();
    }
    
    /**
     * FUnction to get layout name by layout id
     * @param int $id_layout
     * @return string
     */
    public function getLayoutNameById($id_layout = 0){
       return $this->mab_componentHelper->getLayoutNameById($id_layout);
    }
    
    /**
     * FUnction to get layout name by layout id
     * @param int $id_layout
     * @return string
     */
    public function getComponentHeading($id_component = 0){
        return $this->mab_componentHelper->getComponentHeading($id_component);
    }

}


