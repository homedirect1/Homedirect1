<?php

namespace Knowband\Mobileappbuilder\Block\Adminhtml\Tab;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Helper\Category;

class CategoryIcon extends \Magento\Backend\Block\Widget implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    private $sp_helper;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Knowband\Mobileappbuilder\Helper\Data $helper,
        \Magento\Catalog\Helper\Category $catalogCategory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        array $data = []
    ) {
        $this->sp_helper = $helper;
        $this->_categoryHelper = $catalogCategory;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context, $data);
    }

    public function getTabLabel()
    {
        return __('Category Icon Settings');
    }

    public function getTabTitle()
    {
        return __('Category Icon Settings');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
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
    
 
     public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->_categoryHelper->getStoreCategories($sorted , $asCollection, $toLoad);
    }

    /*  
     * Option getter
     * @return array
     */
    public function toOptionSArray()
    {


        $arr = $this->toCategoryArray();
        $ret = [];

        foreach ($arr as $key => $value)
        {

            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        return $ret;
    }

    /*
     * Get options in "key-value" format
     * @return array
     */
    public function toCategoryArray()
    {

        $categories = $this->getStoreCategories(true,false,true);
        $categoryList = $this->renderCategories($categories);
        return $categoryList;
    }

    public function renderCategories($_categories)
    {
        foreach ($_categories as $category){
            $i = 0; 
            $this->categoryList[$category->getEntityId()] = __($category->getName());   // Main categories
            $list = $this->renderSubCat($category,$i);
        }

        return $this->categoryList;     
    }

    public function renderSubCat($cat,$j){

        $categoryObj = $this->categoryRepository->get($cat->getId());

        $level = $categoryObj->getLevel();
        $arrow = str_repeat("---", $level-1);
        $subcategories = $categoryObj->getChildrenCategories(); 

        foreach($subcategories as $subcategory) {
            $this->categoryList[$subcategory->getEntityId()] = __($arrow.$subcategory->getName()); 

            if($subcategory->hasChildren()) {

                $this->renderSubCat($subcategory,$j);

            }
        } 

        return $this->categoryList;
    }
}
