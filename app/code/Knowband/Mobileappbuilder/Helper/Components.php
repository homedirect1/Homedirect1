<?php

/**
 * Knowband_Mobileappbuilder
 *
 * @category    Knowband
 * @package     Knowband_Mobileappbuilder
 * @author      Knowband Team <support@knowband.com.com>
 * @copyright   Knowband (http://wwww.knowband.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Knowband\Mobileappbuilder\Helper;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Catalog\Model\Product as ModelProduct;
class Components extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $mab_storeManager;
    protected $mab_scopeConfig;
    protected $mab_request;
    protected $mab_state;
    protected $inlineTranslation;
    protected $mab_transportBuilder;
    protected $rulesFactory;
    protected $mab_customerGroup;
    protected $mab_objectManager;
    
  

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\Website $website,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $bestSellerCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, 
        \Magento\Catalog\Model\ProductRepository $productRepo,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Knowband\Mobileappbuilder\Model\Layouts $mabLayoutsModel,
        \Knowband\Mobileappbuilder\Model\Topcategory $mabTopCategory,
        \Knowband\Mobileappbuilder\Model\Productdata $mabProductComponent,
        \Knowband\Mobileappbuilder\Model\Layoutcomponent $mabLayoutComponent,
        \Knowband\Mobileappbuilder\Model\Componenttypes $mabComponentTypes,
        \Knowband\Mobileappbuilder\Model\Banners $mabBanners,
        \Knowband\Mobileappbuilder\Helper\Data $mab_dataHelper
    )
    {
        $this->storeManager = $storeManager;
        $this->moduleManager = $context->getModuleManager();
        $this->mab_scopeConfig = $context->getScopeConfig();
        $this->mab_request = $context->getRequest();                
        $this->website = $website;
        $this->bestSellerCollection = $bestSellerCollectionFactory->create();
        $this->_priceHelper = $priceHelper;
        $this->localeDate = $localeDate;
        $this->sp_objectManager = $objectManager;
        $this->mab_layoutsModel = $mabLayoutsModel;
        $this->mab_topCategory = $mabTopCategory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productRepo = $productRepo;
        $this->mab_productComponent = $mabProductComponent;
        $this->mab_layoutComponent = $mabLayoutComponent;
        $this->mab_componentTypes = $mabComponentTypes;
        $this->mab_banners = $mabBanners;
        $this->mab_dataHelper = $mab_dataHelper;
        parent::__construct($context);
    }

    /**
     * Function to get all the categories of store
     * @return array
     */
    public function getCategories() {
        return $this->mab_dataHelper->getCategories();
    }

    /**
     * Function to get to category data
     * @return array
     */
    public function getTopCategoryData() {
        $id_component = $this->mab_request->getParam("id_component");
        $top_cat_model = $this->mab_topCategory->load($id_component, 'id_component');
        $categoryData = $top_cat_model->getData();
        $top_cat_model->unsetData();
        $module_dir = $this->getMediaUrl() . '/Knowband_Mobileappbuilder/';
        $result = [];
        if (!empty($categoryData)) {
            $id_cat_array = explode("|", $categoryData['id_category']);
            $image_url_array = explode("|", $categoryData['image_url']);
            foreach ($id_cat_array as $key => $value) {
                $result['category_id_' . ($key + 1)] = $value;
                $result['slideruploadedfile_' . ($key + 1)] = isset($image_url_array[$key]) ? $image_url_array[$key] : "";
                $result['image_src_' . ($key + 1)] = isset($image_url_array[$key]) ? $module_dir . $image_url_array[$key] : "";
            }
            $result['image_content_mode'] = $categoryData['image_content_mode'];
        }
        return $result;
    }


    /**
     * Function to get media base url
     * @return string
     */
    public function getMediaUrl() {
        return $this->mab_dataHelper->getMediaUrl();
    }

    /**
     * Function to get all products
     * @return array
     */
    public function getAllProducts() {
        $result = [];
        $prod_col = $this->mab_dataHelper->getProductCollection();
        foreach ($prod_col as $prod) {
            $result[] = ['value' => $prod->getId(), 'label' => $prod->getName() . " (sku: " . $prod->getSku() . ")"];
        }
        return $result;
    }

    /**
     * Function to get Product type
     * @return array
     */
    public function getProductTypes() {
        return [
            "best_seller" => __("Best Seller Products"),
//            "featured_products" => __("Featured Products"),
            "new_products" => __("New Products"),
            "special_products" => __("Special Products"),
            "category_products" => __("From a category"),
            "custom_products" => __("Custom Products")
        ];
    }

    /**
     * Function to get product form data
     * @return  array 
     */
    public function getProductFormData() {
        $result = [];
        try {
            $id_component = $this->mab_request->getParam("id_component");
            $prod_component_model = $this->mab_productComponent->load((int) $id_component, "id_component");
            if ($prod_component_model) {
                $result['product_type'] = $prod_component_model->getProductType();
                $result['category_products'] = $prod_component_model->getCategoryProducts();
                $result['product_list'] = explode(",", $prod_component_model->getCustomProducts());
                $result['image_content_mode'] = $prod_component_model->getImageContentMode();
                $result['number_of_products'] = $prod_component_model->getNumberOfProducts();
                $result['category_id'] = $prod_component_model->getIdCategory();

                $lc_model = $this->mab_layoutComponent->load((int) $id_component);
                $component_heading = $lc_model->getComponentHeading();
                $id_component_type = $lc_model->getIdComponentType();

                $component_type_model = $this->mab_componentTypes->load((int) $id_component_type);
                $result['component_name'] = $component_type_model->getComponentName();

                $lc_model->unsetData();
                $result['component_heading'] = $component_heading;
            }
            $prod_component_model->unsetData();
        } catch (\Exception $ex) {
            
        }
        return $result;
    }

    /**
     * Function to get Component Type
     * @return array
     */
    public function getComponentTypeData() {
        $type_data = [];
        try {
            $id_component = $this->mab_request->getParam("id_component", 0);
            $lc_model = $this->mab_layoutComponent->load((int) $id_component);
            $id_component_type = $lc_model->getIdComponentType();
            $lc_model->unsetData();

            $component_type_model = $this->mab_componentTypes->load((int) $id_component_type);
            $type_data = $component_type_model->getData();
            $component_type_model->unsetData();
        } catch (\Exception $ex) {
            
        }
        return $type_data;
    }

    /**
     * Function to get current store id
     * @return int
     */
    public function getStoreId() {
        $store_id = 0;
        if ($this->mab_request->getParam('store')) {
            $store_id = $this->storeManager->getStore($this->mab_request->getParam('store'))->getId();
            $scope = "stores";
        } elseif ($this->mab_request->getParam('website')) {
            $store_id = $this->website->load($this->mab_request->getParam('website'))->getDefaultGroup()
                    ->getDefaultStoreId();
            $scope = "websites";
        } else {
            $scope = "default";
            $store_id = 0;
        }
        return $store_id;
    }

    /**
     * FUnction to get layout name by layout id
     * @param int $id_layout
     * @return string
     */
    public function getLayoutNameById($id_layout = 0) {
        $layout_name = "";
        if ($id_layout) {
            try {
                $layout_model = $this->mab_layoutsModel->load((int) $id_layout);
                $layout_name = $layout_model->getLayoutName();
                $layout_model->unsetData();
            } catch (\Exception $ex) {
                
            }
        }
        return $layout_name;
    }

    public function formatPrice($price, $format = true, $includeContainer = false) {
        return $this->_priceHelper->currency($price, $format, $includeContainer);
    }

    /**
     * FUnction to get layout name by layout id
     * @param int $id_layout
     * @return string
     */
    public function getComponentHeading($id_component = 0) {
        $component_heading = "";
        if ($id_component) {
            try {
                $component_model = $this->mab_layoutComponent->load((int) $id_component);
                $component_heading = $component_model->getComponentHeading();
                $component_model->unsetData();
            } catch (\Exception $ex) {
                
            }
        }
        return $component_heading;
    }

    public function getElementData() {
        $response = [];
        
        try {
            $id_layout = $this->mab_request->getParam('id_layout');
            $id_component = $this->mab_request->getParam('id_component');
            $layout_component_col = $this->mab_layoutComponent->getCollection()
                    ->addFieldToFilter("id_layout", ['eq' => $id_layout])
                    ->addFieldToFilter("id_component", ['eq' => $id_component]);
            $layout_component_col->getSelect()
                    ->join(['ctypes' => $layout_component_col->getTable('kb_mobileapp_component_types')], 'main_table.id_component_type = ctypes.id')
                    ->order('position ASC');

            $components = $layout_component_col->getData();
            unset($layout_component_col);
            if (!empty($components)) {
                $i = 0;
                foreach ($components as $key => $comp) {
                    $component_type_id = $comp['id_component_type'];
                    $component_type = $comp['component_name'];
                    if ($component_type == 'top_category') {
                        $category_data = [];

                        $top_category_model = $this->mab_topCategory->load($comp['id_component'], 'id_component');
                        $categories = $top_category_model->getData();
                        $top_category_model->unsetData();
                        $response['element_type'] = 'categories_top';
                        $response['data'] = [];
                        if (is_array($categories) && !empty($categories)) {
                            $category_array = explode('|', $categories['id_category']);
                            $image_array = explode('|', $categories['image_url']);
                            foreach ($category_array as $k => $value) {
                                if ($value > 0) {
                                    $category_name = $component_type;
                                    $data = [];
                                    $data['id'] = $value;
                                    $data['category_id'] = $value;

                                    $module_dir = $this->mab_dataHelper->getMediaUrl() . '/Knowband_Mobileappbuilder/';
                                    if (isset($image_array[$k]) && !empty($image_array[$k])) {
                                        $data['image_src'] = $module_dir . $image_array[$k];
                                    } else {
                                        $category_model = $this->_category->load($value);
                                        $imageUrl = $category_model->getImageUrl();
                                        $category_model->unsetData();
                                        $data['image_src'] = $imageUrl;
                                    }
                                    $data['image_contentMode'] = $categories['image_content_mode'];
                                    $data['heading'] = '';
                                    $data['name'] = $category_name;
                                    $category_data[] = $data;
                                }
                            }
                            unset($data);
                            $response['data'] = $category_data;
                        }
                        $i++;
                    } elseif ($component_type == 'banner_square') {
                        $banner_heading = isset($components[$i]['component_heading']) ? $components[$i]['component_heading'] : "";
                        $square_banner_data = [];

                        $banners_col = $this->mab_banners->getCollection()
                                ->addFieldToFilter('id_component', ['eq' => (int) $comp['id_component']])
                                ->setOrder('position', 'ASC');
                        $banner_data = $banners_col->getData();
                        unset($banners_col);
                        $response['element_type'] = 'banners_square';
                        $response['heading'] = __($banner_heading);
                        if (is_array($banner_data) && !empty($banner_data)) {
                            foreach ($banner_data as $k => $bd) {
                                $data = [];
                                $data['click_target'] = $bd['redirect_activity'];
                                if ($bd['redirect_activity'] == 'category') {
                                    $data['target_id'] = $bd['category_id'];
                                } else {
                                    $data['target_id'] = $bd['product_id'];
                                }
                                $data['src'] = $bd['image_url'];
                                $data['Image'] = $bd['image_url'];
                                $data['kb_banner_id'] = $bd['id'];
                                $data['heading'] = '';
                                $data['image_contentMode'] = $bd['image_content_mode'];
                                $square_banner_data[] = $data;
                            }
                            unset($data);
                            $response['data'] = $square_banner_data;
                        }
                        $i++;
                    } elseif ($component_type == 'banner_custom') {
                        $banner_heading = isset($components[$i]['component_heading']) ? $components[$i]['component_heading'] : "";
                        $square_banner_data = [];

                        $banners_col = $this->mab_banners->getCollection()
                                ->addFieldToFilter('id_component', ['eq' => (int) $comp['id_component']])
                                ->setOrder('position', 'ASC');
                        $banner_data = $banners_col->getData();
                        unset($banners_col);
                        $response['element_type'] = 'banner_custom';
                        $response['heading'] = __($banner_heading);
                        if (is_array($banner_data) && !empty($banner_data)) {
                            foreach ($banner_data as $k => $bd) {
                                $data = [];
                                $data['click_target'] = $bd['redirect_activity'];
                                if ($bd['redirect_activity'] == 'category') {
                                    $data['target_id'] = $bd['category_id'];
                                } else if ($bd['redirect_activity'] == 'product') { 
                                    $data['target_id'] = $bd['product_id'];
                                }
                                $data['src'] = $bd['image_url'];
                                $data['Image'] = $bd['image_url'];
                                $data['Image'] = $bd['image_url'];
                                $data['Image'] = $bd['image_url'];
                                $data['kb_banner_id'] = $bd['id'];
                                $background_color = str_replace('#', '', $bd['background_color']);
                                $data['background_color'] = $bd['background_color'];
                                $data['banner_height'] = $bd['height'];
                                $data['banner_width'] = $bd['width'];
                                $data['insets']['top'] = $bd['top_margin'];
                                $data['insets']['bottom'] = $bd['bottom_margin'];
                                $data['insets']['left'] = $bd['right_margin'];
                                $data['insets']['right'] = $bd['left_margin'];
                                $data['heading'] = '';
                                $data['image_contentMode'] = $bd['image_content_mode'];
                                $square_banner_data[] = $data;
                            }
                            unset($data);
                            $response['data'] = $square_banner_data;
                        }
                        $i++;
                    } elseif ($component_type == 'banners_grid') {
                        $banner_heading = isset($components[$i]['component_heading']) ? $components[$i]['component_heading'] : "";
                        $banner_grid_data = [];

                        $banners_col = $this->mab_banners->getCollection()
                                ->addFieldToFilter('id_component', ['eq' => (int) $comp['id_component']])
                                ->setOrder('position', 'ASC');
                        $banner_data = $banners_col->getData();
                        unset($banners_col);
                        $response['element_type'] = 'banners_grid';
                        $response['heading'] = __($banner_heading);
                        $response['data'] = [];
                        if (is_array($banner_data) && !empty($banner_data)) {
                            foreach ($banner_data as $k => $bd) {
                                $data = [];
                                $data['click_target'] = $bd['redirect_activity'];
                                if ($bd['redirect_activity'] == 'category') {
                                    $data['target_id'] = $bd['category_id'];
                                } else {
                                    $data['target_id'] = $bd['product_id'];
                                }
                                $data['src'] = $bd['image_url'];
                                $data['title'] = '';
                                $data['Image'] = $bd['image_url'];
                                $data['kb_banner_id'] = $bd['id'];
                                $data['heading'] = '';
                                $data['image_contentMode'] = $bd['image_content_mode'];
                                $banner_grid_data[] = $data;
                            }
                            unset($data);
                            $response['data'] = $banner_grid_data;
                        }
                        $i++;
                    } elseif ($component_type == 'banners_countdown') {
                        $banner_heading = isset($components[$i]['component_heading']) ? $components[$i]['component_heading'] : "";
                        $banner_countdown_data = [];

                        $banners_col = $this->mab_banners->getCollection()
                                ->addFieldToFilter('id_component', ['eq' => (int) $comp['id_component']])
                                ->setOrder('position', 'ASC');
                        $banner_data = $banners_col->getData();
                        unset($banners_col);
                        $response['element_type'] = 'banners_countdown';
                        $response['heading'] = __($banner_heading);
                        $response['data'] = [];
                        if (is_array($banner_data) && !empty($banner_data)) {
                            foreach ($banner_data as $k => $bd) {
                                $data = [];
                                if (strtotime($bd['countdown']) - time() < 0) {
                                    continue;
                                }
                                $data['click_target'] = $bd['redirect_activity'];
                                if ($bd['redirect_activity'] == 'category') {
                                    $data['target_id'] = $bd['category_id'];
                                } else {
                                    $data['target_id'] = $bd['product_id'];
                                }
                                $data['src'] = $bd['image_url'];
                                $data['title'] = '';
                                $data['Image'] = $bd['image_url'];
                                $data['kb_banner_id'] = $bd['id'];
                                $data['heading'] = '';
                                $data['image_contentMode'] = $bd['image_content_mode'];
                                $data['upto_time'] = "" . strtotime($bd['countdown']) - time() . "";
                                $text_color = str_replace('#', '', $bd['text_color']);
                                $data['timer_text_color'] = $bd['text_color'];
                                $data['is_enabled_background_color'] = $bd['is_enabled_background_color'];

                                if (isset($bd['is_enabled_background_color']) && $bd['is_enabled_background_color'] == 1) {
                                    $background_color = str_replace('#', '', $bd['background_color']);
                                    $data['background_color'] = $bd['background_color'];
                                } else {
                                    $data['background_color'] = '';
                                }
                                $banner_countdown_data[] = $data;
                            }
                            unset($data);
                            $response['data'] = $banner_countdown_data;
                        }
                        $i++;
                    } elseif ($component_type == 'banner_horizontal_slider') {
                        $banner_heading = isset($components[$i]['component_heading']) ? $components[$i]['component_heading'] : "";
                        $banner_horizontal_data = [];

                        $banners_col = $this->mab_banners->getCollection()
                                ->addFieldToFilter('id_component', ['eq' => (int) $comp['id_component']])
                                ->setOrder('position', 'ASC');
                        $banner_data = $banners_col->getData();
                        unset($banners_col);
                        $response['element_type'] = 'banners_horizontal_sliding';
                        $response['heading'] = __($banner_heading);
                        $response['data'] = [];
                        if (is_array($banner_data) && !empty($banner_data)) {
                            foreach ($banner_data as $k => $bd) {
                                $data = [];
                                $data['click_target'] = $bd['redirect_activity'];
                                if ($bd['redirect_activity'] == 'category') {
                                    $data['target_id'] = $bd['category_id'];
                                } else {
                                    $data['target_id'] = $bd['product_id'];
                                }
                                $data['src'] = $bd['image_url'];
                                $data['title'] = '';
                                $data['Image'] = $bd['image_url'];
                                $data['kb_banner_id'] = $bd['id'];
                                $data['heading'] = '';
                                $data['image_contentMode'] = $bd['image_content_mode'];
                                $banner_horizontal_data[] = $data;
                            }
                            unset($data);
                            $response['data'] = $banner_horizontal_data;
                        }
                        $i++;
                    } elseif ($component_type == 'products_recent') {
                        $response['element_type'] = $component_type;
                        $response['heading'] = isset($components[$i]['component_heading']) ? __($components[$i]['component_heading']) : "";
                        $response['data'] =  $this->getNewProductsList(4, '');
                        $i++;
                    } elseif ($component_type == 'products_grid') {
                        $products = [];
                        $products = $this->getProductsComponentData($comp['id_component']);
                        if (!empty($products)) {
                            $response['element_type'] = $component_type;
                            $response['heading'] = isset($components[$i]['component_heading']) ? __($components[$i]['component_heading']) : "";
                            $response['data'] = $products;
                            unset($products);
                        }
                        $i++;
                    } elseif ($component_type == 'products_horizontal') {
                        $products = [];
                        $products = $this->getProductsComponentData($comp['id_component']);
                        if (!empty($products)) {
                            $response['element_type'] = $component_type;
                            $response['heading'] = isset($components[$i]['component_heading']) ? __($components[$i]['component_heading']) : "";
                            $response['data'] = $products;
                            unset($products);
                        }
                        $i++;
                    } elseif ($component_type == 'products_square') {
                        $products = [];
                        $products = $this->getProductsComponentData($comp['id_component']);
                        if (!empty($products)) {
                            $response['element_type'] = $component_type;
                            $response['heading'] = isset($components[$i]['component_heading']) ? __($components[$i]['component_heading']) : "";
                            $response['data'] = $products;
                            unset($products);
                        }
                        $i++;
                    }                     
                }
            }
        } catch (\Exception $ex) {
        }
        return $response;
    }

    /**
     * Function to get data of products component
     * @param int $id_component
     * @return array
     */
    public function getProductsComponentData($id_component) {
        $result = [];
        try {
            $product_component_model = $this->mab_productComponent->load((int) $id_component, "id_component");
            $product_data = $product_component_model->getData();
            $product_component_model->unsetData();
            if (!empty($product_data)) {
                $product_type = $product_data['product_type'];
                $number_of_products = (int) $product_data['number_of_products'];
                $image_content_mode = $product_data['image_content_mode'];
                $products = [];
                if ($product_type == 'best_seller') {
                    $products = $this->getBestSellerProducts($number_of_products, $image_content_mode);
                } elseif ($product_type == 'new_products') {
                    $products = $this->getNewProductsList($number_of_products, $image_content_mode);
                } elseif ($product_type == 'featured_products') {
//                    $products = $this->getFeaturedProducts($number_of_products, $image_content_mode);
                    $products = [];
                } elseif ($product_type == 'special_products') {
                    $products = $this->getSpecialProducts($number_of_products, $image_content_mode);
                } elseif ($product_type == 'category_products') {
                    $product_list = explode(',', $product_data['category_products']);
                    $products = $this->getCustomProducts($product_list, $number_of_products, $image_content_mode);
                } elseif ($product_type == 'custom_products') {
                    $product_list = explode(',', $product_data['custom_products']);
                    $products = $this->getCustomProducts($product_list, $number_of_products, $image_content_mode);
                }

                $result = array_slice($products, 0, $number_of_products);
            }
        } catch (\Exception $ex) {
            
        }
        return $result;
    }

    /**
     * Get Best seller products data
     * @param int $number_of_products
     * @param string $image_content_mode
     * @return array best seller product data
     */
    public function getBestSellerProducts($number_of_products, $image_content_mode) {
        $best_sellers = [];
        $limit = $number_of_products;
        $storeId = $this->mab_dataHelper->getStoreIdDetails();
        $products = $this->bestSellerCollection->setModel('Magento\Catalog\Model\Product')
                ->addStoreFilter($this->storeManager->getStore()->getId())
                ->setPageSize($limit);
        if ($products->count() > 0) {
            foreach ($products as $product) {
                $product_model = $this->_productRepo->getByid($product->getProductId());
                $price = '';
                //Price to be shown for grouped products
                if ($product_model->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
                    $products = $product_model->getTypeInstance()->getAssociatedProducts($product_model);
                    $minPrice = null;
                    foreach ($products as $item) {
                        $product_itm = clone $item;
                        $product_itm->setQty(\Magento\Framework\Pricing\PriceInfoInterface::PRODUCT_QUANTITY_DEFAULT);
                        $price = $product_itm->getPriceInfo()
                                ->getPrice('final_price')
                                ->getValue();
                        if (($price !== false) && ($price <= ($minPrice === null ? $price : $minPrice))) {
                            $minPrice = $price;
                        }
                    }
                    $price = __('Starting at ') . $this->formatPrice($minPrice);
                } else if ($product_model->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                    $min_price = $product_model->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
                    $max_price = $product_model->getPriceInfo()->getPrice('final_price')->getMaximalPrice()->getValue();
                    $price = __('From ') . $this->formatPrice($min_price) . __(" To ") . $this->formatPrice($max_price);
                } else {
                    $price = $this->formatPrice($product_model->getFinalPrice());
                }
                $best_sellers[] = [
                    'id' => $product_model->getId(),
                    'is_in_wishlist' => $this->mab_dataHelper->isInWishlist($product_model->getId()),
                    'name' => $product_model->getName(),
                    'price' => $price,
                    'available_for_order' => $product_model->isSaleable() ? "1" : "0",
                    "category_id" => "0",
                    "category_name" => "",
                    'show_price' => "1",
                    'image_contentMode' => $image_content_mode,
                    'new_products' => $this->isProductNew($product_model) ? "1" : "0 ",
                    'on_sale_products' => ($product_model->getPrice() > $product_model->getFinalPrice()) ? "1" : "0",
                    'discount_price' => ($product_model->getPrice() > $product_model->getFinalPrice()) ? $this->formatPrice($product_model->getPrice()) : 0,
                    "discount_percentage" => ($product_model->getFinalPrice() < $product_model->getPrice()) ? (string) floor(abs(($product_model->getFinalPrice() / ($product_model->getPrice())) * 100 - 100)) : "0",
                    "src" => $this->getImageUrl($product_model->getSmallImage()),
                    "ClickActivityName" => "ProductActivity"
                ];
            }
        }
        return $best_sellers;
    }

    /**
     * Get new products data
     * 
     * @param int $number_of_products
     * @param string $image_content_mode
     * @return array new products data
     */
    public function getNewProductsList($number_of_products, $image_content_mode) {
        $new_arrivals = [];
        $limit = $number_of_products;
        $storeId = $this->mab_dataHelper->getStoreIdDetails();
        $products = $this->_productCollectionFactory->create()
                ->addAttributeToSelect("*") // select all attributes
                ->setStoreId($storeId)
                ->addStoreFilter($storeId)
                ->addAttributeToFilter('visibility', ['eq' => 4])
                ->addAttributeToSort("entity_id", "DESC") // sorting
                ->addAttributeToFilter("status", ["eq" => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED])
                ->setPageSize($limit);
        if ($products->count() > 0) {
            foreach ($products as $product) {
                $product_model = $this->_productRepo->getByid($product->getId());
                $price = '';
                //Price to be shown for grouped products
                if ($product_model->getTypeId() == "grouped") {
                    $products = $product_model->getTypeInstance()->getAssociatedProducts($product_model);
                    $minPrice = null;
                    foreach ($products as $item) {
                        $product_itm = clone $item;
                        $product_itm->setQty(\Magento\Framework\Pricing\PriceInfoInterface::PRODUCT_QUANTITY_DEFAULT);
                        $price = $product_itm->getPriceInfo()
                                ->getPrice('final_price')
                                ->getValue();
                        if (($price !== false) && ($price <= ($minPrice === null ? $price : $minPrice))) {
                            $minPrice = $price;
                        }
                    }
                    $price = __('Starting at ') . $this->formatPrice($minPrice);
                } else if ($product_model->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                    $min_price = $product_model->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
                    $max_price = $product_model->getPriceInfo()->getPrice('final_price')->getMaximalPrice()->getValue();
                    $price = __('From ') . $this->formatPrice($min_price) . __(" To ") . $this->formatPrice($max_price);
                } else {

                    $product_price = $product_model->getPrice() ? $product_model->getPrice() : $product_model->getFinalPrice();
                    $formatted_price = $this->formatPrice($product_price, true, false);

                    $special_price = !is_null($product_model->getFinalPrice()) ? $product_model->getFinalPrice() : 0;
                }
                $new_arrivals[] = [
                    'id' => $product_model->getId(),
                    'is_in_wishlist' => $this->mab_dataHelper->isInWishlist($product_model->getId()),
                    'name' => $product_model->getName(),
                    "price" => $formatted_price,
                    'available_for_order' => $product_model->isSaleable() ? "1" : "0",
                    "category_id" => "0",
                    "category_name" => "",
                    'show_price' => "1",
                    'image_contentMode' => $image_content_mode,
                    'new_products' => $this->isProductNew($product_model) ? "1" : "0 ",
                    'on_sale_products' => ($product_model->getPrice() > $product_model->getFinalPrice()) ? "1" : "0",
                    "discount_price" => $this->formatPrice($special_price),
                    "discount_percentage" => ($product_model->getFinalPrice() < $product_model->getPrice()) ? (string) floor(abs(($product_model->getFinalPrice() / ($product_model->getPrice())) * 100 - 100)) : "0",
                    "src" => $this->getImageUrl($product_model->getSmallImage()),
                    "ClickActivityName" => "ProductActivity"
                ];
            }
        }
        return $new_arrivals;
    }

    /**
     * Get special product data
     * 
     * @param int $number_of_products
     * @param string $image_content_mode
     * @return array special product data
     */
    public function getSpecialProducts($number_of_products, $image_content_mode) {
        $special_products = [];
        $limit = $number_of_products;

        $products = $this->_productCollectionFactory->create()
//                    ->addAttributeToFilter('type_id', 'configurable')
                ->addAttributeToFilter('status', ['eq' => 1])
                ->addAttributeToFilter('visibility', ['eq' => 4])
                ->setPageSize($limit);
//            $products->joinField('is_in_stock', 'cataloginventory/stock_item', 'is_in_stock', 'product_id=entity_id', 'is_in_stock=1', '{{table}}.stock_id=1', 'left');

        $now = $this->mab_dataHelper->getDate();
        $products->addAttributeToFilter('special_to_date', ['or' => [
                0 => ['date' => true, 'from' => $now],
            ]
                ], 'left');

        $products->addAttributeToFilter('special_from_date', [
            'or' => [
                0 => ['date' => true, 'to' => $now],
                1 => ['is' => new \Zend_Db_Expr('null')]
            ]
                ], 'left');

        if ($products->count() > 0) {
            foreach ($products as $product) {
                $product_model = $this->_productRepo->getByid($product->getId());
                if ($product->getFinalPrice() < $product->getPrice()) {
                    $saleof = abs(($product->getFinalPrice() / ($product->getPrice())) * 100 - 100);
                }
                $price = '';
                //Price to be shown for grouped products
                if ($product_model->getTypeId() == "grouped") {
                    $products = $product_model->getTypeInstance()->getAssociatedProducts($product_model);
                    $minPrice = null;
                    foreach ($products as $item) {
                        $product_itm = clone $item;
                        $product_itm->setQty(\Magento\Framework\Pricing\PriceInfoInterface::PRODUCT_QUANTITY_DEFAULT);
                        $price = $product_itm->getPriceInfo()
                                ->getPrice('final_price')
                                ->getValue();
                        if (($price !== false) && ($price <= ($minPrice === null ? $price : $minPrice))) {
                            $minPrice = $price;
                        }
                    }
                    $price = __('Starting at ') . $this->formatPrice($minPrice);
                } else if ($product_model->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                    $min_price = $product_model->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
                    $max_price = $product_model->getPriceInfo()->getPrice('final_price')->getMaximalPrice()->getValue();
                    $price = __('From ') . $this->formatPrice($min_price) . __(" To ") . $this->formatPrice($max_price);
                } else {
                    $product_price = $product_model->getPrice() ? $product_model->getPrice() : $product_model->getFinalPrice();
                    $price = $this->formatPrice($product_price);
                }

                $special_price = !is_null($product_model->getFinalPrice()) ? $product_model->getFinalPrice() : "0";
                $special_products[] = [
                    'id' => $product_model->getId(),
                    'is_in_wishlist' => $this->mab_dataHelper->isInWishlist($product_model->getId()),
                    'name' => $product_model->getName(),
                    'price' => $price,
                    'available_for_order' => $product_model->isSaleable() ? "1" : "0",
                    "category_id" => "0",
                    "category_name" => "",
                    'show_price' => "1",
                    'image_contentMode' => $image_content_mode,
                    'new_products' => $this->isProductNew($product_model) ? "1" : "0 ",
                    'on_sale_products' => ($product_model->getPrice() > $product_model->getFinalPrice()) ? "1" : "0",
                    'discount_price' => $this->formatPrice($special_price),
                    "discount_percentage" => ($product_model->getFinalPrice() < $product_model->getPrice()) ? floor(abs(($product_model->getFinalPrice() / ($product_model->getPrice())) * 100 - 100)) : "",
                    "src" => $this->getImageUrl($product_model->getSmallImage()),
                    "ClickActivityName" => "ProductActivity"
                ];
            }
        }
        return $special_products;
    }

    /**
     * Get new products data
     * 
     * @param array $product_list
     * @param int $number_of_products
     * @param string $image_content_mode
     * @return array new products data
     */
    public function getCustomProducts($product_list, $number_of_products, $image_content_mode) {
        $custom_products = [];
        $limit = $number_of_products;
        $storeId = $this->mab_dataHelper->getStoreIdDetails();
        if (!empty($product_list)) {
            $products = $this->_productCollectionFactory->create()
                    ->addAttributeToSelect("*") // select all attributes
                    ->setStoreId($storeId)
                    ->addStoreFilter($storeId)
                    ->addAttributeToFilter('visibility', ['eq' => 4])
                    ->addFieldToFilter('entity_id', ['in' => $product_list])
//                ->addAttributeToSort("entity_id", "DESC") // sorting
                    ->addAttributeToFilter("status", ["eq" => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED])
                    ->setPageSize($limit);
            if ($products->count() > 0) {
                foreach ($products as $product) {
                    $product_model = $this->_productRepo->getByid($product->getId());
                    $price = '';
                    //Price to be shown for grouped products
                    if ($product_model->getTypeId() == "grouped") {
                        $products = $product_model->getTypeInstance()->getAssociatedProducts($product_model);
                        $minPrice = null;
                        foreach ($products as $item) {
                            $product_itm = clone $item;
                            $product_itm->setQty(\Magento\Framework\Pricing\PriceInfoInterface::PRODUCT_QUANTITY_DEFAULT);
                            $price = $product_itm->getPriceInfo()
                                    ->getPrice('final_price')
                                    ->getValue();
                            if (($price !== false) && ($price <= ($minPrice === null ? $price : $minPrice))) {
                                $minPrice = $price;
                            }
                        }
                        $price = __('Starting at ') . $this->formatPrice($minPrice);
                    } else if ($product_model->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                        $min_price = $product_model->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
                        $max_price = $product_model->getPriceInfo()->getPrice('final_price')->getMaximalPrice()->getValue();
                        $price = __('From ') . $this->formatPrice($min_price) . __(" To ") . $this->formatPrice($max_price);
                    } else {
                        $product_price = $product_model->getPrice() ? $product_model->getPrice() : $product_model->getFinalPrice();
                        $formatted_price = $this->formatPrice($product_price, true, false);
                        $special_price = !is_null($product_model->getFinalPrice()) ? $product_model->getFinalPrice() : 0;
                    }
                    $custom_products[] = [
                        'id' => $product_model->getId(),
                        'is_in_wishlist' => $this->mab_dataHelper->isInWishlist($product_model->getId()),
                        'name' => $product_model->getName(),
                        "price" => $formatted_price,
                        'available_for_order' => $product_model->isSaleable() ? "1" : "0",
                        "category_id" => "0",
                        "category_name" => "",
                        'show_price' => "1",
                        'image_contentMode' => $image_content_mode,
                        'new_products' => $this->isProductNew($product_model) ? "1" : "0 ",
                        'on_sale_products' => ($product_model->getPrice() > $product_model->getFinalPrice()) ? "1" : "0",
                        "discount_price" => $this->formatPrice($special_price),
                        "discount_percentage" => ($product_model->getFinalPrice() < $product_model->getPrice()) ? (string) floor(abs(($product_model->getFinalPrice() / ($product_model->getPrice())) * 100 - 100)) : "0",
                        "src" => $this->getImageUrl($product_model->getSmallImage()),
                        "ClickActivityName" => "ProductActivity"
                    ];
                }
            }
        }
        return $custom_products;
    }

    protected function isProductNew(ModelProduct $product) {
        $newsFromDate = $product->getNewsFromDate();
        $newsToDate = $product->getNewsToDate();
        if (!$newsFromDate && !$newsToDate) {
            return false;
        }
        return $this->localeDate->isScopeDateInInterval(
                        $product->getStore(), $newsFromDate, $newsToDate
        );
    }

    public function getImageUrl($product_image) {
        return $this->mab_dataHelper->getMediaUrl() . $this->sp_objectManager->get('\Magento\Catalog\Model\Product\Media\Config')->getMediaPath($product_image);
    }

}
