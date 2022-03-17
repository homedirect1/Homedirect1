<?php

namespace Knowband\Mobileappbuilder\Controller\Adminhtml\Mobileappbuilder;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
class LayoutAjaxAction extends \Magento\Framework\App\Action\Action
{
    protected $mab_resultRawFactory;
    protected $mab_request;
    protected $mab_helper;
    protected $mab_scopeConfig;
    protected $inlineTranslation;
    protected $mab_transportBuilder;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultRawFactory,
        LayoutFactory $viewLayoutFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Model\CategoryFactory $_categoryLoader,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        \Knowband\Mobileappbuilder\Helper\Data $mab_helper,
        \Knowband\Mobileappbuilder\Helper\Components $mab_componentHelper,
        \Knowband\Mobileappbuilder\Model\Layouts $mabLayoutsModel,
        \Knowband\Mobileappbuilder\Model\Componenttypes $mabComponentTypes,
        \Knowband\Mobileappbuilder\Model\Layoutcomponent $mabLayoutComponent,
        \Knowband\Mobileappbuilder\Model\Topcategory $mabTopCategory,
        \Knowband\Mobileappbuilder\Model\Banners $mabBanners,
        \Knowband\Mobileappbuilder\Model\Productdata $mabProductComponent
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $registry;
        $this->directory_list = $directory_list;  
        $this->mab_resultRawFactory = $resultRawFactory;
        $this->_viewLayoutFactory = $viewLayoutFactory;
        $this->mab_helper = $mab_helper;
        $this->mab_layoutsModel = $mabLayoutsModel;
        $this->mab_componentType = $mabComponentTypes;
        $this->mab_layoutComponent = $mabLayoutComponent;
        $this->mab_topCategory = $mabTopCategory;
        $this->mab_bannersComponent = $mabBanners;
        $this->mab_productComponent = $mabProductComponent;
        $this->mab_componentHelper = $mab_componentHelper;
        $this->_filesystem = $fileSystem;
        $this->_product = $productModel;
        $this->_categoryLoader = $_categoryLoader;
    }

    public function execute() {
        $post_value = $this->getRequest()->getParams();
        if (isset($post_value['assign_component_id']) && $post_value['assign_component_id']) {
            $id_component = $this->assignComponent($post_value);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($id_component);
            return $resultJson;
        }
        
        if(isset($post_value['getComponentHeadingForm']) && $post_value['getComponentHeadingForm']){
            $this->getComponentHeadingForm();
        }
        
        if(isset($post_value['getAddLayoutForm']) && $post_value['getAddLayoutForm']){
            $this->getAddLayoutForm();
        }
        
        if(isset($post_value['getCategoryForm']) && $post_value['getCategoryForm']){
            $this->getTopCategoryForm();
        }
        
        if(isset($post_value['getBannerForm']) && $post_value['getBannerForm']){
            $this->getBannerSquareForm();
        }
        
        if(isset($post_value['getProductForm']) && $post_value['getProductForm']){
            $this->getProductGridForm();
        }
        
         if(isset($post_value['getRecentProducts']) && $post_value['getRecentProducts']){
            $this->getRecentProducts();
        }
        
        if(isset($post_value['getEditBannerFormData']) && $post_value['getEditBannerFormData']){
            $id_banner = (int) $this->getRequest()->getParam("id_banner", 0);
            $response = $this->getEditBannerFormData($id_banner);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($response);
            return $resultJson;
        }
        
        if(isset($post_value['deleteLayout']) && $post_value['deleteLayout']){
            $id_layout = (int) $this->getRequest()->getParam("id_layout", 0);
            $response = $this->deleteLayout($id_layout);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($response);
            return $resultJson;
        }
        
        if(isset($post_value['deleteComponent']) && $post_value['deleteComponent']){
            $id_component = (int) $this->getRequest()->getParam("id_component");
            $response = $this->deleteComponent($id_component);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($response);
            return $resultJson;
        }
        
        if(isset($post_value['deleteSliderBanner']) && $post_value['deleteSliderBanner']){
            $id_banner = (int) $this->getRequest()->getParam("id_banner");
            $response = $this->deleteSliderBanner($id_banner);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($response);
            return $resultJson;
        }
        
        if(isset($post_value['saveLayoutForm']) && $post_value['saveLayoutForm']){
            $response = $this->saveLayoutFormData($post_value);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($response);
            return $resultJson;
        }
        
        if(isset($post_value['saveHeadingFormData']) && $post_value['saveHeadingFormData']){
            $response = $this->saveComponentHeadingFormData($post_value);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($response);
            return $resultJson;
        }
        
        if(isset($post_value['saveTopcategoryFormData']) && $post_value['saveTopcategoryFormData']){
            $response = $this->saveTopCategoryFormData($this->getRequest());
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($response);
            return $resultJson;
        }
        
        if(isset($post_value['saveBannerSliderFormData']) && $post_value['saveBannerSliderFormData']){
            $response = $this->saveBannerSliderFormData($this->getRequest());
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($response);
            return $resultJson;
        }
        
        if(isset($post_value['saveProductFormData']) && $post_value['saveProductFormData']){
            $response = $this->saveProductFormData($this->getRequest());
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($response);
            return $resultJson;
        }
        
        if(isset($post_value['getCategoryProducts']) && $post_value['getCategoryProducts']){
            $categoryId = (int) $this->getRequest()->getParam("id_category");
            $prev_category_arr = explode(",", $this->getRequest()->getParam("category_prev_products"));
            $response = $this->getProductsByCategory($categoryId, $prev_category_arr);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($response);
            return $resultJson;
        }
        
        if(isset($post_value['setComponentOrder']) && $post_value['setComponentOrder']){
            $response = [];
            try {
                $id_layout = (int) $this->getRequest()->getParam("id_layout", 0);
                $position_array = $this->getRequest()->getParam("position_array");
//                $position_array = explode(",", $position_val);
                if (!empty($position_array)) {
                    $position = 1;
                    foreach ($position_array as $key => $pos_val) {
                        if (!empty($pos_val)) {
                            $position_arr = explode("_", $pos_val);
                            if (isset($position_arr[3])) {
                                $model = $this->mab_layoutComponent->load((int) $position_arr[3]);
                                $model->setPosition($position);
                                $model->save();
                                $model->unsetData();
                                $position += 1;
                            }
                        }
                    }
                }
                $response['success'] = __("Data saved successfully.");
            } catch (\Exception $ex) {
                $response['error'] = $ex->getMessage();
            }
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($response);
            return $resultJson;
        }
    }
    
    /**
     * Function to delete component from db
     * @param int $id_component
     * @return array
     */
    private function deleteComponent($id_component = 0) {
        $response = [];
        try {
            if ($id_component) {
                $model = $this->mab_layoutComponent->load($id_component);
                $model->delete();
                $response['success'] = __("Component deleted successfully.");
            }
        } catch (\Exception $ex) {
            $response['error'] = $ex->getMessage();
        }
        return $response;
    }
    
    /**
     * Function to delete banner data from db
     * @param int $id_component
     * @return array
     */
    private function deleteSliderBanner($id_banner = 0) {
        $response = [];
        try {
            if ($id_banner) {
                $model = $this->mab_bannersComponent->load($id_banner);
                $model->delete();
                $response['success'] = __("Banner data deleted successfully.");
            }
        } catch (\Exception $ex) {
            $response['error'] = $ex->getMessage();
        }
        return $response;
    }
    
    
    /**
     * Function to delete layout
     *
     * @param int $id_layout
     * @return array
     */
    private function deleteLayout($id_layout = 0) {
        $response = [];
        try {
            if ($id_layout) {
                $model = $this->mab_layoutsModel->load($id_layout);
                $model->delete();
                $response['success'] = true;
                $response['msg'] = __("Layout deleted successfully.");
            }
        } catch (\Exception $ex) {
            $response['error'] = true;
            $response['msg'] = $ex->getMessage();
        }
        return $response;
    }
    
    /**
     * Function to get banner data by banner id
     * @param int $id_banner
     * @return array
     */
    private function getEditBannerFormData($id_banner = 0) {
        $response = [];
        try {
            if ($id_banner) {
                $model = $this->mab_bannersComponent->load($id_banner);
                $banner_data = $model->getData();
                $model->unsetData();
                return $banner_data;
            }
        } catch (\Exception $ex) {
            $response['error'] = $ex->getMessage();
        }
        return $response;
    }
    
    /**
     * Function to save component heading form data into DB
     * @param array $post_value
     * @return array
     */
    private function saveComponentHeadingFormData($post_value = []) {
        $response = [];
        try {
            if (isset($post_value['id_component'])) {
                $model = $this->mab_layoutComponent->load((int) $post_value['id_component']);
                $model->setComponentHeading($post_value['component_heading']);
                $model->save();
                $model->unsetData();
                $response['success'] = __("Data has been saved successfully.");
            }
        } catch (\Exception $ex) {
            $response['error'] = $ex->getMessage();
        }
        return $response;
    }
    
    /**
     * Function to save layout form data into DB
     * @param array $post_value
     * @return array
     */
    private function saveLayoutFormData($post_value = []) {
        $response = [];
        try {
            if (isset($post_value['layout_name'])) {
                $model = $this->mab_layoutsModel;
                if (isset($post_value['layout_id']) && $post_value['layout_id'] > 0) {
                    $model->load((int) $post_value['layout_id']);
                    $model->setLayoutName($post_value['layout_name']);
                    $model->setDateUpdate($this->mab_helper->getDate());
                } else {
                    $model->setLayoutName($post_value['layout_name']);
                    $model->setStoreId((int) $post_value['store_id']);
                    $model->setDateAdded($this->mab_helper->getDate());
                    $model->setDateUpdate($this->mab_helper->getDate());
                }
                $model->save();
                $model->unsetData();
                $response['success'] = true;
                $response['msg'] = __("Data has been saved successfully.");
            }
        } catch (\Exception $ex) {
            $response['error'] = true;
            $response['msg'] = $ex->getMessage();
        }
        return $response;
    }

    /**
     * Function to save component into db
     * @param array $post_value
     * @return boolean|int
     */
    private function assignComponent($post_value = []){
        try {
            $id_layout = (int) $post_value['id_layout'];
            $component_type = $post_value['component_type'];
            $position = 1;
            $typeModel = $this->mab_componentType->load($component_type, 'component_name');
            $id_component_type = $typeModel->getId();
            $typeModel->unsetData();

            $component_col = $this->mab_layoutComponent->getCollection()
                    ->addFieldToFilter("id_layout", ['eq' => $id_layout])
                    ->setOrder('position', 'DESC');
            if ($component_col->getSize()) {
                $component_data = $component_col->getData();
                $position = $component_data[0]['position'] + 1;
            }

            unset($component_col);
            $component_model = $this->mab_layoutComponent;
            $component_model->setIdLayout((int) $id_layout);
            $component_model->setIdComponentType((int) $id_component_type);
            $component_model->setPosition($position);
            $component_model->save();
            $id_component = $component_model->getIdComponent();
            $component_model->unsetData();
            return $id_component;
        } catch (\Exception $ex) {
            return false;
        }
    }
    
    /**
     * Function to get Edit Component Form HTML
     * @return string
     */
    private function getComponentHeadingForm(){
        $itemsBlock = $this->_viewLayoutFactory->create()->createBlock('Knowband\Mobileappbuilder\Block\Adminhtml\FormBlock');
        $output = $itemsBlock->setTemplate('Knowband_Mobileappbuilder::component/edit_heading_form.phtml')->toHtml();
        $this->getResponse()->appendBody($output);
    }
    
    
     /**
     * Function to get New Layout Form HTML
     * @return string
     */
    private function getAddLayoutForm(){
        $itemsBlock = $this->_viewLayoutFactory->create()->createBlock('Knowband\Mobileappbuilder\Block\Adminhtml\FormBlock');
        $output = $itemsBlock->setTemplate('Knowband_Mobileappbuilder::add_layout_form.phtml')->toHtml();
        $this->getResponse()->appendBody($output);
    }
    
    /**
     * Function to get Top Category Form HTML
     * @return string
     */
    private function getTopCategoryForm(){
//        $this->_coreRegistry->register("component_form_submit_action", "adfasfas");
        $itemsBlock = $this->_viewLayoutFactory->create()->createBlock('Knowband\Mobileappbuilder\Block\Adminhtml\FormBlock');
        $data['html'] = $itemsBlock->setTemplate('Knowband_Mobileappbuilder::component/top_category.phtml')->toHtml();
        $elementData = $this->mab_componentHelper->getElementData()??"";
        $data['Added_Categories'] = $elementData['data']??[];
        $this->getResponse()->appendBody(json_encode($data));
    }
    
    /**
     * Function to get Banner Square Form HTML
     * @return string
     */
    private function getBannerSquareForm(){
        
        $itemsBlock = $this->_viewLayoutFactory->create()->createBlock('Knowband\Mobileappbuilder\Block\Adminhtml\FormBlock');
        $output = $itemsBlock->setTemplate('Knowband_Mobileappbuilder::component/banner_square.phtml')->toHtml();
        $data['html'] = $output;
        $elementData = $this->mab_componentHelper->getElementData()??"";
        $data['Added_Banners'] = $elementData['data']??[];
        $data['component_heading'] = $elementData['heading']??"";
        $this->getResponse()->appendBody(json_encode($data));
    }
    
    /**
     * Function to get Product Grid Form HTML
     * @return string
     */
    private function getProductGridForm(){
        $itemsBlock = $this->_viewLayoutFactory->create()->createBlock('Knowband\Mobileappbuilder\Block\Adminhtml\FormBlock');
        $output = $itemsBlock->setTemplate('Knowband_Mobileappbuilder::component/product_grid.phtml')->toHtml();
        $data['html'] = $output;
        $elementData = $this->mab_componentHelper->getElementData()??"";
        $data['added_Products']['products_for_preview'] = $elementData['data']??[];
        $data['added_Products']['component_heading_preview'] = $elementData['heading']??"";
        $this->getResponse()->appendBody(json_encode($data));
    }
    
    /**
     * Function to get Product Grid Form HTML
     * @return string
     */
    private function getRecentProducts(){
        
        $elementData = $this->mab_componentHelper->getElementData()??"";
        $data['added_Products']['products_for_preview'] = $elementData['data']??[];
        $data['added_Products']['component_heading_preview'] = $elementData['heading']??"";
        $this->getResponse()->appendBody(json_encode($data));
    }
    
    /**
     * Function to save the top category data into DB
     * @param object $request
     */
    private function saveTopCategoryFormData($request) {
        $response = [];
        try {
            $post_value = $request->getParams();
            $files_value = $request->getFiles();
            $id_component = (int) $post_value['id_component'];
            $image_content_mode = $post_value['image_content_mode'];

            $id_category_array = '';
            $image_name_array = '';

            for ($i = 1; $i <= 8; $i++) {
                $position = 1;
                if (isset($post_value['id_category_' . $i])) {
                    $id_category_array .= (int) $post_value['id_category_' . $i] . '|';
                    if (isset($files_value['image_' . $i])) {
                            $file = $files_value['image_' . $i];
                            $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
                            if (($file['error'] == 0) && !empty($file['name'])) {
                                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                                $image_name = 'kbmobileapp_tc_' . $i . '_' . time() .".". $file_extension;
                                $file['name'] = $image_name;
                                $uploader = $this->_objectManager->create(
                                        '\Magento\MediaStorage\Model\File\Uploader', ['fileId' => $file]
                                );
                                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
                                $uploader->setFilesDispersion(false);
                                $imageAdapterFactory = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')
                                        ->create();
                                $uploader->setAllowRenameFiles(true);
                                $uploader->setAllowCreateFolders(true);
                                $result = $uploader->save(
                                        $mediaDirectory
                                                ->getAbsolutePath('Knowband_Mobileappbuilder')
                                );
                                $image_name_array .= $image_name . '|';
                            }
                    } else {
                        $top_category_col = $this->mab_topCategory->getCollection()
                                ->addFieldToFilter("id_component", ['eq' => (int) $id_component]);
                        $categories_array = $top_category_col->getData();
                        unset($top_category_col);
                        if (!empty($categories_array)) {
                            $cat_arr = explode('|', $categories_array[0]['id_category']);
                            $image_arr = [];
                            $image_arr = explode('|', $categories_array[0]['image_url']);
                            $id_category_component = $i - 1;
                            if(isset($image_arr[$id_category_component])){
                                $image_name_array .= $image_arr[$id_category_component] . '|';
                            } else {
                                $image_name_array .= '|';
                            }
                        } else {
                            $image_name_array .= '|';
                        }
                    }
                }
            }
            $top_category_col = $this->mab_topCategory->getCollection()
                    ->addFieldToFilter("id_component", ['eq' => (int) $id_component]);
            if ($top_category_col->getSize()) {
                $top_category_model = $this->mab_topCategory->load((int) $id_component, 'id_component');
                $top_category_model->setIdCategory($id_category_array);
                $top_category_model->setImageUrl($image_name_array);
                $top_category_model->setImageContentMode($image_content_mode);
                $top_category_model->save();
                $top_category_model->unsetData();
            } else {
                $top_category_model = $this->mab_topCategory;
                $top_category_model->setIdComponent($id_component);
                $top_category_model->setIdCategory($id_category_array);
                $top_category_model->setImageUrl($image_name_array);
                $top_category_model->setImageContentMode($image_content_mode);
                $top_category_model->save();
                $top_category_model->unsetData();
            }
            unset($top_category_col);
            $response['success'] = __('Data has been saved successfully.');
            
            $elementData = $this->mab_componentHelper->getElementData()??"";
            $response['component_heading'] = $elementData['heading']??"";
            $response['Added_Categories'] = $elementData['data']??[];
        } catch (\Exception $ex) {
            $response['error'] = $ex->getMessage();
        }
        return json_encode($response);
    }

    /**
     * Function to save the top category data into DB
     * @param object $request
     */
    private function saveBannerSliderFormData($request) {
        $response = [];
        try {
            $post_value = $request->getParams();
            $files_value = $request->getFiles();
            $id_component = (int) $post_value['id_component'];
            $id_layout = (int) $post_value['id_layout'];
            $image_url =  $post_value['image_url'];
            $position = (int) $post_value['position'];
            $image_type =  $post_value['image_type'];
            $redirect_activity =  $post_value['redirect_activity'];
            $category_id =  isset($post_value['category_id'])?$post_value['category_id']:0;
            $image_content_mode = isset($post_value['image_content_mode'])?$post_value['image_content_mode']:"";
            $redirect_product_id = isset($post_value['redirect_product_id'])?$post_value['redirect_product_id']:0;
            if ($redirect_product_id == '') {
                $redirect_product_id = 0;
            }
            
            //update the banner heading in db
            $layout_component_model = $this->mab_layoutComponent->load($id_component);
            $id_component_type = $layout_component_model->getIdComponentType();
            $layout_component_model->unsetData();
            
            if($image_type == 'image'){
                if(isset($files_value['image'])){
                    $file = $files_value['image'];
                    if (($file['error'] == 0) && !empty($file['name'])) {
                        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
                        $path = $mediaDirectory->getAbsolutePath('Knowband_Mobileappbuilder');
                        $image_name = 'kbmobileapp_bs_' . time() . '.'.$file_extension;
                        $file['name'] = $image_name;
                        $uploader = $this->_objectManager->create(
                                '\Magento\MediaStorage\Model\File\Uploader', ['fileId' => $file]
                        );
                        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
                        $uploader->setFilesDispersion(false);
                        $imageAdapterFactory = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')
                                ->create();
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setAllowCreateFolders(true);
                        $result = $uploader->save(
                                $mediaDirectory
                                        ->getAbsolutePath('Knowband_Mobileappbuilder')
                        );
                        
                        $image_url = $this->mab_helper->getMediaUrl(). "/Knowband_Mobileappbuilder/" .$image_name;
                    }
                }
            }
            $product_name = $this->getProductNameById($redirect_product_id);
            $countdown = isset($post_value['countdown_validity'])?$post_value['countdown_validity']:"";
            $is_enabled_background_color = isset($post_value['is_enabled_background_color'])?$post_value['is_enabled_background_color']:0;
            $timer_background_color = isset($post_value['timer_background_color'])?$post_value['timer_background_color']:'';
            $timer_text_color = isset($post_value['timer_text_color'])?$post_value['timer_text_color']:'';
            $image_path = '';
            
            if ($redirect_activity == 'home') {
                $redirect_product_id = 0;
                $category_id = 0;
                $product_name = '';
            } else if ($redirect_activity == 'category') {
                $redirect_product_id = 0;
                $product_name = '';
            } else if ($redirect_activity == 'product') {
                $category_id = 0;
            }else{
                $redirect_product_id = 0;
                $category_id = 0;
                $product_name = '';
            }
            
            if(!$is_enabled_background_color){
                $timer_background_color = "";
            }

            $banner_model = $this->mab_bannersComponent;
            
            if(isset($post_value['id_banner']) && $post_value['id_banner']){
                $banner_model->load((int) $post_value['id_banner']);
            }
            
            $banner_model->setIdComponent($id_component);
            $banner_model->setIdBannerType($id_component_type);
            $banner_model->setCountdown($countdown);
            $banner_model->setProductId($redirect_product_id);
            $banner_model->setCategoryId($category_id);
            $banner_model->setRedirectActivity($redirect_activity);
            
            if(!empty($image_url)){
                $banner_model->setImageUrl($image_url);
            }
            
            $timer_background_color = isset($post_value['background_color'])?$post_value['background_color']:'';
            $height_of_banner = isset($post_value['height_of_banner'])?$post_value['height_of_banner']:'';
            $width_of_banner = isset($post_value['width_of_banner'])?$post_value['width_of_banner']:'';
            $margin_top = isset($post_value['margin_top'])?$post_value['margin_top']:'';
            $margin_bottom = isset($post_value['margin_bottom'])?$post_value['margin_bottom']:'';
            $margin_left = isset($post_value['margin_left'])?$post_value['margin_left']:'';
            $margin_right = isset($post_value['margin_right'])?$post_value['margin_right']:'';
            $banner_model->setHeight($height_of_banner);
            $banner_model->setWidth($width_of_banner);
            $banner_model->setTopMargin($margin_top);
            $banner_model->setBottomMargin($margin_bottom);
            $banner_model->setLeftMargin($margin_left);
            $banner_model->setRightMargin($margin_right);
            
            $banner_model->setImageType($image_type);
            $banner_model->setProductName($product_name);
            $banner_model->setImagePath($image_path);
            $banner_model->setImageContentMode($image_content_mode);
            $banner_model->setBackgroundColor($timer_background_color);
            $banner_model->setIsEnabledBackgroundColor($is_enabled_background_color);
            $banner_model->setTextColor($timer_text_color);
            $banner_model->setPosition($position);
            $banner_model->save();
//            print_r($banner_model->getData());
//            die;
            $banner_model->unsetData(); 
            
            $grid_html = $this->_viewLayoutFactory->create()->createBlock('Knowband\Mobileappbuilder\Block\Adminhtml\Layout\Grid\BannerSquare')->toHtml();
            
            $response['success'] = __('Data has been saved successfully.');
            $response['grid_html'] = $grid_html;            
            $elementData = $this->mab_componentHelper->getElementData()??"";
            $response['Added_Banners'] = $elementData['data']??[];
            $response['component_heading'] = $elementData['heading']??"";
        } catch (\Exception $ex) {
            $response['error'] = $ex->getMessage();
        }
        
        return json_encode($response);
    }
    
    /**
     * Function to save the product form data into DB
     * @param object $request
     */
    private function saveProductFormData($request) {
        try {
            $response = [];
            $post_value = $request->getParams();
            $files_value = $request->getFiles();
            $id_layout = (int) $post_value['id_layout'];
            $id_component = (int) $post_value['id_component'];
            $image_content_mode = $post_value['image_content_mode'];
            $category_id = isset($post_value['category_id']) ? $post_value['category_id'] : 0;
            $number_of_product = isset($post_value['number_of_product']) ? $post_value['number_of_product'] : 0;
            $product_list = $post_value['product_list'];
            $category_products = $post_value['category_products'];
            $product_type = isset($post_value['product_type']) ? $post_value['product_type'] : "";
            
            if($product_type == 'custom_products'){
                $category_products = '';
            } else if($product_type == 'category_products'){
                $product_list = '';
            } else {
                $category_products = '';
                $product_list = '';
            }
            
            $product_component_col = $this->mab_productComponent->getCollection()
                    ->addFieldToFilter("id_component", ['eq' => (int) $id_component]);
            if ($product_component_col->getSize()) {
                $product_component_model = $this->mab_productComponent->load((int) $id_component, 'id_component');
                $product_component_model->setIdComponent($id_component);
                $product_component_model->setProductType($product_type);
                $product_component_model->setCategoryProducts($category_products);
                $product_component_model->setCustomProducts($product_list);
                $product_component_model->setNumberOfProducts((int)$number_of_product);
                $product_component_model->setIdCategory((int)$category_id);
                $product_component_model->setImageContentMode($image_content_mode);
                $product_component_model->save();
                $product_component_model->unsetData();
            } else {
                $product_component_model = $this->mab_productComponent;
                $product_component_model->setIdComponent($id_component);
                $product_component_model->setProductType($product_type);
                $product_component_model->setCategoryProducts($category_products);
                $product_component_model->setCustomProducts($product_list);
                $product_component_model->setNumberOfProducts((int)$number_of_product);
                $product_component_model->setIdCategory((int)$category_id);
                $product_component_model->setImageContentMode($image_content_mode);
                $product_component_model->save();
                $product_component_model->unsetData();
            }
            unset($product_component_col);
            
            $response['success'] = __('Data has been saved successfully.');
            $elementData = $this->mab_componentHelper->getElementData()??"";
            $response['added_Products']['products_for_preview'] = $elementData['data']??[];
            $response['added_Products']['component_heading_preview'] = $elementData['heading']??"";
                        
        } catch (\Exception $ex) {
            $response['error'] =  $ex->getMessage();
        }
        return json_encode($response);
        
    }
    
    /**
     * Function to get product Name by id
     * @param int $id_product
     * @return string
     */
    private function getProductNameById($id_product){
        if($id_product){
            $model = $this->_product->load($id_product);
            $name = $model->getName();
            $model->unsetData();
            return $name;
        } else {
            return "";
        }
    }
    
    /**
     * Function to get the products by category
     * @param int $category_id
     * @param array $prev_cat_ids
     * @return string
     */
    private function getProductsByCategory($category_id = 0, $prev_cat_ids = []) {
        try {
            $cat_products_options = '';
            $products = $this->_categoryLoader->create()->load($category_id)
                    ->getProductCollection()
                    ->addAttributeToSelect('*');
            foreach($products as $product){
            //$_product = $this->_product->load($product->getId());
                // for data persistence
                if(in_array($product->getId(), $prev_cat_ids)){ 
                    $cat_products_options .= "<option value='".$product->getId()."' selected >".$product->getName()." (sku: ". $product->getSku().")" ."</option>";
                }else {
                    $cat_products_options .= "<option value='".$product->getId()."'>".$product->getName()." (sku: ". $product->getSku().")" ."</option>";
                }
            //$_product->unsetData();
            }
            
            return ['success' => true, 'category_product_options'=> $cat_products_options];
        } catch (\Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }
}
