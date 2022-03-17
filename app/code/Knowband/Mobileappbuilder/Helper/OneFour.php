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

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Catalog\Model\Product as ModelProduct;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Store\Model\Store;
use Magento\Customer\Model\Metadata\Form;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Exception\NoSuchEntityException;

class OneFour extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    protected $sp_storeManager;
    protected $sp_scopeConfig;
    protected $sp_request;
    protected $sp_state;
    protected $inlineTranslation;
    protected $sp_transportBuilder;
    protected $rulesFactory;
    protected $sp_customerGroup;
    protected $sp_objectManager;
    protected $pushNotificationModel;   
    protected $bannerModel;   
    protected $fcmModel;   
    protected $orderStatusModel;   
    protected $paymentModel; 

    const TRANSLATION_RECORD_FILE = 'Vss_Mobileappbuilder_Record.csv';
    const ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS = 0;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $state,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configResource,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Locale\ResolverInterface $store, 
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, 
        \Knowband\Mobileappbuilder\Model\PushNotification $pushNotificationModel, 
        \Knowband\Mobileappbuilder\Model\Banner $bannerModel, 
        \Knowband\Mobileappbuilder\Model\Fcm $fcmModel, 
        \Knowband\Mobileappbuilder\Model\OrderStatus $orderStatusModel, 
        \Knowband\Mobileappbuilder\Model\Payment $paymentModel, 
        \Knowband\Mobileappbuilder\Helper\Data $dataHelper,
        \Magento\Catalog\Model\ProductRepository $productRepo,
        TimezoneInterface $localeDate,
        StoreCookieManagerInterface $storeCookieManager,
        HttpContext $httpContext,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $bestSellerCollectionFactory,
        \Magento\Directory\Model\Currency $currencyModel,
        \Magento\Framework\Locale\CurrencyInterface $localeFormat,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Api\AccountManagementInterface $AccountManagementInterface,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Magento\Downloadable\Model\Link $link,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Quote\Model\ResourceModel\Quote\Address\Rate\CollectionFactory $rateCollectionFactory,
        \Magento\Catalog\Helper\Image $productImageHelper,
        \Magento\Catalog\Helper\Product\Configuration $productConfiguration,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\Escaper $escaper
    )
    {
        $this->sp_storeManager = $storeManager;
        $this->sp_scopeConfig = $context->getScopeConfig();
        $this->sp_request = $context->getRequest();
        $this->sp_response = $response;
        $this->_session = $sessionManager;
        $this->sp_resource = $configResource;
        $this->sp_state = $state;
        $this->rulesFactory = $ruleFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->sp_transportBuilder = $transportBuilder;
        $this->sp_customerGroup = $customerGroup;   
        $this->sp_objectManager = $objectManager;
        $this->_blockFactory = $blockFactory;
        $this->_priceHelper = $priceHelper;
        $this->date = $date;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->assetRepo = $assetRepo;
        $this->_resource = $resource;
        $this->pageFactory = $pageFactory;   
        $this->pushNotificationModel = $pushNotificationModel;   
        $this->bannerModel = $bannerModel;   
        $this->fcmModel = $fcmModel;   
        $this->orderStatusModel = $orderStatusModel;   
        $this->paymentModel = $paymentModel;   
        $this->dataHelper = $dataHelper;
        $this->_productRepo = $productRepo;
        $this->bestSellerCollection = $bestSellerCollectionFactory->create();
        $this->localeDate = $localeDate;
        $this->_store = $store;
        $this->currenciesModel = $currencyModel;
        $this->_localeFormat = $localeFormat;
        $this->storeRepository = $storeRepository;
        $this->httpContext = $httpContext;
        $this->storeCookieManager = $storeCookieManager;
        $this->customerSession = $customerSession;
        $this->wishlistFactory = $wishlistFactory;
        $this->customerAccountManagement = $AccountManagementInterface;
        $this->quote = $checkoutSession;
        $this->customerFactory  = $customerFactory;
        $this->customer = $customer;
        $this->_eavAttribute = $eavAttribute;
        $this->_catalogProduct = $catalogProduct;
        $this->_eventManager = $context->getEventManager();
        $this->_linkModel = $link;
        $this->priceCurrency =  $priceCurrency;
        $this->escaper = $escaper;
        $this->_rateCollectionFactory = $rateCollectionFactory;
        $this->_productImageHelper = $productImageHelper;
        $this->productConfiguration = $productConfiguration;
        $this->_countryCollectionFactory = $countryCollectionFactory;
        $this->addressRepository = $addressRepository;
        $this->localeLists = $localeLists;
        $this->_formFactory = $formFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_orderConfig = $orderConfig;
        parent::__construct($context);
    }
    
    public function getSid() {
        return $this->_session->getSessionId();
    }
    
    /*
     * Home Page 
     * 
     *      
     */

    public function appGetHome()
    {
        $response = array();
        try {
            $response = $this->getHomePage();
            $response["SID"] = $this->getSid();
            $response["message"] = "";
            $response["status"] = "success";
            $response["version"] = $this->dataHelper->getVersion();
            $response["install_module"] = '';
            $this->dataHelper->logresponse($this->sp_request, 'appGetHome', $response);
            return $response;
        } catch (Exception $ex) {
            $response["message"] = $ex->getMessage();
            $response["status"] = "failure";
            $response["SID"] = $this->getSid();
            $response["version"] = $this->dataHelper->getVersion();
            $response["install_module"] = '';
            $this->dataHelper->logresponse($this->sp_request, 'appGetHome', $response);
            return $response;
        }
    }
    
    /* BOC HOME Helper Functions */

    public function getHomePage($json = false)
    {

        $menu_settings = $this->dataHelper->getSettings('menu_settings');

        $homepage = array(
            "topslider" => $this->getSliders(),
            "topbanner" => $this->getBanners(),
            "fproducts" => array("title" => "", 'products' => array()),
            "sproducts" => $this->getSpecialProducts(),
            "bsproducts" => $this->getBestSeller(),
            "newproducts" => $this->getNewArrivals(),
            "wishlist_active" => $this->checkWishlist(),
            "cms_links" => $this->getMenuCmsLinks(),
            "contact_us_available" => (isset($menu_settings["contact_us_enabled"]) && $menu_settings["contact_us_enabled"]) ? "1" : "0",
            "contact_us_link" => (isset($menu_settings["contact_us_page_value"]) && $menu_settings["contact_us_page_value"]) && (isset($menu_settings["contact_us_enabled"]) && $menu_settings["contact_us_enabled"]) ? $this->sp_objectManager->create('Magento\Cms\Helper\Page')->getPageUrl($menu_settings["contact_us_page_value"]) . (parse_url($this->sp_objectManager->create('Magento\Cms\Helper\Page')->getPageUrl($menu_settings["contact_us_page_value"]), PHP_URL_QUERY) ? '&' : '?') . 'mobileappbuilder_webview=1' : "",
            "Menu_Categories" => $this->getMenuCategories(),
            "languages_record" => $this->dataHelper->returnLanguageRecordAsArray(),
            "languages" => $this->getLanguages(),
            "currencies" => $this->getCurrencies(),
            "is_marketplace" => ($this->dataHelper->isMarketplaceEnabled()) ? '1' : '0'
        );
        if ($json) {
            return json_encode($homepage);
        } else {
            return ($homepage);
        }
    }
    
    public function getSliders()
    {
        $sliders = array();
        $sliders["title"] = __('Top Sliders');
        $sliders["slides"] = array();
        $slider_text = __('Slider');

        $slider_collection = $this->bannerModel->getCollection();
        $where = ' main_table.type = "slider" AND main_table.status = 1';
        $slider_collection->getSelect()->where($where);
        if ($results = $slider_collection->getData()) {
            if (!empty($results)) {
                foreach ($results as $res) {
                    if ($res['redirect_activity'] == 'product') {
                        $target_id = $res['product_id'];
                    } else if ($res['redirect_activity'] == 'category') {
                        $target_id = $res['category_id'];
                    } else {
                        $target_id = '';
                    }
                    $sliders["slides"][] = array(
                        'click_target' => $res['redirect_activity'],
                        'target_id' => $target_id,
                        'title' => $slider_text . $res['kb_banner_id'],
                        'src' => $res['image_url']
                    );
                }
            }
        }

        return $sliders;
    }

    public function getBanners()
    {
        $banners = array();
        $banners["title"] = __('Top Banners');
        $banners["banners"] = array();

        $banner_text = __('Banner');

        $banner_collection = $this->bannerModel->getCollection();
        $where = ' main_table.type = "banner" AND main_table.status = 1';
        $banner_collection->getSelect()->where($where);
        if ($results = $banner_collection->getData()) {
            if (!empty($results)) {
                foreach ($results as $res) {

                    if ($res['redirect_activity'] == 'product') {
                        $target_id = $res['product_id'];
                    } else if ($res['redirect_activity'] == 'category') {
                        $target_id = $res['category_id'];
                    } else {
                        $target_id = '';
                    }
                    $banners["banners"][] = array(
                        'click_target' => $res['redirect_activity'],
                        'target_id' => $target_id,
                        'title' => $banner_text . $res['kb_banner_id'],
                        'src' => $res['image_url']
                    );
                }
            }
        }

        return $banners;
    }

    public function getSpecialProducts()
    {
        $list_status = $this->getProductListStatus("special");
        $special_products = array();
        $special_products["title"] = "";
        $special_products["products"] = array();
        if ($list_status) {
            $limit = $this->getProductListLimit();
            
            $products = $this->_productCollectionFactory->create()
//                    ->addAttributeToFilter('type_id', 'configurable')
                    ->addAttributeToFilter('status', array('eq' => 1))
                    ->addAttributeToFilter('visibility', array('eq' => 4))
                    ->setPageSize($limit);
//            $products->joinField('is_in_stock', 'cataloginventory/stock_item', 'is_in_stock', 'product_id=entity_id', 'is_in_stock=1', '{{table}}.stock_id=1', 'left');

            $now = $this->dataHelper->getDate();
            $products->addAttributeToFilter('special_to_date', array('or' => array(
                    0 => array('date' => true, 'from' => $now),
                )
                    ), 'left');

            $products->addAttributeToFilter('special_from_date', array('or' => array(
                    0 => array('date' => true, 'to' => $now),
                    1 => array('is' => new \Zend_Db_Expr('null'))
                )
                    ), 'left');
            if ($products->count() > 0) {
                $special_products["title"] = __("Special Products");
                foreach ($products as $product) {
                    $product_model = $this->_productRepo->getByid($product->getId());
                    if ($product->getFinalPrice() < $product->getPrice()) {
                        $saleof = abs(($product->getFinalPrice() / ($product->getPrice())) * 100 - 100);
                    }
                    $special_products["products"][] = array(
                        'id' => $product_model->getId(),
                        'is_in_wishlist' => $this->dataHelper->isInWishlist($product_model->getId()),
                        'name' => $product_model->getName(),
                        'price' => $this->formatPrice($product_model->getFinalPrice()),
                        'available_for_order' => $product_model->isSaleable() ? "1" : "0",
                        "category_id" => "0",
                        "category_name" => "",
                        'show_price' => "1",
                        'new_products' => $this->isProductNew($product_model) ? "1" : "0 ",
                        'on_sale_products' => ($product_model->getPrice() > $product_model->getFinalPrice()) ? "1" : "0",
                        'discount_price' => ($product_model->getPrice() > $product_model->getFinalPrice()) ? $this->formatPrice($product_model->getPrice()) : 0,
                        "discount_percentage" => ($product_model->getFinalPrice() < $product_model->getPrice()) ? floor(abs(($product_model->getFinalPrice() / ($product_model->getPrice())) * 100 - 100)) : "",
                        "src" => $this->getImageUrl($product_model->getSmallImage()),
                        "ClickActivityName" => "ProductActivity"
                    );
                }
            }
        }
        return $special_products;
    }

    public function getNewArrivals()
    {
        $list_status = $this->getProductListStatus("new_arrival");
        $new_arrivals = array();
        $new_arrivals["title"] = "";
        $new_arrivals["products"] = array();
        if ($list_status) {
            $limit = $this->getProductListLimit();
            $storeId = $this->dataHelper->getStoreIdDetails();
            $products = $this->_productCollectionFactory->create()
                    ->addAttributeToSelect("*") // select all attributes
                    ->setStoreId($storeId)
                    ->addStoreFilter($storeId)
                    ->addAttributeToFilter('visibility', array('eq' => 4))
                    ->addAttributeToSort("entity_id", "DESC") // sorting
                    ->addAttributeToFilter("status", array("eq" => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED))
                    ->setPageSize($limit);
//            $products->joinField('is_in_stock', 'cataloginventory/stock_item', 'is_in_stock', 'product_id=entity_id', 'is_in_stock=1', '{{table}}.stock_id=1', 'left');
            if ($products->count() > 0) {
                $new_arrivals["title"] = __("New Arrivals");
                foreach ($products as $product) {
                    $product_model = $this->_productRepo->getByid($product->getId());
                    $new_arrivals["products"][] = array(
                        'id' => $product_model->getId(),
                        'is_in_wishlist' => $this->dataHelper->isInWishlist($product_model->getId()),
                        'name' => $product_model->getName(),
                        'price' => $this->formatPrice($product_model->getFinalPrice()),
                        'available_for_order' => $product_model->isSaleable() ? "1" : "0",
                        "category_id" => "0",
                        "category_name" => "",
                        'show_price' => "1",
                        'new_products' => $this->isProductNew($product_model) ? "1" : "0 ",
                        'on_sale_products' => ($product_model->getPrice() > $product_model->getFinalPrice()) ? "1" : "0",
                        'discount_price' => ($product_model->getPrice() > $product_model->getFinalPrice()) ? $this->formatPrice($product_model->getPrice()) : 0,
                        "discount_percentage" => ($product_model->getFinalPrice() < $product_model->getPrice()) ? floor(abs(($product_model->getFinalPrice() / ($product_model->getPrice())) * 100 - 100)) : "",
                        "src" => $this->getImageUrl($product_model->getSmallImage()),
                        "ClickActivityName" => "ProductActivity"
                    );
                }
            }
        }
        return $new_arrivals;
    }

    protected function getProductListLimit()
    {
        $general_settings = $this->dataHelper->getSettings('general_settings');;
        $limit = 10;
        if (isset($general_settings['home_product_list_count']) && $general_settings['home_product_list_count'] > 0) {
            $limit = $general_settings['home_product_list_count'];
        }
        return $limit;
    }

    protected function getProductListStatus($type)
    {
        $general_settings = $this->dataHelper->getSettings('general_settings');
        $status = false;
        if (isset($general_settings['home_product_list']) && count($general_settings['home_product_list']) > 0 && !empty($general_settings['home_product_list'])) {
            $product_list = $general_settings['home_product_list'];
            if (in_array($type, $product_list)) {
                $status = true;
            }
        }
        return $status;
    }


    public function getBestSeller()
    {
        $list_status = $this->getProductListStatus("best_seller");
        $best_sellers = array();
        $best_sellers["title"] = "";
        $best_sellers["products"] = array();
        if ($list_status) {
            $limit = $this->getProductListLimit();
            $storeId = $this->dataHelper->getStoreIdDetails();
            $products = $this->bestSellerCollection->setModel('Magento\Catalog\Model\Product')
            ->addStoreFilter($this->sp_storeManager->getStore()->getId())
                    ->setPageSize($limit); 
            if ($products->count() > 0) {
                $best_sellers["title"] = __("Best Sellers");
                foreach ($products as $product) {
                    $product_model = $this->_productRepo->getByid($product->getProductId());
                    $best_sellers["products"][] = array(
                        'id' => $product_model->getId(),
                        'is_in_wishlist' => $this->dataHelper->isInWishlist($product_model->getId()),
                        'name' => $product_model->getName(),
                        'price' => $this->formatPrice($product_model->getFinalPrice()),
                        'available_for_order' => $product_model->isSaleable() ? "1" : "0",
                        "category_id" => "0",
                        "category_name" => "",
                        'show_price' => "1",
                        'new_products' => $this->isProductNew($product_model) ? "1" : "0 ",
                        'on_sale_products' => ($product_model->getPrice() > $product_model->getFinalPrice()) ? "1" : "0",
                        'discount_price' => ($product_model->getPrice() > $product_model->getFinalPrice()) ? $this->formatPrice($product_model->getPrice()) : 0,
                        "discount_percentage" => ($product_model->getFinalPrice() < $product_model->getPrice()) ? floor(abs(($product_model->getFinalPrice() / ($product_model->getPrice())) * 100 - 100)) : "",
                        "src" => $this->getImageUrl($product_model->getSmallImage()),
                        "ClickActivityName" => "ProductActivity"
                    );
                }
            }
        }
        return $best_sellers;
    }

    public function getMenuCategories()
    {
        $categoryFactory = $this->sp_objectManager->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
        $_categories = $categoryFactory->create()                              
            ->addAttributeToSelect('*')
                ->addFieldToFilter('level', ['eq' => 2])
                ->addIsActiveFilter()
            ->setStore($this->sp_storeManager->getStore()); 
		/** Defined by Ashish on 30th Oct 2018. It was giving warning if subcategory is not available */	
		$subcategory_array = array();
		$sub_subcategory_array = array();		
		/** End of Defined by Ashish on 30th Oct 2018 */	
        if (count($_categories) > 0) {
            foreach ($_categories as $_category) {
                $_subcategories = $_category->getChildrenCategories();
                if (count($_subcategories) > 0) {
                    $subcategory_array = array();
                    foreach ($_subcategories as $_subcategory) {
                        $_sub_subcategories = $_subcategory->getChildrenCategories();
                        $sub_subcategory_array = array();
                        if (count($_sub_subcategories) > 0) {
                            foreach ($_sub_subcategories as $_sub_subcategory) {
                                $sub_subcategory_array[] = array('id' => $_sub_subcategory->getId(), 'name' => $_sub_subcategory->getName());
                            }
                        }
                        $subcategory_array[] = array('id' => $_subcategory->getId(), 'name' => $_subcategory->getName(), 'third_children' => $sub_subcategory_array);
                    }
                }
                $category_array[] = array('id' => $_category->getId(), 'name' => $_category->getName(), 'second_children' => $subcategory_array);
            }
        }
        return ($category_array);
    }

    public function getMenuCmsLinks()
    {
        $menu_settings = $this->dataHelper->getSettings('menu_settings');
        $menus = array();
        if (!empty($menu_settings)) {
            for ($i = 1; $i <= 4; $i++) {
                if (isset($menu_settings["menu" . $i . "_enabled"]) && $menu_settings["menu" . $i . "_enabled"]) {
                    $link = $this->sp_objectManager->create('Magento\Cms\Helper\Page')->getPageUrl($menu_settings["menu" . $i . "_page_value"]);
                    $menus[] = array(
                        'link' => $link . (parse_url($link, PHP_URL_QUERY) ? '&' : '?') . 'mobileappbuilder_webview=1',
                        'name' => $menu_settings["menu" . $i . "_title"],
                    );
                }
            }
        }
        return $menus;
    }
    
    protected function isProductNew(ModelProduct $product)
    {
        $newsFromDate = $product->getNewsFromDate();
        $newsToDate = $product->getNewsToDate();
        if (!$newsFromDate && !$newsToDate) {
            return false;
        }
        return $this->localeDate->isScopeDateInInterval(
            $product->getStore(),
            $newsFromDate,
            $newsToDate
        );
    }

    public function getLanguages()
    {
        
        $languages = array();
        $stores = $this->sp_storeManager->getStores($withDefault = false);
        //Try to get list of locale for all stores;
        foreach ($stores as $store) {
            $languages['lang_list'][] = array(
                'name' => $store->getName(),
                'id_lang' => $store->getId(),
                'active' => '1',
                'language_code' => $store->getCode(),
                'iso_code' => substr($this->sp_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId()), 0, 2),
                "date_format_lite" => "m/d/Y",
                "date_format_full" => "m/d/Y H:i:s",
                "is_rtl" => "0",
                "id_shop" => $store->getCode(),
                "shops" => array(),
            );
        }
        $languages['default_lang'] = substr($this->_store->getLocale(), 0, 2);
        $languages["default_lang_id"] = $this->sp_storeManager->getWebsite()->getDefaultGroup()->getDefaultStoreId();
        $languages["default_id_shop"] = $this->sp_storeManager->getDefaultStoreView()->getCode();
        
        return $languages;
    }

    /*
     * Get Available currencies on store
     * Added by gpal 14 Sep 2017
     */

    public function getCurrencies()
    {
        $currencies_list = array();
        
        $currencies = $this->currenciesModel->getConfigAllowCurrencies();
        
        $baseCurrencyCode = $this->sp_storeManager->getStore()->getCurrentCurrencyCode();

        foreach ($currencies as $currency) {
            $currencies_list["currency_list"][] = array(
                "id_currency" => $currency,
                'name' => $this->_localeFormat->getCurrency($currency)->getName() . ' (' . $currency . ')',
                'currency_code' => $currency,
                'currency_symbol' => $this->_localeFormat->getCurrency($currency)->getSymbol()
            );
        }
        $currencies_list["default_currency_id"] = $baseCurrencyCode;
        $currencies_list["default_currency_code"] = $baseCurrencyCode;
        return $currencies_list;
    }
    
    public function getFirebaseServerKey()
    {
        $push_notification_settings = $this->dataHelper->getSettings('push_notification_settings');
        if (isset($push_notification_settings['firebase_server_key']) && trim($push_notification_settings['firebase_server_key'] != '')) {
            return $push_notification_settings['firebase_server_key'];
        } else {
            return false;
        }
    }
    
    public function formatPrice($price, $format = true, $includeContainer = false) {
        return $this->_priceHelper->currency($price, $format, $includeContainer);
    }
    
    public function getImageUrl($product_image) {
        return $this->dataHelper->getMediaUrl().$this->sp_objectManager->get('\Magento\Catalog\Model\Product\Media\Config')->getMediaPath($product_image);
    }
    
    public function checkWishlist()
    {
        $wishlist_active = $this->dataHelper->getSavedSettings('wishlist/general/active');
        if ($wishlist_active == "1") {
            return "1";
        } else {
            return "0";
        }
    }
    
    public function appSetLanguage()
    {
        $response = array();
        $translated = array();

        $this->updateLanguageFileRecords();
        if ($this->sp_request->isPost()) {
            $post_data = $this->sp_request->getPost();
            $storeId = $post_data["iso_code"];
            $store = $this->storeRepository->getActiveStoreByCode($storeId);
            $store_code = $store->getCode();
            $this->dataHelper->setCookie(\Magento\Store\Model\StoreCookieManager::COOKIE_NAME, $store_code);
            $defaultStoreView = $this->sp_storeManager->getDefaultStoreView();
            $this->httpContext->setValue(Store::ENTITY, $store->getCode(), $defaultStoreView->getCode());
            $this->storeCookieManager->setStoreCookie($store);
            if (isset($post_data["all_app_texts"]) && $post_data["all_app_texts"]) {
                $iso_code_store_view_id = $this->sp_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
                $localeInterface = $this->sp_objectManager->create('Magento\Framework\Locale\Resolver');
                $localeInterface->setLocale($iso_code_store_view_id);
                $localeInterface->setDefaultLocale($iso_code_store_view_id);
                foreach ($this->dataHelper->getAllTextKeys() as $text) {
                    $translated[] = array("unique_key" => $text, "iso_code" => substr($iso_code_store_view_id, 0, 2), "trans_text" => __($text));
                }
            }
        }
        $response["translated_texts"] = $translated;
        $response["install_module"] = "";
        $response["languages_record"] = $this->dataHelper->returnLanguageRecordAsArray();
        $response["SID"] = $this->getSId();
        $response["session_data"] = "";
        $response["version"] = $this->dataHelper->getVersion();
        $this->dataHelper->logresponse($this->sp_request, 'appSetLanguage', $response);
        return $response;
    }
    
    
    public function appGetTranslations()
    {
        $this->updateLanguageFileRecords();
        $response = array();
        $translated = array();
        if ($this->sp_request->isPost()) {
            $all_app_texts = $this->sp_request->getPost('all_app_texts');
            $storeId = $this->sp_request->getParam("iso_code");//ed($storeId);
            if(!empty($storeId)) {
				try {
					$store = $this->storeRepository->getActiveStoreByCode($storeId);
					$store_code = $store->getCode();
					$this->dataHelper->setCookie(\Magento\Store\Model\StoreCookieManager::COOKIE_NAME, $store_code);
					$defaultStoreView = $this->sp_storeManager->getDefaultStoreView();
					$this->httpContext->setValue(Store::ENTITY, $store->getCode(), $defaultStoreView->getCode());
					$this->storeCookieManager->setStoreCookie($store);
					if (isset($all_app_texts) && $all_app_texts) {
						$iso_code_store_view_id = $this->sp_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
						$localeInterface = $this->sp_objectManager->create('Magento\Framework\Locale\Resolver');
						$localeInterface->setLocale($iso_code_store_view_id);
						$localeInterface->setDefaultLocale($iso_code_store_view_id);
						foreach ($this->dataHelper->getAllTextKeys() as $text) {
							$translated[] = array("unique_key" => $text, "iso_code" => substr($iso_code_store_view_id, 0, 2), "trans_text" => __($text));
						}
					}
				} catch(\Exception $e) {
					foreach ($this->dataHelper->getAllTextKeys() as $text) {
						$translated[] = array("unique_key" => $text, "iso_code" => "en", "trans_text" => __($text));
					}
				}
			} else {
				foreach ($this->dataHelper->getAllTextKeys() as $text) {
					$translated[] = array("unique_key" => $text, "iso_code" => "en", "trans_text" => __($text));
				}
			}
        }
        $response["translated_texts"] = $translated;
        
        //Read JSON file to get Google details
        $google_app_id = "";
        $google_api_key = "";
        $google_data = $this->dataHelper->getSettings('google_login_settings');
        $facebook_data = $this->dataHelper->getSettings('facebook_login_settings');
        $google_credentials = $this->dataHelper->getSavedSettings('knowband/mobileappbuilder/google_credentials');
        if (isset($google_data['google_login_status'])) {
            
            //Application ID
            if (isset($google_credentials['google_app_id'])) {
                $google_app_id = $google_credentials['google_app_id'];
            }
            //API Key
            if (isset($google_credentials['google_api_key'])) {
                $google_api_key = $google_credentials['google_api_key'];
            }
        }
        //Add code to send Google and FaceBook details
        $response['social_login'] = (object) array(
            "is_facebook_login_enabled" => (isset($facebook_data['facebook_login_status'])) ? "true" : "false",
            "is_google_login_enabled" => (isset($google_data['google_login_status'])) ? "true" : "false",
            "google_app_id" => $google_app_id,
            "api_key" => $google_api_key,
            "fb_app_id" => isset($facebook_data['facebook_app_id']) ? $facebook_data['facebook_app_id'] : ""
        );
        
        $response["install_module"] = "";
        $response["languages_record"] = $this->dataHelper->returnLanguageRecordAsArray();
        $response["SID"] = $this->getSId();
        $response["version"] = $this->dataHelper->getVersion();
        $this->dataHelper->logresponse($this->sp_request, 'appGetTranslations', $response);
        return $response;
    }
    
    public function updateLanguageFileRecords()
    {
        $file_path = $this->dataHelper->getRootPath().'/var/log/'. self::TRANSLATION_RECORD_FILE;
        $records = array();
        $lang_record = $this->dataHelper->returnLanguageRecordAsArray();
        $stores = $this->sp_storeManager->getStores($withDefault = false);
        foreach ($stores as $store) {
            $iso_code = $this->sp_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId());
            $file_path_translation = $this->dataHelper->getRootPath().'/app/code/Knowband/Mobileappbuilder/i18n/' . $iso_code . '.csv';
            $store_view_id = $store->getId();
            if (!empty($lang_record)) {
                if ($lang_record[$store_view_id]["timestamp"] != filemtime($file_path_translation)) {
                    $records[$store_view_id][] = $store_view_id;
                    $records[$store_view_id][] = filemtime($file_path_translation) ? filemtime($file_path_translation) : time();
                } else {
                    $records[$store_view_id][] = $store_view_id;
                    $records[$store_view_id][] = $lang_record[$store_view_id]["timestamp"];
                }
            } else {
                $records[$store_view_id][] = $store_view_id;
                $records[$store_view_id][] = filemtime($file_path_translation) ? filemtime($file_path_translation) : time();
            }
        }


        $this->writeArraytoCSV($records, $file_path);
    }

    private function writeArraytoCSV($array, $path)
    {
        $file = fopen($path, "w");

        foreach ($array as $line) {
            fputcsv($file, $line);
        }
        fclose($file);
    }
    
    /*
     * Login for normal user i.e. through email and password 
     * 
     *      
     */

    public function appLogin()
    {
        $response = array();
        if ($this->sp_request->isPost()) {

            $email = $this->sp_request->getParam("email"); 
            $password = $this->sp_request->getParam("password"); 
            try {
                $session = $this->customerSession;
                try {
                    $customer =  $this->customerAccountManagement->authenticate(trim($email), $password);
                    $session->setCustomerDataAsLoggedIn($customer);
                    $session->regenerateId();
                    $response["install_module"] = "";
                    $response["login_user"] = array(
                        "status" => "success",
                        "message" => __("Login Successful."),
                        "customer_id" => $customer->getId(),
                        "wishlist_count" => $this->wishlistFactory->create()->loadByCustomerId($customer->getId(), true)->getItemsCount(),
                        "session_data" => "",
                        "cart_count" => $this->getCartCount()
                    );
                } catch (\Magento\Framework\Exception\EmailNotConfirmedException $e) {
                    $response["login_user"] = array(
                        "status" => "failure",
                        "message" => $e->getMessage()
                    );
                } catch (\Magento\Framework\Exception\InvalidEmailOrPasswordException $e) {
                    $response["login_user"] = array(
                        "status" => "failure",
                        "message" => $e->getMessage()
                    );
                } catch (\Exception $e) {
                    $response["login_user"] = array(
                        "status" => "failure",
                        "message" => $e->getMessage()
                    );
                }
            } catch (\Exception $ex) {
                $response["login_user"] = array(
                    "status" => "failure",
                    "message" => $ex->getMessage()
                );
            }
        }
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["install_module"] = "";
        $this->dataHelper->logresponse($this->sp_request, 'appLogin', $response);
        return $response;
    }
    
    protected function getCartCount()
    {
        $total = 0;
        foreach ($this->quote->getQuote()->getAllVisibleItems() as $item) {
            $total += ($item->getQty());
        }
        return $total;
    }
    
    /*
     * Registration for normal user
     * 
     *      
     */

    public function appRegisterUser()
    {
        $response = array();
        if ($this->sp_request->isPost()) {
            $json_data = $this->sp_request->getParam("signup");
            $post_data = json_decode($json_data, true);
            $errors = array();
            $signup_form_config = $this->sp_scopeConfig->getValue('customer/address', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            
            $prefix = (isset($post_data["title"]) && trim($post_data["title"])) ? $post_data["title"] : NULL;
            $firstname = (isset($post_data["first_name"]) && trim($post_data["first_name"])) ? $post_data["first_name"] : NULL;
            $middlename = (isset($post_data["middlename"]) && trim($post_data["middlename"])) ? $post_data["middlename"] : NULL;
            $lastname = (isset($post_data["last_name"]) && trim($post_data["last_name"])) ? $post_data["last_name"] : NULL;
            $suffix = (isset($post_data["suffix"]) && trim($post_data["suffix"])) ? $post_data["suffix"] : NULL;
            $email = (isset($post_data["email"]) && trim($post_data["email"])) ? $post_data["email"] : NULL;
            $dob = (isset($post_data["dob"]) && trim($post_data["dob"])) ? $post_data["dob"] : NULL;
            $taxvat = (isset($post_data["taxvat"]) && trim($post_data["taxvat"])) ? $post_data["taxvat"] : NULL;
            $gender = (isset($post_data["gender"]) && trim($post_data["gender"])) ? $post_data["gender"] : NULL;
            $password = (isset($post_data["password"]) && trim($post_data["password"])) ? $post_data["password"] : NULL;
            $websiteId = $this->sp_storeManager->getWebsite()->getWebsiteId();
            $store = $this->sp_storeManager->getWebsite(true)->getDefaultGroup()->getDefaultStore();
            $customer   = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($firstname)
                    ->setLastname($lastname)
                    ->setEmail($email)
                    ->setPassword($password);
            if (is_null($firstname)) {
                $errors["firstname"] = __("This is a required field.");
            }
            if (is_null($lastname)) {
                $errors["lastname"] = __("This is a required field.");
            }
            if (is_null($email)) {
                $errors["email"] = __("This is a required field.");
            }
            if (is_null($password)) {
                $errors["password"] = __("This is a required field.");
            }
            if (!is_null($prefix)) {
                $customer->setPrefix($prefix);
            } else {
                if (isset($signup_form_config["prefix_show"]) && $signup_form_config["prefix_show"] == "req") {
                    $errors["prefix"] = __("This is a required field.");
                }
            }
            if (!is_null($suffix)) {
                $customer->setSuffix($suffix);
            } else {
                if (isset($signup_form_config["suffix_show"]) && $signup_form_config["suffix_show"] == "req") {
                    $errors["suffix"] = __("This is a required field.");
                }
            }
            if (!is_null($middlename)) {
                $customer->setMiddlename($middlename);
            }
            if (!is_null($dob)) {
                $customer->setDob($this->date->date(null, $dob));
            } else {
                if (isset($signup_form_config["dob_show"]) && $signup_form_config["dob_show"] == "req") {
                    $errors["dob"] = __("This is a required field.");
                }
            }
            if (!is_null($taxvat)) {
                $customer->setTaxvat($taxvat);
            } else {
                if (isset($signup_form_config["taxvat_show"]) && $signup_form_config["taxvat_show"] == "req") {
                    $errors["taxvat"] = __("This is a required field.");
                }
            }
            if (!is_null($gender)) {
                $customer->setGender($gender);
            } else {
                if (isset($signup_form_config["gender_show"]) && $signup_form_config["gender_show"] == "req") {
                    $errors["gender"] = __("This is a required field.");
                }
            }
            try {
                if (empty($errors)) {
                    $customer->save();
                    $customer->sendNewAccountEmail();
                    $customer =  $this->customerAccountManagement->authenticate(trim($email), $password);
                    $this->customerSession->setCustomerDataAsLoggedIn($customer);
                    $response["install_module"] = "";
                    $response["signup_user"] = array(
                        "status" => "success",
                        "message" => __("Registration Successful."),
                        "customer_id" => $customer->getId(),
                        "wishlist_count" => $this->wishlistFactory->create()->loadByCustomerId($customer->getId(), true)->getItemsCount(),
                        "session_data" => "",
                        "cart_count" => $this->getCartCount(),
                    );
                } else {
                    $response["signup_user"] = array(
                        "status" => "failure",
                        "message" => $errors
                    );
                }
            } catch (\Exception $e) {
                $response["signup_user"] = array(
                    "status" => "failure",
                    "message" => $e->getMessage()
                );
            }
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["install_module"] = "";
        $this->dataHelper->logresponse($this->sp_request, 'appRegisterUser', $response);
        return $response;
    }
    
    /*
     * Login for social account user
     * 
     *      
     */

    public function appSociallogin()
    {
        $json_data = $this->sp_request->getParam('login');
        $post_data = json_decode($json_data, true);
        $email = $post_data['email'];
        $first_name = $post_data['first_name'];
        $last_name = $post_data['last_name'];
        $response = array();
        if ($email) {
            try {
                $websiteId = $this->sp_storeManager->getWebsite()->getWebsiteId();
                $customer = $this->customer;
                $customer->setWebsiteId($websiteId);
                $customer->loadByEmail($email);
                $register_check = true;
                $customer_data = $customer->getData();
                $customer->unsetData();
                if (empty($customer_data)) {
                    $register_check = $this->createSocialCustomer($email, $first_name, $last_name);
                }
                if ($register_check) {
                    try {
                        $customer_login = $this->customer;
                        $customer_login->setWebsiteId($websiteId);
                        $customer_login->loadByEmail($email);
                        $session = $this->customerSession;
                        $session->loginById($customer_login->getId());
                        $session->setCustomerAsLoggedIn($customer_login);
                        $response["install_module"] = "";
                        $response["login_user"] = array(
                            "status" => "success",
                            "message" => __("Login Successful."),
                            "customer_id" => $customer_login->getId(),
                            "wishlist_count" => $this->wishlistFactory->create()->loadByCustomerId($customer->getId(), true)->getItemsCount(),
                            "session_data" => "",
                            "cart_count" => $this->getCartCount(),
                        );
                        $customer_login->unsetData();
                    } catch (\Exception $ex) {
                        $response["login_user"] = array(
                            "status" => "failure",
                            "message" => $ex->getMessage()
                        );
                    }
                } else {
                    $response["login_user"] = array(
                        "status" => "failure",
                        "message" => __("Cannot create user account.")
                    );
                }
            } catch (\Exception $ex) {
                $response["login_user"] = array(
                    "status" => "failure",
                    "message" => $ex->getMessage()
                );
            }
        } else {
            $response["login_user"] = array(
                "status" => "failure",
                "message" => __("Email Id is required.")
            );
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["install_module"] = "";
        $this->dataHelper->logresponse($this->sp_request, 'appSociallogin', $response);
        return $response;
    }
    
    /*
     * Registration for socail user
     * 
     *      
     */

    public function createSocialCustomer($email, $firstname, $lastname)
    {
        if (isset($email) && $email != "") {
            $websiteId = $this->sp_storeManager->getWebsite()->getWebsiteId();
            $customer_register   = $this->customerFactory->create();
            $customer_register->setWebsiteId($websiteId);
            $customer_register->setEmail($email);
            $customer_register->setPassword(substr(uniqid(rand(), true), 0, 9));
            $customer_register->setFirstname($firstname);
            $customer_register->setLastname($lastname);

            try {
                $customer_register->save();
                $customer_register->setConfirmation('1'); // or it should be null
                $customer_register->save();
//                $session = $this->customerSession;
//                $session->loginById($customer_register->getEntityId());
//                $session->setCustomerAsLoggedIn($customer_register);
            } catch (\Exception $ex) {
                return false;
            }
            try{
                $storeId = $customer_register->getSendemailStoreId();
                $customer_register->sendNewAccountEmail('registered', '', $storeId);
                $customer_register->unsetData();
            } catch (\Exception $ex) {
                
            }
            return true;
        } else {
            return false;
        }
    }
    
    public function appGetCategoryDetails()
    {
        try {
            if ($this->sp_request->isPost()) {
                $category_id = $this->sp_request->getParam("category_id");
                $page_number = !is_null($this->sp_request->getParam("page_number")) ? $this->sp_request->getParam("page_number") : 1;
                $item_count = !is_null($this->sp_request->getParam("item_count")) ? $this->sp_request->getParam("item_count") : 12;
                $sort_by = !is_null($this->sp_request->getParam("sort_by")) ? $this->sp_request->getParam("sort_by") : NULL;
                $search_term = !is_null($this->sp_request->getParam("search_term")) ? $this->sp_request->getParam("search_term") : NULL;
                $root_category = $this->sp_storeManager->getStore()->getRootCategoryId();

                $title = '';
                if ($search_term) {
                    $title = $search_term;
                } else if (isset($category_id) && $category_id !== "") {
                    $categorymodel = $this->sp_objectManager->create('Magento\Catalog\Model\Category')
                            ->load($category_id);
                    $title = $categorymodel->getName();
                }

                $category_id = (isset($category_id) && $category_id !== "") ? $category_id : $root_category;

                $response = array();
                $listProduct = $this->sp_objectManager->create(
                    'Magento\Catalog\Block\Product\ListProduct'
                );
                $layer = $listProduct->getLayer();
                

                
                $filter_parameters = $this->sp_request->getParam('filter');
                $attributeCodes = array();
                if (isset($filter_parameters)) {
                    $parsed_filter_data = json_decode($filter_parameters, true);
                    if (isset($parsed_filter_data)) {
                        foreach ($parsed_filter_data["filter_result"] as $filter_data) {
                            $filter_code_data = explode("|", $filter_data["title"]);
                            $filter_code = $filter_code_data[1];
                            foreach ($filter_data["items"] as $items) {
                                if($filter_code == 'cat'){
                                    $category_id = $items["id"];
                                }
                                $attributeCodes[str_replace('000 ', '', $filter_code)][] = str_replace('000 ', '', $items["id"]);
                            }
                        }
                    }
                }
                $category = $this->sp_objectManager->create('Magento\Catalog\Model\Category')->load($category_id);
                $layer->setCurrentCategory($category);
                $collection = $layer->getProductCollection();

                if (isset($search_term) && trim($search_term) != "") {
                    $collection = $this->sp_objectManager->create(
                        \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection::class,
                        [
                            'searchRequestName' => 'quick_search_container'
                        ]
                    );
                    $collection->addSearchFilter($search_term);
                }
                foreach ($attributeCodes as $code => $value) {
                    $values_string = '';
                    if ($code == 'price') {
                        $price_filter_range = $this->getPriceFilterCondition($value);
                        if (!empty($price_filter_range)) {
                            $collection->addAttributeToFilter('price', $price_filter_range);
                        }
                        continue;
                    }
                    foreach ($value as $val) {
                        $values_string .= "'" . $val . "',";
                    }
                    $values_string = trim($values_string, ",");
                    $collection->addFieldToFilter($code, ['in' => [$values_string]]);
                }
//                foreach ($attributeCodes as $code => $value) {
//                    $attributeId = $this->_eavAttribute->getIdByCode('catalog_product', $code);
//                    if ($attributeId) {//pd($attributeModel->getData());
//                        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
//                        $tableAlias = $code . '_idx';
//                        $values_string = '';
//                        foreach ($value as $val) {
//                            $values_string .= "'" . $val . "',";
//                        }
//                        $values_string = trim($values_string, ",");
//                        $conditions = array(
//                            "{$tableAlias}.entity_id = e.entity_id",
//                            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attributeId),
//                            $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId()),
//                            $connection->quoteInto("{$tableAlias}.value IN (?)", $values_string)
//                        );
//                            
//                        
//                        $collection->getSelect()->join(
//                                array($tableAlias => $connection->getTableName('catalog_product_index_eav')), implode(' AND ', $conditions), array()
//                        );
//                        $collection->distinct(true);
//                        
//                    }
//                }
                if (isset($sort_by)) {
                    if ($sort_by == "low") {
                        $collection->setOrder('price', 'ASC');
                    } elseif ($sort_by == "high") {
                        $collection->setOrder('price', 'DESC');
                    }
                }
                $collection->setPageSize($item_count)->setCurPage($page_number);
                $last_page = $collection->getLastPageNumber();
//                echo $collection->getSelect()->__toString();
                $all_products = array();
                if ($page_number <= $last_page) {
                    foreach ($collection as $_product) {
                        $product = $this->_productRepo->getByid($_product->getId());
                        
                        //Price to be shown for the grouped product
                        if($product->getTypeId() == "grouped"){
                            $products = $product->getTypeInstance()->getAssociatedProducts($product);
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
                            $formatted_price = __('Starting at ') . $this->formatPrice($minPrice, true, false);
                        } else {
                           $formatted_price =  $this->formatPrice($product->getFinalPrice(), true, false);
                        }
                        
                        $all_products[] = array(
                            'id' => $product->getId(),
                            'is_in_wishlist' => $this->dataHelper->isInWishlist($product->getId()),
                            'name' => $product->getName(),
                            'price' => $formatted_price,
                            'available_for_order' => $product->isSaleable() ? "1" : "0",
                            'show_price' => "1",
                            'new_products' => $this->isProductNew($product) ? "1" : "0 ",
                            'on_sale_products' => ($product->getPrice() > $product->getFinalPrice()) ? "1" : "0",
                            'discount_price' => ($product->getPrice() > $product->getFinalPrice()) ? $this->formatPrice($product->getPrice()) : 0,
                            "discount_percentage" => (($product->getPrice() > $product->getFinalPrice()) && $product->getFinalPrice() !== 0) ? number_format((($product->getPrice() - $product->getFinalPrice()) / $product->getPrice()) * 100, 2, '.', '') : "0",
                            "src" => $this->getImageUrl($product->getSmallImage()),
                        );
                    }
                }

                $response["fproducts"]["products"] = $all_products;
                $response["fproducts"]["title"] = $title;
            } else {
                $response["status"] = "failure";
                $response["message"] = __("Something went wrong");
            }
        } catch (Exception $e) {
            $response = array('status' => "failure", 'message' => $e->getMessage());
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["install_module"] = "";
        $this->dataHelper->logresponse($this->sp_request, 'appGetCategoryDetails', $response);
        return $response;
    }
    
    private function getPriceFilterCondition($priceFilterValue = []) {
        $final_arr = [];
        foreach ($priceFilterValue as $range) {
            $range_exp = explode('-', $range);
            if (count($range_exp) != 2) {
                continue;
            } else {
                if ($range_exp[0] == '') {
                    $final_arr[] = ['from' => 0, 'to' => $range_exp[1]];
                } else if ($range_exp[1] == '') {
                    $final_arr[] = ['from' => $range_exp[0], 'to' => 99999999];
                } else {
                    $final_arr[] = ['from' => $range_exp[0], 'to' => $range_exp[1]];
                }
            }
        }
        return $final_arr;
    }

    public function appGetFilters($category_id = null)
    {
        $response = array();
        try {
            if ($this->sp_request->isPost()) {

                if ($category_id = $this->sp_request->getParam('category_id')) {
                    $response['filter_result'] = array();
                    $filterableAttributes = $this->sp_objectManager->get(\Magento\Catalog\Model\Layer\Category\FilterableAttributeList::class);
                    
                    $layerResolver = $this->sp_objectManager->get(\Magento\Catalog\Model\Layer\Resolver::class);
                    $filterList = $this->sp_objectManager->create(
                    \Magento\Catalog\Model\Layer\FilterList::class,
                        [
                            'filterableAttributes' => $filterableAttributes
                        ]
                    );

                    $layer = $layerResolver->get();
                    $layer->setCurrentCategory($category_id);
                    $attributes = $filterList->getFilters($layer);//pd($attributes);
                    foreach ($attributes as $attribute) {//pd($attribute);
                        
                        $items = array();
                        foreach ($attribute->getItems() as $option) {
                            $items[] = array('name' => strip_tags($option->getLabel()), 'id' => $option->getValue(), 'color_value' => '');
                        }
                        if ($items != null) {
                            $response['filter_result'][] = array('id' => $attribute->getRequestVar(), "title" => $attribute->getName(), "choice_type" => "multiple", 'is_color_group' => "0", 'items' => $items, 'name' => 'id_attribute_group');
                        }
                        unset($items);
                    }
                } else {
                    $response["status"] = "failure";
                    $response["message"] = __("Please select a category.");
                }
            } else {
                $response["status"] = "failure";
                $response["message"] = __("Something went wrong");
            }
        } catch (Exception $e) {
            $response = array('status' => "failure", 'message' => $e->getMessage());
        }
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["install_module"] = "";
        $this->dataHelper->logresponse($this->sp_request, 'appGetFilters', $response);
        return $response;
    }
    
    
    public function appGetProductDetails()
    {
        $response = array();
        try{
            if ($this->sp_request->isPost()) {
                if ($product_id = $this->sp_request->getParam("product_id")) {
                    $product = $this->_productRepo->getByid($product_id);
                    if ($product->getId()) {
//                  $product = Mage::getModel('catalog/product')->setStoreId($this->sp_request->getPost("iso_code"))->load($product_id);

                        $stockItemResource = $this->sp_objectManager->get('\Magento\CatalogInventory\Api\StockRegistryInterface');
                        $stock = $stockItemResource->getStockItem($product->getId());
                        $special_price = !is_null($product->getSpecialPrice()) ? $product->getSpecialPrice() : 0;
                        $response["product"] = array(
                            "accessories" => array("accessories_items" => array(), "has_accessories" => 0),
                            "allow_out_of_stock" => "0",
                            "id_product" => $product->getId(),
                            'is_in_wishlist' => $this->dataHelper->isInWishlist($product->getId()),
                            "name" => $product->getName(),
                            "price" => $this->formatPrice($product->getFinalPrice()),
                            //                    "int_price" => $product->getPrice(),
                            "product_type" => $product->getTypeId(),
                            //                    "sku" => $product->getSku(),
                            "discount_price" => $this->formatPrice($special_price),
                            "discount_percentage" => $special_price ? number_format((($product->getPrice() - $special_price) / $product->getPrice()) * 100, 2) : 0,
                            "minimal_quantity" => $stock->getMinQty(),
                            'new_products' => $this->isProductNew($product) ? "1" : "0 ",
                            'on_sale_products' => ($product->getPrice() > $product->getFinalPrice()) ? "1" : "0",
                            "is_grouped_product" => $product->getTypeId() == "grouped" ? "1" : "0",
                            "grouped_products" => $this->getGroupedProducts($product),
                            "images" => $this->getProductImages($product),
                            "options" => $this->getProductOptions($product),
                            "description" => $product->getDescription(),
                            "product_info" => $this->getAdditionalData($product),
                            'available_for_order' => $product->isSaleable() ? "1" : "0",
                            'customization_fields' => array("customizable_items" => array(), "is_customizable" => "0"),
                            "customization_message" => "",
                            "has_file_customization" => "0",
                            'combinations' => array(),
                            "pack_products" => array("is_pack" => "0", "pack_items" => array()),
                            "product_attachments_array" => array(),
                            "product_youtube_url" => "",
                            "seller_info" => array(),
                            'show_price' => "1",
                            'quantity' => "0",
                            'product_url' => $product->getProductUrl()
                        );
                        
                        //Price to be shown for grouped products
                        if($product->getTypeId() == "grouped"){
                            $products = $product->getTypeInstance()->getAssociatedProducts($product);
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
                            $response["product"]['price'] = __('Starting at ') . $this->formatPrice($minPrice);
                        }

                        //Add seller Information if Marketplace is installed and feature is enable
                        if ($this->dataHelper->isMarketplaceEnabled()) {
                            //Marketplace code will be implemented after module is developed.
                        }
                    } else {
                        $response["product"] = array(
                            "status" => "failure",
                            "message" => __("Product not found.")
                        );
                    }
                } else {
                    $response["product"] = array(
                        "status" => "failure",
                        "message" => __("Product Id is required.")
                    );
                }
            }
        } catch (\Exception $ex) {
            
        }
        
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["install_module"] = "";
        $this->dataHelper->logresponse($this->sp_request, 'appGetProductDetails', $response);
        return $response;
    }
    
    public function getProductSellerInfo($productID)
    {
        
        return false;
    }
    
    public function getGroupedProducts($main_product)
    {
        $grouped_products = array();
        if ($main_product->getTypeId() == "grouped") {
            $_simples = $this->sp_objectManager->get('Magento\GroupedProduct\Model\Product\Type\Grouped')->getAssociatedProductCollection($main_product);
            foreach ($_simples as $simple_product) {
                $product = $this->_productRepo->getByid($simple_product->getId());
                $special_price = !is_null($product->getSpecialPrice()) ? $product->getSpecialPrice() : 0;
                $grouped_products[] = array(
                    "product_id" => $product->getId(),
                    'is_in_wishlist' => $this->dataHelper->isInWishlist($product->getId()),
                    "title" => $product->getName(),
                    "price" => $this->formatPrice($product->getFinalPrice()),
                    "sku" => $product->getSku(),
                    "discount_price" => $this->formatPrice($special_price),
                    "discount_percentage" => $special_price ? number_format((($product->getPrice() - $special_price) / $product->getPrice()) * 100, 2) : 0,
                    'available_for_order' => $product->isSaleable() ? "1" : "0",
                    "images" => $this->getGroupedProductImages($product),
                );
            }
        }
        return $grouped_products;
    }
    
    public function getGroupedProductImages($product)
    {
        $images = "";
        foreach ($product->getMediaGalleryImages() as $image) {
            $images = $image->getUrl();
        }
        return $images;
    }
    
    public function getProductImages($product)
    {
        $images = array();
        foreach ($product->getMediaGalleryImages() as $image) {
            $images[] = array("src" => $image->getUrl());
        }
        return $images;
    }
    
    public function getProductOptions($product)
    {
        $front_values = array();

        if ($product->getTypeId() == "configurable") {
            $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
            $attributeOptions = array();
            foreach ($attributes as $attribute) {
                $productAttributeOptions[] = array(
                    'id' => $attribute->getId(),
                    'label' => $attribute->getLabel(),
                    'use_default' => $attribute->getUseDefault(),
                    'position' => $attribute->getPosition(),
                    'values' => $attribute->getOptions() ? $attribute->getOptions() : array(),
                    'attribute_id' => $attribute->getProductAttribute()->getId(),
                    'attribute_code' => $attribute->getProductAttribute()->getAttributeCode(),
                    'frontend_label' => $attribute->getProductAttribute()->getFrontend()->getLabel(),
                    'store_label' => $attribute->getProductAttribute()->getStoreLabel(),
                );
            }
            foreach ($productAttributeOptions as $productAttribute) {
                foreach ($productAttribute['values'] as $attribute) {
                    $attributeOptions[$productAttribute['attribute_code']][] = array(
                        "id" => $attribute['value_index'],
                        "value" => $attribute['store_label'],
                        "price" => isset($attribute["pricing_value"])?$attribute["pricing_value"]:NULL,
                    );
                }
            }
            foreach ($attributes as $attr) {
                $product_attr = $attr->getProductAttribute();
                $front_values[] = array(
                    "title" => $product_attr->getFrontendLabel(),
                    "type" => $product_attr->getFrontendInput(),
                    "required" => $product_attr->getIsUserDefined(),
                    "id" => $product_attr->getAttributeId(),
                    "items" => isset($attributeOptions[$product_attr->getAttributeCode()]) ? $attributeOptions[$product_attr->getAttributeCode()] : array(),
                    'price' => $product_attr->getPrice(),
                );
            }
        } elseif ($product->getTypeId() == "bundle") {
            $options = array();
            $typeInstance = $product->getTypeInstance(true);
            $typeInstance->setStoreFilter($product->getStoreId(), $product);

            $optionCollection = $typeInstance->getOptionsCollection($product);

            $selectionCollection = $typeInstance->getSelectionsCollection(
                    $typeInstance->getOptionsIds($product), $product
            );
            
            $optionsArray = $optionCollection->appendSelections($selectionCollection, false, $this->_catalogProduct->getSkipSaleableCheck()
            );

            foreach ($optionsArray as $_option) {
                if (!$_option->getSelections()) {
                    continue;
                }
                $optionId = $_option->getId();
                $option = array(
                    'selections' => array(),
                    'title' => $_option->getTitle(),
                    'type' => $_option->getType(),
                    'required' => $_option->getRequired(),
                );

                foreach ($_option->getSelections() as $_selection) {
                    $selectionId = $_selection->getSelectionId();
                    $_qty = !($_selection->getSelectionQty() * 1) ? '1' : $_selection->getSelectionQty() * 1;


                    $selection = array(
                        'qty' => $_qty,
                        'customQty' => $_selection->getSelectionCanChangeQty(),
                        'price' => $this->formatPrice($_selection->getFinalPrice()),
                        'name' => $_selection->getName(),
                    );

                    $responseObject = new \Magento\Framework\DataObject();
                    $this->_eventManager->dispatch('catalog_product_view_config', ['response_object' => $responseObject, 'selection' => $_selection]);
                    if (is_array($responseObject->getAdditionalOptions())) {
                        foreach ($responseObject->getAdditionalOptions() as $o => $v) {
                            $selection[$o] = $v;
                        }
                    }
                    $option['selections'][$selectionId] = $selection;
                }
                $options[$optionId] = $option;
            }
            foreach ($options as $_id => $option) {

                $option_values = array();
                foreach ($option["selections"] as $value_id => $value) {
                    $option_values[] = array(
                        'id' => (string) $value_id,
                        'value' => $value["name"],
                        'custom_quantity' => $value["customQty"],
                        "price" => $this->formatPrice($value["price"]),
                    );
                }

                $front_values[] = array(
                    'id' => (string) $_id,
                    'title' => $option["title"],
                    'required' => $option["required"],
                    'type' => $option["type"],
                    'items' => $option_values
                );
            }
        } else if ($product->getTypeId() == "downloadable") {
            $downloadableLinks = array();
            $links= $this->_linkModel
                      ->getCollection()
                        ->addTitleToResult()
                        ->addPriceToResult($this->dataHelper->getStoreIdDetails())
                      ->addFieldToFilter('product_id',array('eq'=>$product->getId()));
            $required = $product->getData('links_purchased_separately');
            if ($required) {
                foreach($links as $linkData){
                    $downloadableLinks[] = array(
                        "value" => $linkData['title'],
                        "id" => $linkData['link_id'],
                        "custom_quantity" => array(),
                        'price' => $linkData['price'],
                    ); 
                 }
                 if ($downloadableLinks) {
                     $front_values[] = array(
                        "title" => ($product->getData('links_title') != '') ? $product->getData('links_title') : __('Links'),
                        "type" => 'checkbox',
                        "required" => $required,
                        "id" => $product->getId(),
                        "items" => $downloadableLinks,
                        'price' => '',
                    );
                 }
            }
        }
        return $front_values;
    }
    
    public function getAdditionalData($product)
    {

        $data = array();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront()) {
                $value = $attribute->getFrontend()->getValue($product);

                if (!$product->hasData($attribute->getAttributeCode())) {
                    $value = __('N/A');
                } elseif ((string) $value == '') {
                    $value = __('No');
                } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = $this->priceCurrency->convertAndFormat($value);
                }
                if (is_string($value) && strlen($value)) {
                    $data[] = array(
                        'name' => $attribute->getStoreLabel(),
                        'value' => $value,
                    );
                }
            }
        }
        return $data;
    }
    
    public function appCheckLogStatus()
    {
        $response = array();
        if ($this->sp_request->isPost()) {
            $general_settings = $this->dataHelper->getSettings('general_settings');
            if (isset($general_settings['enabledlog'])) {
                $response["log_status"] = "1";
            } else {
                $response["log_status"] = "0";
            }
        }
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["install_module"] = "";
        $this->dataHelper->logresponse($this->sp_request, 'appCheckLogStatus', $response);
        return $response;
    }
    
    public function appCheckLiveChatSupportStatus()
    {
        $response = array();
        $general_settings = $this->dataHelper->getSettings('general_settings');
        if (isset($general_settings['enabledlivechat']) && $general_settings['enabledlivechat'] == 1) {
            $response["status"] = "1";
        } else {
            $response["status"] = "0";
        }
        if (isset($general_settings['livechatkey']) && $general_settings['livechatkey'] != '') {
            $response["chat_api_key"] = $general_settings['livechatkey'];
        } else {
            $response["chat_api_key"] = "";
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["install_module"] = "";
        $this->dataHelper->logresponse($this->sp_request, 'appCheckLiveChatSupportStatus', $response);
        return $response;
    }
    
    public function appAddToCart($params = false)
    {
        $response = array();
        if ($this->sp_request->isPost()) {
            if ($post_data = $this->sp_request->getParam("cart_products")) {
                $parsed_post_data = json_decode($post_data, true);
                $qty = $parsed_post_data["cart_products"][0]["quantity"];
                $product_id = $parsed_post_data["cart_products"][0]["product_id"];
                $super_attr_array = array();
                $bundle_attr_array = array();
                $group_attr_array = array();
                $bundle_qty_array = array();
                $downloadable_link_array = array();
                $hasLinks = false;
                if (isset($parsed_post_data["cart_products"][0]["product_type"])) {
                    if ($parsed_post_data["cart_products"][0]["product_type"] == "configurable") {
                        foreach ($parsed_post_data["cart_products"][0]["id_product_attribute"] as $superattr) {
                            $super_attr_array[$superattr["id"]] = $superattr["value"];
                        }
                    }
                    if ($parsed_post_data["cart_products"][0]["product_type"] == "bundle") {
                        foreach ($parsed_post_data["cart_products"][0]["id_product_attribute"] as $bundleattr) {
                            $bundle_attr_array[$bundleattr["id"]] = $bundleattr["value"];
                            $bundle_qty_array[$bundleattr["id"]] = isset($bundleattr["qty"]) ? $bundleattr["qty"] : "1";
                        }
                    }
                    if ($parsed_post_data["cart_products"][0]["product_type"] == "grouped") {
                        foreach ($parsed_post_data["cart_products"][0]["id_product_attribute"] as $groupattr) {
                            $group_attr_array[$groupattr["id"]] = $groupattr["qty"];
                        }
                    }
                    if ($parsed_post_data["cart_products"][0]["product_type"] == "downloadable") {
                        foreach ($parsed_post_data["cart_products"][0]["id_product_attribute"] as $downloadablelinks) {
                            $downloadable_link_array[] = $downloadablelinks["value"];
                            $hasLinks = true;
                        }
                        $downloadable_link_array = array_unique($downloadable_link_array);
                        
                    }
                }
                $params = array(
                    "product" => $product_id,
                    "related_product" => "",
                    "qty" => $qty,
                );
                if (isset($parsed_post_data["cart_products"][0]["product_type"])) {
                    if ($parsed_post_data["cart_products"][0]["product_type"] == "configurable") {
                        $params["super_attribute"] = $super_attr_array;
                    }
                    if ($parsed_post_data["cart_products"][0]["product_type"] == "bundle") {
                        $params["bundle_option"] = $bundle_attr_array;
                        $params["bundle_option_qty"] = $bundle_qty_array;
                    }
                    if ($parsed_post_data["cart_products"][0]["product_type"] == "grouped") {
                        $params["super_group"] = $group_attr_array;
                    }
                }
                $cart = $this->sp_objectManager->create('Magento\Checkout\Model\Cart');
                try {
                    if (isset($params['qty'])) {
                        $filter = new \Zend_Filter_LocalizedToNormalized(
                                array('locale' => $this->_store->getLocale())
                        );
                        $params['qty'] = $filter->filter($params['qty']);
                    }

                    $product = $this->_productRepo->getByid($params["product"]);
                    $related = $params['related_product'];
                    if ($hasLinks) {
                        $input = array( 'qty' => 1, 'links' => $downloadable_link_array );
                        $request = new \Magento\Framework\DataObject($input);
                        $cart->addProduct($product, $request);
                    } else {
                        
                        $cart->addProduct($product, $params);
                    }
                    if (!empty($related)) {
                        $cart->addProductsByIds(explode(',', $related));
                    }
                    $cart->save();

                    $this->quote->setCartWasUpdated(true);
                    $this->_eventManager->dispatch('checkout_cart_add_product_complete', array('product' => $product, 'request' => $this->sp_request, 'response' => $this->sp_response));
                    if (!$this->quote->getNoCartRedirect(true)) {
                        if (!$cart->getQuote()->getHasError()) {
                            $response["status"] = "success";
                            $response["message"] = $this->escaper->escapeHtml($product->getName()).__(' was added to your shopping cart.');
                        } else {
                            foreach ($cart->getQuote()->getErrors() as $errors) {
                                $response["status"] = "failure";
                                $response["message"] = $errors->getCode();
                            }
                        }
                    }
                } catch (\Exception $e) {
                    if ($this->quote->getUseNotice(true)) {
                        $response["status"] = "failure";
                        $response["message"] = $this->escaper->escapeHtml($e->getMessage());
                    } else {
                        $messages = array_unique(explode("\n", $e->getMessage()));
                        $response["status"] = "failure";
                        foreach ($messages as $message) {
                            if (count($messages) == 1) {
                                $response["message"] = $message;
                            } else {
                                $response["message"][] = $message;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $response["status"] = "failure";
                    $response["message"] = __('Cannot add the item to shopping cart.');
                }
            } else {
                $response["status"] = "failure";
                $response["message"] = __('Cannot add the item to shopping cart.');
            }
        } else {
            $response["status"] = "failure";
            $response["message"] = __('Cannot add the item to shopping cart.');
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["install_module"] = "";
        $this->dataHelper->logresponse($this->sp_request, 'appAddToCart', $response);
        return $response;
    }
    
    public function appUpdateCartQuantity()
    {
        $response = array();
        if ($post_data = $this->sp_request->getParam("cart_products")) {
            $parsed_post_data = json_decode($post_data, true);
            $cart_id = $parsed_post_data["cart_products"][0]["cart_id"];
            if ($cart_id) {
                try {
                    $qty = $parsed_post_data["cart_products"][0]["quantity"];
                    if ($qty) {
                        $cartData = array();
                        $filter = new \Zend_Filter_LocalizedToNormalized(
                                array('locale' => $this->_store->getLocale())
                        );

                        $cartData[$cart_id]['qty'] = $filter->filter(trim($qty));

                        $cart = $this->sp_objectManager->create('Magento\Checkout\Model\Cart');
                        if (!$cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                            $cart->getQuote()->setCustomerId(null);
                        }

                        $cartData = $cart->suggestItemsQty($cartData);
                        $cart->updateItems($cartData)
                                ->save();
                        $response = $this->appGetCartDetails(true);
                        $response['status'] = "success";
                        $response['message'] = __('Item has been updated in your cart successfully.');
                    } else {
                        $response['status'] = "failure";
                        $response['message'] = __('Quantity has not been set for this product.');
                    }
                } catch (\Exception $e) {
                    $response['status'] = "failure";
                    $response['message'] = $this->escaper->escapeHtml($e->getMessage());
                } catch (\Exception $e) {
                    $response['status'] = "failure";
                    $response['message'] = __('Cannot update shopping cart.');
                }
            } else {
                $response['status'] = "failure";
                $response['message'] = __('Something went wrong.');
            }
        } else {
            $response['status'] = "failure";
            $response['message'] = __('Something went wrong.');
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["install_module"] = "";
        $this->dataHelper->logresponse($this->sp_request, 'appUpdateCartQuantity', $response);
        return $response;
    }
    
    public function appGetCartDetails($array = false, $estimation = true)
    {


        // This array will be used to get the related products list if there are no mappped products with cart products 
        $notInProductIds = array();

        
        $customerSession = $this->customerSession;
        $customer = $customerSession->getCustomerDataObject();
        
        if ($customer) {
            $this->quote->getQuote()->assignCustomer($customer);
            $customerSession = $this->customerSession;
            $customer = $customerSession->getCustomer();
            $session = $this->quote;
            $quote = $session->getQuote();
            $quote->collectTotals()->save();
            
            $rates = $this->_rateCollectionFactory->create()->setAddressFilter($this->quote->getQuote()->getShippingAddress()->getId())->toArray();
            $shipping_methods = array();

            if (isset($rates['items']) && $rates['items']) {
                foreach ($rates['items'] as $_rate) {
                    $shipping_methods[] = array(
                        "name" => $_rate['method_title'],
                        "delay_text" => "",
                        "code" => $_rate['code'],
                        "price" => $this->formatPrice($_rate['price'])
                    );
                }
            }



            $selected_shipping_method = $this->quote->getQuote()->getShippingAddress()->getShippingMethod();

            if (($selected_shipping_method == '' || $selected_shipping_method === NULL) && $customer->getIsDefaultShippingAddress()) {
                $default_shipping = $customer->getDefaultShippingAddress();
                $shipping_address = $default_shipping->getData();
                $id_address = "0";
                if (isset($shipping_address["entity_id"]) && $shipping_address["entity_id"]) {
                    $id_address = $shipping_address["entity_id"];
                } elseif (isset($shipping_address["customer_address_id"]) && $shipping_address["customer_address_id"]) {
                    $id_address = $shipping_address["customer_address_id"];
                }
                $this->sp_objectManager->create('Magento\Checkout\Model\Type\Onepage')->saveShipping($shipping_address, $id_address);
                if (isset($shipping_methods[0]["code"])) {
                    $session = $this->quote;
                    $quote = $session->getQuote();
                    $selected_shipping_method = $shipping_methods[0]["code"];
                    $quote->getShippingAddress()->setShippingMethod($selected_shipping_method)->save();
                    $quote->collectTotals()->save();
                    $selected_shipping_method = $shipping_methods[0]["code"];
                }
            }
            
        }
        $cart = $this->sp_objectManager->create('Magento\Checkout\Model\Cart');
        // Shopping cart popup session variable
        $itemsCount["total_cart_items"] = $cart->getItemsCount();

        $cart->save();

        // getQuote function to get the cart details
        $cart = $cart->getQuote();

        $cartTotal = array();
        if ($cart->isVirtual()) {
            $totals = $cart->getBillingAddress()->getTotals();
        } else {
            $totals = $cart->getShippingAddress()->getTotals();
        }


        foreach ($totals as $total) {
            $cartTotal[] = array("name" => $total->getData("title"), "value" => $this->formatPrice($total->getData("value"),  true));
        }

        $vouchers_data = array();

        if (isset($totals['discount'])) {
            $vouchers_data[] = array("id" => "1", "name" => $totals['discount']->getTitle(), 'value' => $this->formatPrice($totals['discount']->getValue(), true, false));
        }


        $productCategories = array();
        $productArray = array();

        foreach ($cart->getAllVisibleItems() as $item) {
            $productCartID = $item->getId();
            $productID = $item->getProductId();
            // Product Categories - Get product categories to find related products
            $categoryIds = $item->getProduct()->getCategoryIds($productID);
            if (count($categoryIds) > 0) {
                foreach ($categoryIds as $categoryId) {
                    array_push($productCategories, $categoryId);
                }
            }
            $_item = $this->_productRepo->getByid($productID);
            $productImage = $this->sp_objectManager->get('Magento\Catalog\Helper\Image')->init($_item, 'product_base_image')
                                           ->constrainOnly(TRUE)
                                           ->keepAspectRatio(TRUE)
                                           ->keepTransparency(TRUE)
                                           ->keepFrame(FALSE)
                                           ->resize(120, 90)->getUrl();
            array_push($notInProductIds, $productID);

            // Product options
            $productOptions = array();
            $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
            if (!empty($options['attributes_info'])) {
                $productOptions = $options['attributes_info'];
                foreach ($productOptions as $k => $v) {
                    $productOptions[$k]['name'] = $productOptions[$k]['label'];
                    unset($productOptions[$k]['label']);
                }
            }
            
            $productOptions[] = array('name' => __("Quantity"), 'value' => $item->getQty());
            // Product options
            // Bundle product options
            $bundleProductOptions = array();
            if (isset($options['bundle_options'])) {
                $bundleProductOptions = array_values($options['bundle_options']);
            }
            // Bundle product options
            // Cart products array

            $checkoutSession = $this->quote;
            $checkoutquote = $this->quote->getQuote();
            $totalsquote = $checkoutquote->getTotals();
//            $_item = $this->_productRepo->getByid($productID);
            $var = $this->productConfiguration->getCustomOptions($item);
            $optionstr = '';
            if ($var) {
                foreach ($var as $o) {
                    $optionstr.=$o['label'] . ':' . $o['print_value'] . ', ';
                    $optionstr = str_replace("&quot;", "\"", $optionstr);
                    $optionstr = str_replace("&#039;", "'", $optionstr);
                    $optionstr = str_replace("&amp;", "&", $optionstr);
                    $optionstr = str_replace("&lt;", "<", $optionstr);
                    $optionstr = str_replace("&gt;;", ">", $optionstr);
                }
            }
            $stockItemResource = $this->sp_objectManager->get('\Magento\CatalogInventory\Api\StockRegistryInterface');
            $stock = $stockItemResource->getStockItem($productID);
            array_push($productArray, array(
                'product_id' => $productID,
                'is_in_wishlist' => $this->dataHelper->isInWishlist($productID),
                'title' => $item->getProduct()->getName(),
                'price' => $this->formatPrice($item->getProduct()->getFinalPrice()),
                'discount_price' => $this->formatPrice($item->getProduct()->getFinalPrice()),
                'discount_percentage' => "",
                'images' => $productImage,
                'quantity' => $item->getQty(),
                'product_items' => $productOptions,
                'cart_id' => $productCartID,
                'stock' => ($_item->isAvailable() && $stock->getIsInStock()) ? true : false,
                'bundle_product_options' => $bundleProductOptions,
                'customizable_items' => array(),
                'id_address_delivery' => "0",
                'is_gift_product' => "0",
                'id_product_attribute' => "0"
            ));
        }
        $store = $this->sp_storeManager->getWebsite(true)->getDefaultGroup()->getDefaultStore();
        if ($array) {
            if ($estimation) {
                return (array(
                    "install_module" => '',
                    "checkout_page" => array("per_products_shipping" => "0"),
                    "delay_shipping" => array("applied" => "0", "available" => "0"),
                    "gift_wrapping" => array("applied" => "0", "available" => "0", "cost_text" => "", "message" => ""),
                    "minimum_purchase_message" => "",
                    "guest_checkout_enabled" => $this->sp_scopeConfig->getValue('checkout/options/guest_checkout', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId()),
                    "voucher_allowed" => "1",
                    "voucher_html" => "",
                    "vouchers" => $vouchers_data,
                    "cart" => $itemsCount,
                    "cart_id" => "0",
                    "products" => $productArray,
                    "totals" => $cartTotal,
                    "SID" => $this->getSid(),
                    "version" => $this->dataHelper->getVersion()));
            } else {
                return (array(
                    "install_module" => '',
                    "checkout_page" => array("per_products_shipping" => "0"),
                    "delay_shipping" => array("applied" => "0", "available" => "0"),
                    "gift_wrapping" => array("applied" => "0", "available" => "0", "cost_text" => "", "message" => ""),
                    "minimum_purchase_message" => "",
                    "guest_checkout_enabled" => $this->sp_scopeConfig->getValue('checkout/options/guest_checkout', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId()),
                    "voucher_allowed" => "1",
                    "voucher_html" => "",
                    "vouchers" => $vouchers_data,
                    "cart" => $itemsCount,
                    "products" => $productArray,
                    "cart_id" => "0",
                    "totals" => $cartTotal,
                    "SID" => $this->getSid(),
                    "version" => $this->dataHelper->getVersion()));
            }
        } else {
            $this->dataHelper->logresponse($this->sp_request, 'appGetCartDetails', array(
                "install_module" => '',
                "checkout_page" => array("per_products_shipping" => "0"),
                "delay_shipping" => array("applied" => "0", "available" => "0"),
                "gift_wrapping" => array("applied" => "0", "available" => "0", "cost_text" => "", "message" => ""),
                "minimum_purchase_message" => "",
                "guest_checkout_enabled" => $this->sp_scopeConfig->getValue('checkout/options/guest_checkout', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId()),
                "voucher_allowed" => "1",
                "voucher_html" => "",
                "vouchers" => $vouchers_data,
                "cart" => $itemsCount,
                "cart_id" => "0",
                "products" => $productArray,
                "totals" => $cartTotal,
                "SID" => $this->getSid(),
                "version" => $this->dataHelper->getVersion()));
            
            return array(
                "install_module" => '',
                "checkout_page" => array("per_products_shipping" => "0"),
                "delay_shipping" => array("applied" => "0", "available" => "0"),
                "gift_wrapping" => array("applied" => "0", "available" => "0", "cost_text" => "", "message" => ""),
                "minimum_purchase_message" => "",
                "guest_checkout_enabled" => $this->sp_scopeConfig->getValue('checkout/options/guest_checkout', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId()),
                "voucher_allowed" => "1",
                "voucher_html" => "",
                "vouchers" => $vouchers_data,
                "cart" => $itemsCount,
                "cart_id" => "0",
                "products" => $productArray,
                "totals" => $cartTotal,
                "SID" => $this->getSid(),
                "version" => $this->dataHelper->getVersion()
             );
        }
    }
    
    public function appRemoveProduct()
    {
        $response = array();
        if ($post_data = $this->sp_request->getPost("cart_products")) {
            $parsed_post_data = json_decode($post_data, true);
            $cart_id = $parsed_post_data["cart_products"][0]["cart_id"];
            if ($cart_id) {
                try {
                    $cart = $this->sp_objectManager->create('Magento\Checkout\Model\Cart')->removeItem($cart_id)
                            ->save();
                    $response = $this->appGetCartDetails(true);
                    $response['status'] = "success";
                    $response['message'] = __('Item has been removed from your cart successfully.');
                } catch (\Exception $e) {
                    $response['status'] = "failure";
                    $response['message'] = __('Cannot remove the item.');
                }
            } else {
                $response['status'] = "failure";
                $response['message'] = __('Cannot remove the item.');
            }
        } else {
            $response['status'] = "failure";
            $response['message'] = __('Cannot remove the item.');
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["install_module"] = "";
        $this->dataHelper->logresponse($this->sp_request, 'appRemoveProduct', $response);
        return $response;
    }
    
    public function appGetAddressForm()
    {
        //Web-service Name: AppRegistrationPage
        $signup_form_config = $this->sp_scopeConfig->getValue(
                                'customer/address',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                            );
        $form_fields = array();
        if ($this->sp_request->getParam("id_shipping_address")) {
            $address_id = $this->sp_request->getParam("id_shipping_address");
        } else {
            $address_id = 0;
        }
        if($address_id > 0){
            $address = $this->sp_objectManager->create('Magento\Customer\Model\Address')->load($address_id);
        }else{
            $address = NULL;
        }
        if (isset($signup_form_config["prefix_show"]) && $signup_form_config["prefix_show"]) {
            $group_items = array();
            if (isset($signup_form_config["prefix_options"]) && $signup_form_config["prefix_options"] != "") {
                $options_dropdown = array_combine(explode(";", $signup_form_config["prefix_options"]), explode(";", $signup_form_config["prefix_options"]));
                foreach ($options_dropdown as $name => $label) {
                    $group_items[] = array("name" => $name, "label" => $label);
                }
            }
            $form_fields["shipping_address_items"][] = array(
                "label" => __("Prefix"),
                "type" => (isset($signup_form_config["prefix_options"]) && $signup_form_config["prefix_options"] != "") ? "dropdownfield" : "textfield",
                "name" => "prefix",
                "value" => ($address && null != $address->getPrefix()) ? $address->getPrefix() : "",
                "required" => ($signup_form_config["prefix_show"] == "req") ? "1" : "0",
                "validation" => "",
                "group_items" => $group_items
            );
        }
        $form_fields["shipping_address_items"][] = array(
            "label" => __("First Name"),
            "type" => "textfield",
            "name" => "firstname",
            "value" => ($address && null != $address->getFirstname()) ? $address->getFirstname() : "",
            "required" => "1",
            "validation" => "",
            "group_items" => array()
        );
        if (isset($signup_form_config["middlename_show"]) && $signup_form_config["middlename_show"]) {
            $form_fields["shipping_address_items"][] = array(
                "label" => __("Middle Name/Initial"),
                "type" => "textfield",
                "name" => "middlename",
                "value" => ($address && null != $address->getMiddlename()) ? $address->getMiddlename() : "",
                "required" => "0",
                "validation" => "",
                "group_items" => array()
            );
        }
        $form_fields["shipping_address_items"][] = array(
            "label" => __("Last Name"),
            "type" => "textfield",
            "name" => "lastname",
            "value" => ($address && null != $address->getLastname()) ? $address->getLastname() : "",
            "required" => "1",
            "validation" => "",
            "group_items" => array()
        );
        if (isset($signup_form_config["suffix_show"]) && $signup_form_config["suffix_show"]) {
            $group_items = array();
            if (isset($signup_form_config["suffix_options"]) && $signup_form_config["suffix_options"] != "") {
                $options_dropdown = array_combine(explode(";", $signup_form_config["suffix_options"]), explode(";", $signup_form_config["suffix_options"]));
                foreach ($options_dropdown as $name => $label) {
                    $group_items[] = array("name" => $name, "label" => $label);
                }
            }
            $form_fields["shipping_address_items"][] = array(
                "label" => __("Suffix"),
                "type" => (isset($signup_form_config["suffix_options"]) && $signup_form_config["suffix_options"] != "") ? "dropdownfield" : "textfield",
                "name" => "suffix",
                "value" => ($address && null != $address->getSuffix()) ? $address->getSuffix() : "",
                "required" => ($signup_form_config["suffix_show"] == "req") ? "1" : "0",
                "validation" => "",
                "group_items" => $group_items
            );
        }
        $form_fields["shipping_address_items"][] = array(
            "label" => __("Company"),
            "type" => "textfield",
            "name" => "company",
            "value" => ($address && null != $address->getCompany()) ? $address->getCompany() : "",
            "required" => "0",
            "validation" => "",
            "group_items" => array()
        );
        $form_fields["shipping_address_items"][] = array(
            "label" => __("Address"),
            "type" => "textfield",
            "name" => "address_1",
            "value" => ($address && isset($address->getStreet()[0]) && null != $address->getStreet()[0]) ? $address->getStreet()[0] : "",
            "required" => "1",
            "validation" => "",
            "group_items" => array()
        );
        $form_fields["shipping_address_items"][] = array(
            "label" => __("Address 2"),
            "type" => "textfield",
            "name" => "address_2",
            "value" => ($address && $address && isset($address->getStreet()[1]) && null != $address->getStreet()[1]) ? $address->getStreet()[1] : "",
            "required" => "0",
            "validation" => "",
            "group_items" => array()
        );
        $form_fields["shipping_address_items"][] = array(
            "label" => __("City"),
            "type" => "textfield",
            "name" => "city",
            "value" => ($address && null != $address->getCity()) ? $address->getCity() : "",
            "required" => "1",
            "validation" => "",
            "group_items" => array()
        );
        $form_fields["shipping_address_items"][] = array(
            "label" => __("Country"),
            "type" => "dropdownfield",
            "name" => "country",
            "value" => ($address && null != $address->getCountryId()) ? $address->getCountryId() : "",
            "required" => "1",
            "validation" => "",
            "group_items" => array()
        );
        $default_country_id = ($address && null != $address->getCountryId()) ? $address->getCountryId() : $this->getDefaultCountryId();
        $regions = $this->appGetRegions($default_country_id);
        $form_fields["shipping_address_items"][] = array(
            "label" => __("State/Province"),
            "type" => (count($regions) > 0) ? "dropdownfield" : "textfield",
            "name" => (count($regions) > 0) ? "region_id" : "region",
            "value" => (count($regions) > 0) ? ($address && null != $address->getRegionId()) ? $address->getRegionId() : "" : ($address && null != $address->getRegion()) ? $address->getRegion() : "",
            "required" => "1",
            "validation" => "",
            "group_items" => array()
        );
        $form_fields["shipping_address_items"][] = array(
            "label" => __("Zip/Postal Code"),
            "type" => "textfield",
            "name" => "postcode",
            "value" => ($address && null != $address->getPostcode()) ? $address->getPostcode() : "",
            "required" => $this->getPostcodeRequired($default_country_id),
            "validation" => "",
            "group_items" => array()
        );
        $form_fields["shipping_address_items"][] = array(
            "label" => __("Telephone"),
            "type" => "textfield",
            "name" => "mobile_no",
            "value" => ($address && null != $address->getTelephone()) ? $address->getTelephone() : "",
            "required" => "1",
            "validation" => "",
            "group_items" => array()
        );
        $form_fields["shipping_address_items"][] = array(
            "label" => __("Fax"),
            "type" => "textfield",
            "name" => "fax",
            "value" => ($address && null != $address->getFax()) ? $address->getFax() : "",
            "required" => "0",
            "validation" => "",
            "group_items" => array()
        );
        $form_fields["countries"] = $this->getCountriesList();
        $form_fields["default_country_id"] = $default_country_id;
        

        $form_fields["SID"] = $this->getSid();
        $form_fields["version"] = $this->dataHelper->getVersion();
        $form_fields["install_module"] = "";
        $this->dataHelper->logresponse($this->sp_request, 'appGetAddressForm', $form_fields);
        return $form_fields;
    }
    
    protected function getDefaultCountryId()
    {
        $store = $this->sp_storeManager->getWebsite(true)->getDefaultGroup()->getDefaultStore();
        $session_country_billing = $this->quote->getQuote()->getBillingAddress()->getCountryId();
        $defaultCountryId = isset($session_country_billing) ? $session_country_billing : $this->sp_scopeConfig->getValue('general/country/default', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId());
        return $defaultCountryId;
    }

    public function getCountriesList()
    {
        $countries = $this->getCountries();
        $countries_array = array();
        foreach ($countries as $ctr) {
            if(empty($ctr['value'])) continue;
            $countries_array[] = array('id' => $ctr['value'], 'name' => $ctr['label'], 'postcode_required' => $this->getPostcodeRequired($ctr['value']));
        }
        return $countries_array;
    }
    
    protected function getPostcodeRequired($country_id)
    {
        $optional_postcode_countries = $this->sp_scopeConfig->getValue(
                                'general/country/optional_zip_countries',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                            );
        if ($optional_postcode_countries) {
            $optional_postcode = explode(",", $optional_postcode_countries);
            if (in_array($country_id, $optional_postcode)) {
                return "0";
            } else {
                return "1";
            }
        } else {
            return "1";
        }
    }
    
    public function getCountryCollection()
    {
        $collection = $this->_countryCollectionFactory->create()->loadByStore();
        return $collection;
    }

    /**
     * Retrieve list of top destinations countries
     *
     * @return array
     */
    protected function getTopDestinations()
    {
        $destinations = (string)$this->sp_scopeConfig->getValue(
            'general/country/destinations',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return !empty($destinations) ? explode(',', $destinations) : [];
    }

    /**
     * Retrieve list of countries in array option
     *
     * @return array
     */
    public function getCountries()
    {
        return $options = $this->getCountryCollection()
                ->setForegroundCountries($this->getTopDestinations())
                    ->toOptionArray();
    }
    
    public function appGetRegions($countryID = null)
    {
        $country_id = $this->sp_request->getParam('country_id');
        if (isset($country_id)) {
            if ($country_id != "") {
                try {
                    $regions_modified = array();
                    
                    $regions = $this->sp_objectManager->create('Magento\Directory\Model\ResourceModel\Region\Collection')
                            ->addCountryFilter($country_id);
                    foreach ($regions as $region) {
                        $regions_modified[] = array("state_id" => $region["region_id"], "name" => $region["name"], 'country_id' => $country_id);
                    }
                    $postcode_required = $this->getPostcodeRequired($country_id);
                    $states = array("zipcode_required" => $postcode_required, "states" => $regions_modified, "dni_required" => "0");
                    $states["install_module"] = '';
                    $this->dataHelper->logresponse($this->sp_request, 'getRegions', $states);
                    return $states;
                } catch (\Exception $ex) {
                    $states = array("status" => "failure", "message" => $ex->getMessage());
                    $states["install_module"] = '';
                    $this->dataHelper->logresponse($this->sp_request, 'appGetRegions', $states);
                    return $states;
                }
            }
        } elseif ($countryID != null) {
            if ($countryID != "") {
                $regions = $this->sp_objectManager->create('Magento\Directory\Model\ResourceModel\Region\Collection')->addCountryFilter($countryID);
                return $regions;
            }
        } else {
            $states = array("status" => "failure", "message" => __("Please select a country."));
            $states["install_module"] = $this->install_module();
            $this->dataHelper->logresponse($this->sp_request, 'appGetRegions', $states);
            return $states;
        }
    }
    
    public function appGetCountries()
    {
        
        $countries_array = $this->getCountriesList();
        
        $countries_array["SID"] = $this->getSid();
        $countries_array["version"] = $this->dataHelper->getVersion();
        $countries_array["session_data"] = "";
        $countries_array["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appGetCountries', $countries_array);
        return $countries_array;
    }
    
    public function appGetRegistrationForm()
    {
        $signup_form_config = $this->sp_scopeConfig->getValue(
            'customer/address',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $form_fields = array();
        if (isset($signup_form_config["prefix_show"]) && $signup_form_config["prefix_show"]) {
            $group_items = array();
            if (isset($signup_form_config["prefix_options"]) && $signup_form_config["prefix_options"] != "") {
                $options_dropdown = array_combine(explode(";", $signup_form_config["prefix_options"]), explode(";", $signup_form_config["prefix_options"]));
                foreach ($options_dropdown as $name => $label) {
                    $form_fields["signup_details"]["titles"][] = array("id" => $name, "label" => $label, "name" => "prefix");
                }
            }
        }
        $form_fields["signup_details"]["dob"] = "";
        $form_fields["signup_details"]["email"] = "";
        $form_fields["signup_details"]["firstname"] = "";
        $form_fields["signup_details"]["lastname"] = "";
        $form_fields["signup_details"]["password"] = "";

        $form_fields["SID"] = $this->getSid();
        $form_fields["version"] = $this->dataHelper->getVersion();
        $form_fields["session_data"] = "";
        $form_fields["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appGetRegistrationForm', $form_fields);
        return $form_fields;
    }
    
    public function appCheckout($array_output = false)
    {
        $session = $this->customerSession;
        $customer = $session->getCustomerDataObject();
        
        if ($customer) {
            $this->quote->getQuote()->assignCustomer($customer);
        }
        $hasBillingError = false;

        $default_billing = $this->quote->getQuote()->getBillingAddress();
        $billing_addr = $default_billing->getData();
        $customer = $this->customerSession;
        
        if ($customer && !isset($billing_addr["country_id"])) {
            try{
                $default_billing = $customer->getDefaultBillingAddress();
            }catch (NoSuchEntityException $e) {
            }
        }
        $default_shipping = $this->quote->getQuote()->getShippingAddress();
        $shipping_addr = $default_shipping->getData();
        if ($customer && !isset($shipping_addr["country_id"])) {
            try{
                $default_shipping = $customer->getDefaultShippingAddress();
            }catch (NoSuchEntityException $e) {
            }
        }

        /* To fix the previously logged in customer address issue */
        
        if (isset($billing_addr['email']) && $billing_addr['email'] != $customer->getEmail()) {
            try{
                $default_billing = $customer->getDefaultBillingAddress();
            }catch (NoSuchEntityException $e) {
            }
        }
        
        if (isset($shipping_addr['email']) && $shipping_addr['email'] != $customer->getEmail()) {
            try{
                $default_shipping = $customer->getDefaultShippingAddress();
            }catch (NoSuchEntityException $e) {
            }
        }
        
        $checkout_repsonse = array();
        if ($post_ship_addr_id = $this->sp_request->getParam("id_shipping_address")) {
            $default_shipping = $this->sp_objectManager->create('Magento\Customer\Model\Address')->load($post_ship_addr_id);
        }
        if ($post_bill_addr_id = $this->sp_request->getParam("id_billing_address")) {
            $default_billing = $this->sp_objectManager->create('Magento\Customer\Model\Address')->load($post_bill_addr_id);
        }
        
        
        
        if ((!$default_billing || !$default_shipping)) {
            
            $customer = $this->customerSession;
            if (!$customer->isLoggedIn()) {
                $hasBillingError = true;
                $checkout_repsonse["status"] = "failure";
                $checkout_repsonse["message"] = __("Customer is not looged in");
                $checkout_repsonse["SID"] = $this->getSid();
                $checkout_repsonse["version"] = $this->dataHelper->getVersion();
                $checkout_repsonse["session_data"] = "";
                $checkout_repsonse["install_module"] = '';
                $this->dataHelper->logresponse($this->sp_request, 'AppReviewCartPage', $checkout_repsonse);
                return $checkout_repsonse;
            }
            if ($customer->isLoggedIn()) {
                $addresses = array();
                foreach ($customer->getCustomer()->getAddresses() as $address) {
                    $region = array();
                    if (!is_null($address->getRegionId())) {
                        $region = $this->sp_objectManager->create('Magento\Directory\Model\ResourceModel\Region\Collection')
                        ->addFieldToFilter('main_table.region_id', ['eq' => $address->getRegionId()])
                        ->getFirstItem();
                    }
                    $addresses[] = array(
                        'id_shipping_address' => $address->getId(),
                    );
                }
            }
            if ($ship_addr_id = $addresses[0]["id_shipping_address"]) {
                $default_shipping = $this->sp_objectManager->create('Magento\Customer\Model\Address')->load($ship_addr_id);
            }
            if ($bill_addr_id = $addresses[0]["id_shipping_address"]) {
                $default_billing = $this->sp_objectManager->create('Magento\Customer\Model\Address')->load($bill_addr_id);
            }
            if ((!$default_billing || !$default_shipping) && $this->customerSession->isLoggedIn()) {
                $hasBillingError = true;
                $checkout_repsonse["status"] = "failure";
                $checkout_repsonse["message"] = __("Address not selected.");
                $checkout_repsonse["SID"] = $this->getSid();
                $checkout_repsonse["version"] = $this->dataHelper->getVersion();
                $checkout_repsonse["session_data"] = "";
                $checkout_repsonse["install_module"] = '';
                $this->dataHelper->logresponse($this->sp_request, 'AppReviewCartPage', $checkout_repsonse);
                return $checkout_repsonse;
            }
        }

        if (!$hasBillingError) {
        $billing_address = $default_billing->getData();
        $shipping_address = $default_shipping->getData();
        //problem is default address is not selected
        $form_config = $this->sp_scopeConfig->getValue(
            'customer/address',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $id_address = "0";
        if (isset($billing_address["entity_id"]) && $billing_address["entity_id"]) {
            $id_address = $billing_address["entity_id"];
        } elseif (isset($billing_address["customer_address_id"]) && $billing_address["customer_address_id"]) {
            $id_address = $billing_address["customer_address_id"];
        }
        $checkout_bill_address["id_shipping_address"] = $id_address;
        $checkout_bill_address["alias"] = "";
        $checkout_bill_address["firstname"] = $billing_address["firstname"];
        if (isset($form_config["middlename_show"]) && $form_config["middlename_show"]) {
            $checkout_bill_address["middlename"] = $billing_address["middlename"];
        }
        $checkout_bill_address["lastname"] = $billing_address["lastname"];

        $checkout_bill_address["company"] = $billing_address["company"];

        $bill_street_explode = explode(PHP_EOL, $billing_address["street"]);
        $checkout_bill_address["address_1"] = (isset($bill_street_explode[0]) && null != $bill_street_explode[0]) ? $bill_street_explode[0] : "";
        $checkout_bill_address["address_2"] = (isset($bill_street_explode[1]) && null != $bill_street_explode[1]) ? $bill_street_explode[1] : "";
        $checkout_bill_address["city"] = (null != $billing_address["city"]) ? $billing_address["city"] : '';
        $checkout_bill_address["state"] = (null != $billing_address["region"]) ? $billing_address["region"] : '';
        if (!is_null($billing_address["region_id"])) {
            $region = $this->sp_objectManager->create('Magento\Directory\Model\ResourceModel\Region\Collection')
                        ->addFieldToFilter('main_table.region_id', ['eq' => $billing_address["region_id"]])
                        ->getFirstItem();
            $checkout_bill_address["state"] = (count($region->getData()) > 0) ? $region->getName() : (null != $billing_address["region"]) ? $billing_address["region"] : '';
        }
        $checkout_bill_address["postcode"] = (null != $billing_address["postcode"]) ? $billing_address["postcode"] : '';
        $checkout_bill_address["mobile_no"] = (isset($billing_address["telephone"]) && null != $billing_address["telephone"]) ? $billing_address["telephone"] : "";
        $checkout_bill_address["country_id"] = (null != $billing_address["country_id"]) ? $billing_address["country_id"] : '';
        $checkout_bill_address["country"] = $this->localeLists->getCountryTranslation($billing_address["country_id"]);

        $result_bill = array();
        if ($this->customerSession->isLoggedIn()) {
            $billing_address = $this->quote->getQuote()->getBillingAddress();
            $billing_address_data = null;

            try {
                $billing_address_data = $this->addressRepository->getById($checkout_bill_address["id_shipping_address"]);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                /** Catch Statement */
            }
            if ($billing_address_data->getCustomerId() != $this->quote->getQuote()->getCustomerId()) {
                return ['error' => 1, 'message' => __('The customer address is not valid.')];
            }
            $billing_address->importCustomerAddressData($billing_address_data)->setSaveInAddressBook(0);
            $billing_address->setCollectShippingRates(true)->collectShippingRates();
//            $this->totalsCollector->collectAddressTotals($this->quote->getQuote(), $billing_address);
            $billing_address->setShouldIgnoreValidation(true);
            $billing_address->save();
            $this->quote->getQuote()->save();
//            $result_bill = $this->sp_objectManager->create('Magento\Checkout\Model\Type\Onepage')->saveBilling($billing_address, $checkout_bill_address["id_shipping_address"]);
        }
        $id_address = "0";
        if (isset($shipping_address["entity_id"]) && $shipping_address["entity_id"]) {
            $id_address = $shipping_address["entity_id"];
        } elseif (isset($shipping_address["customer_address_id"]) && $shipping_address["customer_address_id"]) {
            $id_address = $shipping_address["customer_address_id"];
        }

        $checkout_ship_address["id_shipping_address"] = $id_address;
        $checkout_ship_address["alias"] = "";
        $checkout_ship_address["firstname"] = $shipping_address["firstname"];
        if (isset($form_config["middlename_show"]) && $form_config["middlename_show"]) {
            $checkout_ship_address["middlename"] = $shipping_address["middlename"];
        }
        $checkout_ship_address["lastname"] = $shipping_address["lastname"];
        $ship_street_explode = explode(PHP_EOL, $shipping_address["street"]);
        $checkout_ship_address["address_1"] = isset($ship_street_explode[0]) ? $ship_street_explode[0] : "";
        $checkout_ship_address["address_2"] = isset($ship_street_explode[1]) ? $ship_street_explode[1] : "";

        $checkout_ship_address["company"] = (null != $shipping_address["company"]) ? $shipping_address["company"] : '';
        $checkout_ship_address["city"] = (null != $shipping_address["city"]) ? $shipping_address["city"] : '';
        $checkout_ship_address["state"] = (null != $shipping_address["region"]) ? $shipping_address["region"] : '';
        if (!is_null($shipping_address["region_id"])) {
            $region = $this->sp_objectManager->create('Magento\Directory\Model\ResourceModel\Region\Collection')
                        ->addFieldToFilter('main_table.region_id', ['eq' => $shipping_address["region_id"]])
                        ->getFirstItem();
            $checkout_ship_address["state"] = (count($region->getData()) > 0) ? $region->getName() : (null != $shipping_address["region"]) ? $shipping_address["region"] : '';
        }
        $checkout_ship_address["postcode"] = (null != $shipping_address["postcode"]) ? $shipping_address["postcode"] : '';
        $checkout_ship_address["mobile_no"] = (isset($shipping_address["telephone"]) && null != $shipping_address["telephone"] ) ? $shipping_address["telephone"] : "";
        $checkout_ship_address["country"] = $this->localeLists->getCountryTranslation($shipping_address["country_id"]);

        $result_ship = array();
        if ($this->customerSession->isLoggedIn()) {
            $result_ship = $this->sp_objectManager->create('Magento\Checkout\Model\Type\Onepage')->saveShipping($shipping_address, $checkout_ship_address["id_shipping_address"]);
        }
        if ((isset($result_bill["error"]) && $result_bill["error"] == "1") ||
                (isset($result_ship["error"]) && $result_ship["error"] == "1") ||
                (isset($result_bill["error"]) && $result_bill["error"] == "-1") ||
                (isset($result_ship["error"]) && $result_ship["error"] == "-1")) {
            $checkout_repsonse["status"] = "failure";
            $checkout_repsonse["message"] = isset($result_bill["message"]) ? $result_bill["message"] : isset($result_ship["message"]) ? $result_ship["message"] : "";
        }      
        
        $session = $this->quote;
                $quote = $session->getQuote();
                $quote->collectTotals()->save();
        
        $rates = $this->_rateCollectionFactory->create()->setAddressFilter($this->quote->getQuote()->getShippingAddress()->getId())->toArray();
        $shipping_methods = array();

        if (isset($rates['items']) && $rates['items']) {
            foreach ($rates['items'] as $_rate) {
                $shipping_methods[] = array(
                    "name" => $_rate['method_title'],
                    "delay_text" => "",
                    "code" => $_rate['code'],
                    "price" => $this->formatPrice($_rate['price'], true, false)
                );
            }
        }

        

        $selected_shipping_method = $this->quote->getQuote()->getShippingAddress()->getShippingMethod();

        if ($selected_shipping_method == '' || $selected_shipping_method === NULL) {
            if ($selected_shipping_method = $this->sp_request->getParam("shipping_method")) {
                $session = $this->quote;
                $quote = $session->getQuote();
                $quote->getShippingAddress()->setShippingMethod($selected_shipping_method)->save();
                $quote->collectTotals()->save();
            }else if (isset($shipping_methods[0]["code"])) {
                $session = $this->quote;
                $quote = $session->getQuote();
                $selected_shipping_method = $shipping_methods[0]["code"];
                $quote->getShippingAddress()->setShippingMethod($selected_shipping_method)->save();
                $quote->collectTotals()->save();
            }
        }

        $checkout_repsonse["checkout_page"] = $this->appGetCartDetails(true, false);
        unset($checkout_repsonse["checkout_page"]["delay_shipping"]);
        unset($checkout_repsonse["checkout_page"]["gift_wrapping"]);
        unset($checkout_repsonse["checkout_page"]["voucher_html"]);
        unset($checkout_repsonse["checkout_page"]["checkout_page"]);
        unset($checkout_repsonse["checkout_page"]["per_products_shipping"]);
        unset($checkout_repsonse["checkout_page"]["install_module"]);
        unset($checkout_repsonse["checkout_page"]["version"]);
        unset($checkout_repsonse["checkout_page"]["SID"]);
        unset($checkout_repsonse["checkout_page"]["cart_id"]);

        $checkout_repsonse["checkout_page"]["billing_address"] = $checkout_bill_address;
        $checkout_repsonse["checkout_page"]["shipping_address"] = $checkout_ship_address;
        $checkout_repsonse["gift_wrapping"] = array("applied" => "0", "available" => "0", "cost_text" => "", "message" => "");
        $checkout_repsonse["checkout_page"]["per_products_shipping"] = "0";
        $checkout_repsonse["checkout_page"]["shipping_available"] = "1";
        $checkout_repsonse["checkout_page"]["per_products_shipping_methods"] = array();
        $checkout_repsonse["checkout_page"]["shipping_message"] = "";
        $checkout_repsonse["message"] = __("Cart information loaded successfully");
        $checkout_repsonse["status"] = "success";
        $this->quote->getQuote()->collectTotals();
        $this->quote->getQuote()->save();

        if ($this->quote->getQuote()->isVirtual()) {
            $checkout_repsonse["checkout_page"]["shipping_available"] = "1";
            $checkout_repsonse["checkout_page"]["shipping_message"] = __("No Shipping Method Required");
        }

        if (!$this->quote->getQuote()->isVirtual() && count($shipping_methods) == 0) {
            $checkout_repsonse["checkout_page"]["shipping_available"] = "0";
            $checkout_repsonse["checkout_page"]["shipping_message"] = __("No Shipping Method Available");
        }
        $checkout_repsonse["checkout_page"]["shipping_methods"] = $shipping_methods;
        $checkout_repsonse["checkout_page"]["default_shipping"] = $selected_shipping_method;
        $checkout_repsonse["total_cost"] = $this->quote->getQuote()->getGrandTotal();
        $checkout_repsonse["currency_code"] = $this->sp_storeManager->getStore()->getCurrentCurrencyCode();
        $checkout_repsonse["currency_symbol"] = $this->_localeFormat->getCurrency($this->sp_storeManager->getStore()->getCurrentCurrencyCode())->getSymbol();

        if ($array_output) {
            return $checkout_repsonse;
        }
        
        $checkout_repsonse["SID"] = $this->getSid();
        $checkout_repsonse["version"] = $this->dataHelper->getVersion();
        $checkout_repsonse["session_data"] = "";
        $checkout_repsonse["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appCheckout', $checkout_repsonse);
        return $checkout_repsonse;
        }
    }
    
    public function appSetShippingMethod()
    {

        if ($this->sp_request->getParam("shipping_method")) {
            $session = $this->quote;
            $quote = $session->getQuote();
            try {
                $selected_shipping_method = $this->sp_request->getParam("shipping_method");
                $quote->getShippingAddress()->setShippingMethod($selected_shipping_method)->save();
                $quote->collectTotals()->save();
                $response = $this->appCheckout(true);
                $response["status"] = "success";
                $response["message"] = __("Shipping method was set successfully.");
            } catch (\Exception $ex) {
                $response["status"] = "failure";
                $response["message"] = $ex->getMessage();
            }
        } else {
            $response["status"] = "failure";
            $response["message"] = __('An error occurred while selecting shipping method.');
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appSetShippingMethod', $response);
        return $response;
    }
    
    public function appGetCustomerAddress()
    {
        $response = array();
        $customer = $this->customerSession;
        if ($customer->isLoggedIn()) {
            $addresses = array();
            foreach ($customer->getCustomer()->getAddresses() as $address) {
                $region = array();
                if (!is_null($address->getRegionId())) {
                    $region = $this->sp_objectManager->create('Magento\Directory\Model\ResourceModel\Region\Collection')
                        ->addFieldToFilter('main_table.region_id', ['eq' => $address->getRegionId()])
                        ->getFirstItem();
                }
                $addresses[] = array(
                    'alias' => "",
                    'id_shipping_address' => $address->getId(),
                    'firstname' => (null != $address->getFirstname()) ? $address->getFirstname() : '',
                    'lastname' => (null != $address->getLastname()) ? $address->getLastname() : '',
                    'mobile_no' => (null != $address->getTelephone()) ? $address->getTelephone() : '',
                    'company' => (null != $address->getCompany()) ? $address->getCompany() : '',
                    'address_1' => (isset($address->getStreet()[0]) && null != $address->getStreet()[0]) ? $address->getStreet()[0] : "",
                    'address_2' => (isset($address->getStreet()[1]) && null != $address->getStreet()[1]) ? $address->getStreet()[1] : "",
                    "city" => (null != $address->getCity()) ? $address->getCity() : '',
                    "state" => (count($region->getData()) > 0) ? $region->getName() : (null != $address->getRegion()) ? $address->getRegion() : '',
                    "country" => $this->localeLists->getCountryTranslation($address->getCountry()),
                    "postcode" => (null != $address->getPostcode()) ? $address->getPostcode() : '',
                );
            }
            $response["shipping_address"] = $addresses;
            $default_billing = $customer->getCustomer()->getDefaultBillingAddress();
            $default_shipping = $customer->getCustomer()->getDefaultShippingAddress();
            if (!$default_billing || !$default_shipping) {
                $response["default_address"] = "0";
            } else {
                $response["default_address"] = "1";
            }
        } else {
            $response["status"] = "failure";
            $response["message"] = __("Please login to view your addresses.");
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appGetCustomerAddress', $response);
        return $response;
    }

    public function appAddAddress()
    {
        $response = array();
        if ($this->sp_request->isPost()) {
            $json_data = $this->sp_request->getParam("shipping_address");

            if ($post_data = json_decode($json_data, true)) {
                if ($this->customerSession->isLoggedIn()) {
                    $customer = $this->customerSession->getCustomer();                  
                    $addresss = $this->sp_objectManager->get('\Magento\Customer\Model\AddressFactory');

                    $address = $addresss->create();
                    $addressForm = $this->_formFactory->create(
                        'customer_address',
                        'customer_address_edit',
                        [],
                        $this->sp_request->isAjax(),
                        Form::IGNORE_INVISIBLE,
                        []
                    );
                    $post_data["street"] = array($post_data["address_1"], $post_data["address_2"]);
                    $post_data["country_id"] = $post_data["country"];
                    $post_data["telephone"] = $post_data["mobile_no"];
                    $addressForm->compactData($post_data);
                    $addressErrors = $addressForm->validateData($post_data);

                    $address_form_config = $this->sp_scopeConfig->getValue('customer/address',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    if ($addressErrors === true) {
                        $address->setCustomerId($customer->getId());
                        $address->setFirstname($post_data["firstname"]);
                        if (isset($address_form_config["middlename_show"]) && $address_form_config["middlename_show"]) {
                            $address->setMiddleName($post_data["middlename"]);
                        }
                        $address->setLastname($post_data["lastname"]);
                        $address->setPostcode($post_data["postcode"]);
                        $address->setCity($post_data["city"]);
                        $address->setTelephone($post_data["mobile_no"]);
                        $address->setFax($post_data["fax"]);
                        $address->setCountryId($post_data["country"]);
                        if (isset($post_data["region_id"]) && $post_data["region_id"]) {
                            $address->setRegionId($post_data["region_id"]);
                        }
                        if (isset($post_data["region"]) && $post_data["region"]) {
                            $address->setRegion($post_data["region"]);
                        }
                        if (isset($post_data["suffix"]) && $post_data["suffix"]) {
                            $address->setSuffix($post_data["suffix"]);
                        }
                        if (isset($post_data["gender"]) && $post_data["gender"]) {
                            $address->setGender($post_data["gender"]);
                        }
                        if (isset($post_data["taxvat"]) && $post_data["taxvat"]) {
                            $address->setTaxvat($post_data["taxvat"]);
                        }
                        if (isset($post_data["dob"]) && $post_data["dob"]) {
                            $address->setDob($this->date->date(null, $post_data['dob']));
                        }
                        $address->setCompany($post_data["company"]);
                        $address->setStreet($post_data["street"]);
                        $address->setSaveInAddressBook('1');
                        try {
                            if (isset($post_data["id_shipping_address"]) && $post_data["id_shipping_address"]) {
                                $address->setId($post_data["id_shipping_address"]);
                            }
                            $address->save();
                            $saved_id = $address->getId();
                            $response["id_shipping_address"] = $saved_id;

                            $customerObj = $this->customer->load($customer->getId());
                            if (!$customerObj->getDefaultBillingAddress()) {
                                foreach ($customerObj->getAddresses() as $address) {
                                    $address->setIsDefaultBilling(true);
                                    continue; // we only want to set first address of the customer as default billing address
                                }
                            }
                            if (!$customerObj->getDefaultShippingAddress()) {
                                foreach ($customerObj->getAddresses() as $address) {
                                    $address->setIsDefaultShipping(true);
                                    continue; // we only want to set first address of the customer as default shipping address
                                }
                            }
                            $address->save();

                            $response["shipping_address_count"] = count($this->customerSession->getCustomer()->getAddresses());
                            $response["cart_id"] = "0";

                            $response["shipping_address_reponse"]["status"] = "success";
                            if (isset($post_data["id_shipping_address"]) && $post_data["id_shipping_address"]) {
                                $response["shipping_address_reponse"]["message"] = __("Address has been updated successfully.");
                            } else {
                                $response["shipping_address_reponse"]["message"] = __("Address has been added successfully.");
                            }
                        } catch (\Exception $e) {
                            $response["shipping_address_reponse"]["status"] = "failure1";
                            $response["shipping_address_reponse"]["message"] = $e->getMessage();
                        }
                    } else {
                        $response["shipping_address_reponse"]["status"] = "failure2";
                        foreach ($addressErrors as $errors) {
                            $response["shipping_address_reponse"]["message"][] = $errors;
                        }
                    }
                } else {
                    $response["shipping_address_reponse"]["status"] = "failure";
                    $response["shipping_address_reponse"]["message"] = __("Please login to add/update your addresses.");
                }
            } else {
                $response["shipping_address_reponse"]["status"] = "failure3";
                $response["shipping_address_reponse"]["message"] = __("Something went wrong.");
            }
        } else {
            $response["shipping_address_reponse"]["status"] = "failure";
            $response["shipping_address_reponse"]["message"] = __("Something went wrong.");
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appAddAddress', $response);
        return $response;
    }

    public function appUpdateAddress()
    {
        $response = array();
        if ($this->sp_request->isPost()) {
            $json_data = $this->sp_request->getParam("shipping_address");
            $id_shipping_address = $this->sp_request->getParam("id_shipping_address");
            if ($post_data = json_decode($json_data, true)) {
                if ($this->customerSession->isLoggedIn()) {
                    $customer = $this->customerSession->getCustomer();
                    $addresss = $this->sp_objectManager->get('\Magento\Customer\Model\AddressFactory');

                    $address = $addresss->create();

                    $addressForm = $this->_formFactory->create(
                        'customer_address',
                        'customer_address_edit',
                        [],
                        $this->sp_request->isAjax(),
                        Form::IGNORE_INVISIBLE,
                        []
                    );
                    $post_data["street"] = array($post_data["address_1"], $post_data["address_2"]);
                    $post_data["country_id"] = $post_data["country"];
                    $post_data["telephone"] = $post_data["mobile_no"];
                     $addressForm->compactData($post_data);
                    $addressErrors = $addressForm->validateData($post_data);
                    $address_form_config = $this->sp_scopeConfig->getValue(            
                            'customer/address',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    if ($addressErrors === true) {
                        $address->setCustomerId($customer->getId());
                        $address->setFirstname($post_data["firstname"]);
                        if (isset($address_form_config["middlename_show"]) && $address_form_config["middlename_show"]) {
                            $address->setMiddleName($post_data["middlename"]);
                        }
                        $address->setLastname($post_data["lastname"]);
                        $address->setPostcode($post_data["postcode"]);
                        $address->setCity($post_data["city"]);
                        $address->setTelephone($post_data["telephone"]);
                        $address->setFax($post_data["fax"]);
                        $address->setCountryId($post_data["country"]);
                        if (isset($post_data["region_id"]) && $post_data["region_id"]) {
                            $address->setRegionId($post_data["region_id"]);
                        }
                        if (isset($post_data["region"]) && $post_data["region"]) {
                            $address->setRegion($post_data["region"]);
                        }
                        if (isset($post_data["suffix"]) && $post_data["suffix"]) {
                            $address->setSuffix($post_data["suffix"]);
                        }
                        if (isset($post_data["gender"]) && $post_data["gender"]) {
                            $address->setGender($post_data["gender"]);
                        }
                        if (isset($post_data["taxvat"]) && $post_data["taxvat"]) {
                            $address->setTaxvat($post_data["taxvat"]);
                        }
                        if (isset($post_data["dob"]) && $post_data["dob"]) {
                            $address->setDob($this->date->date(null, $post_data['dob']));
                        }
                        $address->setCompany($post_data["company"]);
                        $address->setStreet($post_data["street"]);
                        $address->setSaveInAddressBook('1');
                        try {
                            if (isset($id_shipping_address) && $id_shipping_address) {
                                $address->setId($id_shipping_address);
                            }
                            $address->save();
                            $saved_id = $address->getId();
                            $response["id_shipping_address"] = $saved_id;

                            $response["shipping_address_count"] = count($this->customerSession->getCustomer()->getAddresses());
                            $response["cart_id"] = "0";
                            $response["shipping_address_reponse"]["status"] = "success";
                            if (isset($id_shipping_address) && $id_shipping_address) {
                                $response["shipping_address_reponse"]["message"] = __("Address has been updated successfully.");
                            } else {
                                $response["shipping_address_reponse"]["message"] = __("Address has been added successfully.");
                            }
                        } catch (Exception $e) {
                            $response["status"] = "failure";
                            $response["message"] = $e->getMessage();
                        }
                    } else {
                        $response["status"] = "failure";
                        foreach ($addressErrors as $errors) {
                            $response["message"][] = $errors;
                        }
                    }
                } else {
                    $response["status"] = "failure";
                    $response["message"] = __("Please login to add/update your addresses.");
                }
            } else {
                $response["status"] = "failure";
                $response["message"] = __("Something went wrong.");
            }
        } else {
            $response["status"] = "failure";
            $response["message"] = __("Something went wrong.");
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appUpdateAddress', $response);
        return $response;
    }

    public function appSetBillingAddress()
    {
        $response = array();
        if ($this->sp_request->isPost()) {

            $customerAddressId = $this->sp_request->getParam('id_billing_address', array());

            $post_data = array();
            $post_data["test"] = 'mobileappbuilder';
            $billing_address = $this->quote->getQuote()->getBillingAddress();
            $billing_address_data = null;

            try {
                $billing_address_data = $this->addressRepository->getById($customerAddressId);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $response["status"] = "failure";
                $response["message"] = $e->getMessage();
            }
            if ($billing_address_data->getCustomerId() != $this->quote->getQuote()->getCustomerId()) {
                $response["status"] = "failure";
                $response["message"] = __('The customer address is not valid.');
            }
            $billing_address->importCustomerAddressData($billing_address_data)->setSaveInAddressBook(0);
            $billing_address->setCollectShippingRates(true)->collectShippingRates();
            $billing_address->setShouldIgnoreValidation(true);
            $billing_address->save();
            $this->quote->getQuote()->save();
            
            $shipping_address = $this->quote->getQuote()->getShippingAddress();
            $shipping_address_data = null;

            try {
                $shipping_address_data = $this->addressRepository->getById($customerAddressId);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $response["status"] = "failure";
                $response["message"] = $e->getMessage();
            }
            if ($shipping_address_data->getCustomerId() != $this->quote->getQuote()->getCustomerId()) {
                $response["status"] = "failure";
                $response["message"] = __('The customer address is not valid.');
            }
            $shipping_address->importCustomerAddressData($shipping_address_data)->setSaveInAddressBook(0);
            $shipping_address->setCollectShippingRates(true)->collectShippingRates();
            $shipping_address->setShouldIgnoreValidation(true);
            $shipping_address->save();
            $this->quote->getQuote()->save();
            
            if(!isset($response['status'])) {
                $response = $this->appCheckout(true);
                $response["status"] = "success";
                $response["message"] = "";
            }
        } else {
            $response["status"] = "failure";
            $response["message"] = __('An error occurred while saving billing address.');
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appSetBillingAddress', $response);
        return $response;
    }

    public function appSetShippingAddress()
    {
        $response = array();

        if ($this->sp_request->isPost()) {
            $customerAddressId = $this->sp_request->getParam('id_shipping_address', array());
            $post_data = array();
            $post_data["test"] = 'mobileappbuilder';
            
            $shipping_address = $this->quote->getQuote()->getShippingAddress();
            $shipping_address_data = null;

            try {
                $shipping_address_data = $this->addressRepository->getById($customerAddressId);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $response["status"] = "failure";
                $response["message"] = $e->getMessage();
            }
            if ($shipping_address_data->getCustomerId() != $this->quote->getQuote()->getCustomerId()) {
                $response["status"] = "failure";
                $response["message"] = __('The customer address is not valid.');
            }
            $shipping_address->importCustomerAddressData($shipping_address_data)->setSaveInAddressBook(0);
            $shipping_address->setCollectShippingRates(true)->collectShippingRates();
            $shipping_address->setShouldIgnoreValidation(true);
            $shipping_address->save();
            $this->quote->getQuote()->save();
            if (!isset($response["status"])) {
                $response = $this->appCheckout(true);
                $response["status"] = "success";
                $response["message"] = "";
            }
        } else {
            $response["status"] = "failure";
            $response["message"] = __('An error occurred while saving shipping address.');
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appSetShippingAddress', $response);
        return $response;
    }
    
    /*
     * Function to get payment methods data configured for mobile app
     */

    public function appGetMobilePaymentMethods()
    {
        $response = array();
        $payment_array = array();
        $payment_method_collection = $this->paymentModel->getCollection();
        $payment_method_data = $payment_method_collection->getData();
        if ($payment_method_data) {
            foreach ($payment_method_data as $data) {
                if ($data['status'] == '1') {
                    $config_data = json_decode($data['values'],true);
                    $payment_array[] = array(
                        'payment_method_name' => $data['kb_payment_name'],
                        'payment_method_code' => $data['kb_payment_code'],
                        'configuration' => array(
                            'payment_method_mode' => $config_data['payment_method_mode'],
                            'client_id' => $config_data['client_id'],
                            'is_default' => $config_data['is_default'],
                            'other_info' => $config_data['other_info']
                        )
                    );
                }
            }
            if (count($payment_array) == 0) {
                $response["status"] = "failure";
                $response["payments"] = "";
                $response["message"] = __('No Payment methods is enabled');
            } else {
                $response["status"] = "success";
                $response["payments"] = $payment_array;
                $response["message"] = "";
            }
        } else {
            $response["status"] = "failure";
            $response["payments"] = "";
            $response["message"] = __('No Payment methods has been configured');
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appGetMobilePaymentMethods', $response);
        return $response;
    }
    
    public function placeOrder()
    {
        $response = array();
        if ($this->sp_request->isPost()) {
            $data = $this->sp_request->getParam("payment");
            $quote = $this->quote->getQuote();
            try {
                if ($quote->isVirtual()) {
                    $quote->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
                } else {
                    $quote->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
                }
                // shipping totals may be affected by payment method
                if (!$quote->isVirtual() && $quote->getShippingAddress()) {
                    $quote->getShippingAddress()->setCollectShippingRates(true);
                }
//                $data['checks'] =  [\Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_CHECKOUT,
//                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_COUNTRY,
//                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_CURRENCY,
//                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX,
//                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_ZERO_TOTAL];
                $quote->setPaymentMethod($data['method']);
                $payment = $quote->getPayment();
                $payment->setQuote($quote);
                $payment->importData($data);
                $quote->collectTotals ();
                $quote->save();
                

                if ($redirectUrl = $quote->getPayment()->getCheckoutRedirectUrl()) {
                    $response["redirect_url"] = $redirectUrl;
                } else {
                    $response["redirect_url"] = "";
                    $this->sp_objectManager->get('Magento\Checkout\Model\Type\Onepage')->saveOrder();
                    if ($redirectUrl = $this->sp_objectManager->get('Magento\Checkout\Model\Type\Onepage')->getCheckout()->getRedirectUrl()) {
                        $response["redirect_url"] = $redirectUrl;
                    } else {
                        $response["redirect_url"] = "";
                    }
                }
                $response["status"] = "success";
            } catch (Exception $ex) {
                $response["status"] = "failure";
                $response["message"] = $ex->getMessage();
            }
        } else {
            $response["status"] = "failure";
            $response["message"] = __("Something went wrong.");
        }
        $this->dataHelper->logresponse($this->sp_request, 'placeOrder', $response);
        return $response;
    }
    
    /*
     * Function to create order from moile app payment methods
     */

    public function appCreateOrder()
    {
        $response = array();
        if ($this->sp_request->isPost()) {
            $payment_method_info = $this->sp_request->getParam("payment_info");
            $payment_method_info = json_decode($payment_method_info, true);
            $data = array(
                'method' => $payment_method_info['payment_method_code']
            );
            $quote = $this->quote->getQuote();
            try {
                if(isset($data['method']) && $data['method'] == 'cod'){
                    $data['method'] = 'cashondelivery';
                }
                if ($quote->isVirtual()) {
                    $quote->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
                } else {
                    $quote->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
                }
                // shipping totals may be affected by payment method
                if (!$quote->isVirtual() && $quote->getShippingAddress()) {
                    $quote->getShippingAddress()->setCollectShippingRates(true);
                }
                $data['checks'] = 0;
                $payment = $quote->getPayment();
                $payment->importData($data);
                $quote->save();
                $checkout = $this->sp_objectManager->get('Magento\Checkout\Model\Type\Onepage')->saveOrder();
                $order_id = $checkout->getCheckout()->getLastOrderId();
                $response["status"] = "success";
                $response["message"] = __('Order created successfully');
                $response["order_id"] = $order_id;
                $session = $this->sp_objectManager->get('Magento\Checkout\Model\Type\Onepage')->getCheckout();
                $lastOrderId = $session->getLastOrderId();
//                $session->clear();
                $this->_eventManager->dispatch('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
                $checkoutSessionQuote = $this->quote->getQuote();
                $checkoutSessionQuote->setIsActive(0)->save();
                $checkoutSessionQuote->delete();
            } catch (\Exception $ex) {
                $response["status"] = "failure";
                $response["message"] = $ex->getMessage();
            }
        } else {
            $response["status"] = "failure";
            $response["message"] = __("Something went wrong.");
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appCreateOrder', $response);
        return $response;
    }
    
    public function appUpdateProfile()
    {
        $response = array();
        if ($this->sp_request->isPost()) {
            $json_login = $this->sp_request->getParam("personal_info");
            $post_data = json_decode($json_login, true);
            if ($this->customerSession->isLoggedIn()) {
                $customer_current = $this->customerSession->getCustomer();
                $email = $customer_current->getEmail();
                $password = $post_data["password"];
                $firstname = isset($post_data["first_name"]) ? $post_data["first_name"] : "";
                $lastname = isset($post_data["last_name"]) ? $post_data["last_name"] : "";
                $prefix = isset($post_data["prefix"]) ? $post_data["prefix"] : "";
                $new_password = isset($post_data["new_password"]) ? $post_data["new_password"] : "";
                $session = $this->customerSession;
                try {
                    $this->customerAccountManagement->authenticate(trim($email), $password);
                    $customer = $this->customer;
                    $customer->setWebsiteId($this->sp_storeManager->getWebsite()->getWebsiteId());
                    $customer->loadByEmail($email);
                    if ($customer->getId()) {
                        $customerId = $customer->getId();
                        $customer = $this->customer->load($customerId);
                        $customer->setEmail($email);
                        if ($prefix) {
                            $customer->setPrefix($prefix);
                        }
                        if ($firstname) {
                            $customer->setFirstname($firstname);
                        }
                        if ($lastname) {
                            $customer->setLastname($lastname);
                        }
                        if ($new_password) {
                            $customer->setPassword($new_password);
                        }

                        try {
                            $customer->save();
                            $customer->setConfirmation(null);
                            $customer->save();
                            $response["status"] = "success";
                            $response["message"] = __("Your information has been updated successfully.");
                        } catch (\Exception $ex) {
                            $response["status"] = "failure";
                            $response["message"] = $ex->getMessage();
                        }
                    }
                } catch (\Exception $ex) {
                    $response["status"] = "failure";
                    $response["message"] = $ex->getMessage();
                }
            }
        }

        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appUpdateProfile', $response);
        return $response;
    }
    
    public function appForgotPassword()
    {
        $response = array();
        if ($this->sp_request->isPost()) {
            $email = (string) $this->sp_request->getParam('email');
            if ($email) {
                if (!\Zend_Validate::is($email, 'EmailAddress')) {
                    $this->customerAccountManagement->initiatePasswordReset($email);
                    $response["status"] = "failure";
                    $response["message"] = __('Invalid email address.');
                }
                /** @var $customer Mage_Customer_Model_Customer */
                if (empty($response)) {
                    $customer = $this->customer;
                    $customer->setWebsiteId($this->sp_storeManager->getWebsite()->getWebsiteId());
                    $customer->loadByEmail($email);

                    if ($customer->getId()) {
                        try {
                            $this->customerAccountManagement->initiatePasswordReset($email, AccountManagement::EMAIL_RESET);
                            $response["status"] = "success";
                            $response["message"] = __('If there is an account associated with your email  you will receive an email with a link to reset your password.');
                        } catch (\Exception $exception) {
                            $response["status"] = "failure";
                            $response["message"] = $exception->getMessage();
                        }
                    }
                }
            } else {
                $response["status"] = "failure";
                $response["message"] = __('Please enter your email.');
            }
        } else {
            $response["status"] = "failure";
            $response["message"] = __("Something went wrong.");
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appForgotPassword', $response);
        return $response;
    }
    
    public function appGetOrders()
    {
        if ($this->customerSession->isLoggedIn()) {
            $customer_config = $this->sp_scopeConfig->getValue(            
                            'customer/address',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);;
            $customer = $this->customerSession->getCustomer();

            if (isset($customer_config["prefix_show"]) && $customer_config["prefix_show"]) {
                $group_items = array();
                if (isset($customer_config["prefix_options"]) && $customer_config["prefix_options"] != "") {
                    $options_dropdown = array_combine(explode(";", $customer_config["prefix_options"]), explode(";", $customer_config["prefix_options"]));
                    foreach ($options_dropdown as $name => $label) {
                        $response["personal_info"]["titles"][] = array(
                            "id" => $label,
                            "label" => $label,
                            "name" => "gender",
                        );
                    }
                }
            } else {
                $response["personal_info"]["titles"] = array();
            }

            if (isset($customer_config["prefix_show"]) && $customer_config["prefix_show"]) {
                $response["personal_info"]["gender"] = $customer->getPrefix();
            }
            $response["personal_info"]["firstname"] = (null !== $customer->getFirstname()) ? $customer->getFirstname() : "";
            if (isset($customer_config["middlename_show"]) && $customer_config["middlename_show"]) {
                $response["personal_info"]["middlename"] = $customer->getMiddlename();
            }
            $response["personal_info"]["lastname"] = (null !== $customer->getLastname()) ? $customer->getLastname() : "";
            $response["personal_info"]["email"] = $customer->getEmail();
            $response["personal_info"]["dob"] = $customer->getDob();
            if (isset($customer_config["gender_show"]) && $customer_config["gender_show"]) {
                $response["personal_info"]["gender"] = $customer->getGender();
            }

            $orders = $this->_orderCollectionFactory->create()
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('customer_id', $this->customerSession->getCustomer()->getId())
//                    ->addFieldToFilter('state', array('in' => $this->_orderConfig->getVisibleOnFrontStatuses()))
                    ->setOrder('created_at', 'desc');
            $myorders = array();
            foreach ($orders as $order) {
                $orderItems = $order->getAllVisibleItems();
                $order_items = array();
                foreach ($orderItems as $item) {
                    // Product options
                    $productOptions = array();
                    $options = $item->getProductOptions();
                    if (!empty($options['attributes_info'])) {
                        $productOptions = $options['attributes_info'];
                    }
                    foreach ($productOptions as $k => $v) {
                        $productOptions[$k]['name'] = $productOptions[$k]['label'];
                        unset($productOptions[$k]['label']);
                    }
                    $stockItemResource = $this->sp_objectManager->get('\Magento\CatalogInventory\Api\StockRegistryInterface');
                    $stock = $stockItemResource->getStockItem($item->getProduct()->getId());
                    $order_items[] = array(
                        'id' => $item->getId(),
                        "id_product_attribute" => "0",
                        'customizable_items' => array(),
                        'is_gift_product' => '0',
                        'stock' => $stock->getData('is_in_stock') ? true : false,
                        'title' => $item->getProduct()->getName(),
                        'price' => $this->formatPrice($item->getProduct()->getPrice(), true, false),
                        'discount_price' => $this->formatPrice($item->getProduct()->getFinalPrice(), true, false),
                        'discount_percentage' => "",
                        'images' => $this->sp_objectManager->get('Magento\Catalog\Helper\Image')->init($item->getProduct(), 'product_base_image')
                                           ->constrainOnly(TRUE)
                                           ->keepAspectRatio(TRUE)
                                           ->keepTransparency(TRUE)
                                           ->keepFrame(FALSE)
                                           ->resize(120, 90)->getUrl(),
                        'quantity' => $item->getQtyOrdered(),
                        'product_items' => $productOptions,
                        'total' => $this->formatPrice($item->getRowTotal(), true, false),
                    );
                }
                $myorders[] = array(
                    "cart_id" => "0",
                    "order_id" => $order->getId(),
                    "order_number" => $order->getIncrementId(),
                    "status" => $order->getStatus(),
                    "status_color" => "#000000",
                    "date_added" => $order->getCreatedAt(),
                    "total" => $this->formatPrice($order->getGrandTotal(), true, false),
                    "reorder_allowed" => $this->sp_scopeConfig->getValue(            
                            'sales/reorder/allow',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                    "products" => $order_items,
                );
            }
            $response["status"] = "success";
            $response["message"] = "";
            $response["order_history"] = $myorders;
            $response["install_module"] = "";
        } else {
            $response["status"] = "failure";
            $response["message"] = __("Please login to view your orders.");
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appGetOrders', $response);
        return $response;
    }
    
    public function appGetOrderDetails()
    {
        if ($this->sp_request->isPost()) {
            if ($order_id = $this->sp_request->getParam("order_id")) {
                if ($this->customerSession->isLoggedIn()) {
                    $order = $this->_orderCollectionFactory->create()
                                ->addFieldToSelect('*')
                                ->addFieldToFilter('entity_id', $order_id)
                                ->addFieldToFilter('customer_id', $this->customerSession->getCustomer()->getId())
                                ->getFirstItem();
                    
                    if ($order->getData()) {
                            $order_data = $order->getData();
                            if (isset($order_data['is_virtual']) && $order_data['is_virtual'] == '1') {
                                $virtual_order = true;
                            } else {
                                $virtual_order = false;
                            }
                            
                            $order_details = array();
                            $order_details = array(
                                "gift_wrapping" => array("applied" => "0", "available" => "0", "cost_text" => "", "message" => ""),
                                "order_comment" => "",
                                "order_history" => $this->getOrderHistory($order),
                                "billing_address" => $this->getOrderBillingAddress($order),
                                "shipping_address" => ($virtual_order) ? null : $this->getOrderShippingAddress($order),
                                "shipping_method" => ($virtual_order) ? null : array("name" => $order->getShippingDescription()),
                                "payment_method" => array("name" => $order->getPayment()->getMethodInstance()->getTitle()),
                                "products" => $this->getOrderProducts($order),
                                "status_history" => $this->getOrderStatusHistory($order),
                                "total" => $this->getOrderTotals($order),
                                "vouchers" => array()
                            );
                            $response["status"] = "success";
                            $response["message"] = "";
                            $response["order_details"] = $order_details;
                    } else {
                        $response["status"] = "failure";
                        $response["message"] = __("Orders not found.");
                    }
                } else {
                    $response["status"] = "failure";
                    $response["message"] = __("Please login to view your order details.");
                }
            } else {
                $response["status"] = "failure";
                $response["message"] = __("Please select an order.");
            }
        } else {
            $response["status"] = "failure";
            $response["message"] = __("Something went wrong");
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appGetOrderDetails', $response);
        return $response;
    }
    
    public function getOrderHistory($order)
    {
        return array(
            "order_id" => $order->getId(),
            "cart_id" => "0",
            "order_number" => $order->getIncrementId(),
            "status" => $order->getStatus(),
            "status_color" => "#000000",
            "date_added" => $order->getCreatedAt(),
            "total" => $this->formatPrice($order->getGrandTotal(), true, false),
            "reorder_allowed" => $this->sp_scopeConfig->getValue("sales/reorder/allow"),
        );
    }

    public function getOrderShippingAddress($order)
    {
        $customer_config = $this->sp_scopeConfig->getValue('customer/address');

        $shippingAddress = $order->getShippingAddress();
        $region = array();
        if (!is_null($shippingAddress->getRegionId())) {
            $region = $this->sp_objectManager->create('Magento\Directory\Model\ResourceModel\Region\Collection')
                        ->addFieldToFilter('main_table.region_id', ['eq' => $shippingAddress->getRegionId()])
                        ->getFirstItem();
            if ((count($region->getData()) > 0)) {
                $region_name = $region->getName();
            }
        } else {
            $region_name = $shippingAddress->getRegion();
        }
        $address["alias"] = "";
        $address["firstname"] = $shippingAddress->getFirstname();
        if (isset($customer_config["middlename_show"]) && $customer_config["middlename_show"]) {
            $address["middlename"] = (null != $shippingAddress->getMiddlename()) ? $shippingAddress->getMiddlename() : "";
        }
        $address["lastname"] = $shippingAddress->getLastname();
        if (isset($customer_config["gender_show"]) && $customer_config["gender_show"]) {
            $address["gender"] = (null != $shippingAddress->getGender()) ? $shippingAddress->getGender() : "";
        }
        if (isset($customer_config["suffix_show"]) && $customer_config["suffix_show"]) {
            $address["suffix"] = (null != $shippingAddress->getSuffix()) ? $shippingAddress->getSuffix() : "";
        }
        $address["country"] = $this->localeLists->getCountryTranslation($shippingAddress->getCountry());
        $address["city"] = (null != $shippingAddress->getCity()) ? $shippingAddress->getCity() : '';
        $address["company"] = (null != $shippingAddress->getCompany()) ? $shippingAddress->getCompany() : '';
        $address["postcode"] = (null != $shippingAddress->getPostcode()) ? $shippingAddress->getPostcode() : '';
        $address["mobile_no"] = (null != $shippingAddress->getTelephone()) ? $shippingAddress->getTelephone() : '';
        $address["state"] = (isset($region_name) && null != $region_name ) ? $region_name : "";
        $street = (null != $shippingAddress->getStreet()) ? $shippingAddress->getStreet() : '';
        $address["address_1"] = $street[0];
        $address["address_2"] = isset($street[1]) ? $street[1] : "";
        $address["alias"] = "";

        return $address;
    }

    public function getOrderBillingAddress($order)
    {
        $customer_config = $this->sp_scopeConfig->getValue('customer/address');

        $billingAddress = $order->getBillingAddress();
        $region = array();
        if (!is_null($billingAddress->getRegionId())) {
            $region = $this->sp_objectManager->create('Magento\Directory\Model\ResourceModel\Region\Collection')
                        ->addFieldToFilter('main_table.region_id', ['eq' => $billingAddress->getRegionId()])
                        ->getFirstItem();
            if ((count($region->getData()) > 0)) {
                $region_name = $region->getName();
            }
        } else {
            $region_name = $billingAddress->getRegion();
        }
        $address["alias"] = "";
        $address["firstname"] = $billingAddress->getFirstname();
        if (isset($customer_config["middlename_show"]) && $customer_config["middlename_show"]) {
            $address["middlename"] = (null != $billingAddress->getMiddlename()) ? $billingAddress->getMiddlename() : "";
        }
        $address["lastname"] = $billingAddress->getLastname();
        if (isset($customer_config["gender_show"]) && $customer_config["gender_show"]) {
            $address["gender"] = (null != $billingAddress->getGender()) ? $billingAddress->getGender() : "";
        }
        if (isset($customer_config["prefix_show"]) && $customer_config["prefix_show"]) {
            $address["prefix"] = (null != $billingAddress->getPrefix()) ? $billingAddress->getPrefix() : "";
        }
        if (isset($customer_config["suffix_show"]) && $customer_config["suffix_show"]) {
            $address["suffix"] = (null != $billingAddress->getSuffix()) ? $billingAddress->getSuffix() : "";
        }
        $address["country"] = $this->localeLists->getCountryTranslation($billingAddress->getCountry());
        ;
        $address["city"] = (null != $billingAddress->getCity()) ? $billingAddress->getCity() : '';
        $address["postcode"] = (null != $billingAddress->getPostcode()) ? $billingAddress->getPostcode() : '';
        $address["company"] = (null != $billingAddress->getCompany()) ? $billingAddress->getCompany() : '';
        $address["mobile_no"] = (null != $billingAddress->getTelephone()) ? $billingAddress->getTelephone() : '';
        $address["state"] = (isset($region_name) && null != $region_name) ? $region_name : "";
        $street = (null != $billingAddress->getStreet()) ? $billingAddress->getStreet() : '';
        $address["address_1"] = $street[0];
        $address["address_2"] = isset($street[1]) ? $street[1] : "";
        $address["id_billing_address"] = (null != $billingAddress->getCustomerAddressId()) ? $billingAddress->getCustomerAddressId() : "";

        return $address;
    }

    public function getOrderProducts($order)
    {
        $orderItems = $order->getAllVisibleItems();
        $order_items = array();
        foreach ($orderItems as $item) {
            // Product options
            $productOptions = array();
            $options = $item->getProductOptions();
            if (!empty($options['attributes_info'])) {
                $productOptions = $options['attributes_info'];
            }
            foreach ($productOptions as $k => $v) {
                $productOptions[$k]['name'] = $productOptions[$k]['label'];
                unset($productOptions[$k]['label']);
            }
            $stockItemResource = $this->sp_objectManager->get('\Magento\CatalogInventory\Api\StockRegistryInterface');
            $stock = $stockItemResource->getStockItem($item->getProduct()->getId());
            $productOptions[] = array("name" => __("Quantity"), "value" => 1);
            $order_items[] = array(
                'id' => $item->getId(),
                'id_product_attribute' => "0",
                'title' => $item->getProduct()->getName(),
                'price' => $this->formatPrice($item->getProduct()->getPrice(), true, false),
                'discount_price' => $this->formatPrice($item->getProduct()->getFinalPrice(), true, false),
                'discount_percentage' => "",
                'images' => $this->sp_objectManager->get('Magento\Catalog\Helper\Image')->init($item->getProduct(), 'product_base_image')
                                           ->constrainOnly(TRUE)
                                           ->keepAspectRatio(TRUE)
                                           ->keepTransparency(TRUE)
                                           ->keepFrame(FALSE)
                                           ->resize(120, 90)->getUrl(),
                'quantity' => $item->getQtyOrdered(),
                'product_items' => $productOptions,
                'customizable_items' => array(),
                'is_gift_product' => '0',
                'stock' => $stock->getData('is_in_stock') ? true : false,
                'total' => $this->formatPrice($item->getRowTotal(), true, false),
            );
        }
        return $order_items;
    }

    public function getOrderStatusHistory($order)
    {
        $order_status_history = $order->getStatusHistoryCollection(true);
        $order_history_details = array();
        foreach ($order_status_history as $history) {
            $order_history_details[] = array(
                "id" => $history->getId(),
                "order_status" => $history->getStatus(),
                "notify" => $history->getIsCustomerNotified(),
                "comment" => $history->getComment(),
                "history_date" => $history->getCreatedAt(),
                "status_color" => "#000000"
            );
        }
        return $order_history_details;
    }

    public function getOrderTotals($order)
    {
        $totals_order = array();

        $totals_order[] = array(
            "name" => __("Subtotal"),
            "value" => $this->formatPrice($order->getSubtotal(), true, false)
        );
        $totals_order[] = array(
            "name" => __("Shipping & Handling"),
            "value" => $this->formatPrice($order->getShippingAmount(), true, false)
        );
        $totals_order[] = array(
            "name" => __("Discount"),
            "value" => $this->formatPrice($order->getDiscountAmount(), true, false)
        );
        $totals_order[] = array(
            "name" => __("Tax"),
            "value" => $this->formatPrice($order->getTaxAmount(), true, false)
        );
        $totals_order[] = array(
            "name" => __("Grand Total"),
            "value" => $this->formatPrice($order->getGrandTotal(), true, false)
        );
        return $totals_order;
    }
    
    public function appCheckOrderStatus()
    {
        $response = array();
        if ($this->sp_request->isPost()) {
            $session = $this->sp_objectManager->get('Magento\Checkout\Model\Type\Onepage')->getCheckout();
            $lastorder_id = $session->getLastOrderId();
            if (isset($lastorder_id)) {
                $response["status"] = "success";
                $response["message"] = __("Order created by this cart");
                $response["cart_id"] = "0";
            } else {
                $response["status"] = "failure";
                $response["message"] = __("");
            }
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appCheckOrder', $response);
        return $response;
    }
    
    public function appApplyVoucher()
    {
        $response = array();
        try {
            $couponCode = $this->sp_request->getParam('voucher');
            $codeLength = strlen($couponCode);

            $isCodeLengthValid = $codeLength && $codeLength <= 255;

            $this->quote->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->quote->getQuote()->setCouponCode($isCodeLengthValid ? $couponCode : '')
                    ->collectTotals()
                    ->save();

            if ($codeLength) {
                if ($isCodeLengthValid && $couponCode == $this->quote->getQuote()->getCouponCode()) {
                    $response = $this->appGetCartDetails(true);
                    $response['status'] = "success";
                    $response['message'] = __('Voucher has been applied to your cart successfully.');
                } else {
                    $response['status'] = "failure";
                    $response['message'] = __('Coupon code is not valid.');
                }
            } else {
                $response = $this->appGetCartDetails(true);
                $response['status'] = "success";
                $response['message'] = __('Voucher has been applied to your cart successfully.');
            }
        } catch (\Exception $e) {
            $response['status'] = "failure";
            $response['message'] = $e->getMessage();
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appApplyVoucher', $response);
        return $response;
    }

    public function appRemoveVoucher()
    {
        $response = array();
        try {
            $couponCode = "";
            $codeLength = strlen($couponCode);

            $isCodeLengthValid = $codeLength && $codeLength <= 255;

            $this->quote->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->quote->getQuote()->setCouponCode('')
                    ->collectTotals()
                    ->save();

            if ($codeLength) {
                if ($isCodeLengthValid && $couponCode == $this->quote->getQuote()->getCouponCode()) {
                    $response = $this->appGetCartDetails(true);
                    $response['status'] = "success";
                    $response['message'] = __('Voucher has been removed from your cart successfully.');
                } else {
                    $response['status'] = "failure";
                    $response['message'] = __('Coupon code is not valid.');
                }
            } else {
                $response = $this->appGetCartDetails(true);
                $response['status'] = "success";
                $response['message'] = __('Voucher has been removed from your cart successfully.');
            }
        } catch (\Exception $e) {
            $response['status'] = "failure";
            $response['message'] = $e->getMessage();
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appRemoveVoucher', $response);
        return $response;
    }
    
    public function appAddToWishlist()
    {
        $response = array();
        if ($this->sp_request->isPost()) {
            $session = $this->customerSession;

            $wishlistId = $this->sp_request->getParam('wishlist_id');

            $customer = $session->getCustomer();
            $customerId = $session->getCustomerId();
            /* @var Mage_Wishlist_Model_Wishlist $wishlist */
            $wishlist = $this->wishlistFactory->create();
            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } else {
                if ($customerId) {
                    $wishlist->loadByCustomerId($customerId, true);
                } else {
                    $response["status"] = "failure";
                    $response["message"] = __("Please login to add this product to wishlist.");
                }
            }
            if (empty($response)) {
                $post_data = array();
                $productId = (int) $this->sp_request->getPost("product_id");
                if (!$productId) {
                    $response["status"] = "failure";
                    $response["message"] = __("Please select a product");
                }
                if (empty($response)) {
                    $product = $this->_productRepo->getByid($productId);
                    if (!$product->getId() || !$product->isVisibleInCatalog()) {
                        $response["status"] = "failure";
                        $response["message"] = __('Cannot specify product.');
                    }
                }
                if (empty($response)) {
                    try {

                        $wishlist_id = $wishlist->getData('wishlist_id');
                        $item_collection = $wishlist->getItemCollection()
                                ->addFieldToFilter('product_id', $productId)
                                ->addFieldToFilter('wishlist_id', $wishlist_id);

                        if (!$data = $item_collection->getData()) {
                            $requestParams = array(
                                "product" => $productId
                            );
                            if ($session->getBeforeWishlistRequest()) {
                                $requestParams = $session->getBeforeWishlistRequest();
                                $session->unsBeforeWishlistRequest();
                            }
                            $buyRequest = new \Magento\Framework\DataObject($requestParams);

                            $result = $wishlist->addNewItem($product, $buyRequest);
                            if (is_string($result)) {
                                throw new \Magento\Framework\Exception\LocalizedException(__($result));
                            }
                            $wishlist->save();
                            $this->_eventManager->dispatch(
                                'wishlist_add_product',
                                ['wishlist' => $wishlist, 'product' => $product, 'item' => $result]
                            );
                        }

                        $this->sp_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
                        $response["status"] = "success";
                        $response["wishlist_count"] = $this->wishlistFactory->create()->loadByCustomerId($customer->getId(), true)->getItemsCount();
                        $response["message"] = __('%1$s has been added to your wishlist.', $product->getName());
                    } catch (\Exception $e) {
                        $response["status"] = "failure";
                        $response["message"] = __('An error occurred while adding item to wishlist: %s', $e->getMessage());
                    }
                }
            }
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appAddToWishlist', $response);
        return $response;
    }
    
    public function appGetWishlist()
    {

        $response = array();
        try {
            $wishlistId = $this->sp_request->getParam('wishlist_id');
            $customerId = $this->customerSession->getCustomerId();
            /* @var Mage_Wishlist_Model_Wishlist $wishlist */
            $wishlist = $this->wishlistFactory->create();
            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } else {
                $wishlist->loadByCustomerId($customerId, true);
            }

            if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
                $wishlist = null;
                $response["status"] = "failure";
                $response["message"] = __("Requested wishlist doesn't exist");
            }
            //Mage::register('wishlist', $wishlist);
            $response["status"] = "success";
            $response["message"] = "";
            $response["install_module"] = "";
            $wishlist_products = array();
            if($wishlist){
                $wishListItemCollection = $wishlist->getItemCollection();
            }else{
                $wishListItemCollection = NULL;
            }


            foreach ($wishListItemCollection as $item) {
                $stockItemResource = $this->sp_objectManager->get('\Magento\CatalogInventory\Api\StockRegistryInterface');
                $stock = $stockItemResource->getStockItem($item->getProduct()->getId());
                
                
                $product_options = $attributes = $item->getOptions();
                $product_options[] = array("label" => __("SKU"), "value" => $item->getProduct()->getSku());
                $product_options[] = array("label" => __("Quantity"), "value" => 1);
                foreach ($product_options as $k => $v) {
                    $product_options[$k]['name'] = $product_options[$k]['label'];
                    unset($product_options[$k]['label']);
                }
                $wishlist_products[] = array(
                    "allow_out_of_stock" => "0",
                    "customizable_items" => array(),
                    "item_id" => $item->getId(),
                    "product_id" => $item->getProduct()->getId(),
                    "title" => $item->getProduct()->getName(),
                    'stock' => $stock->getData('is_in_stock') ? true : false,
                    'available_for_order' => $item->getProduct()->isSaleable() ? "1" : "0",
                    'price' => $this->formatPrice($item->getProduct()->getPrice(), true, false),
                    'show_price' => "1",
                    'is_gift_product' => "0",
                    'minimal_quantity' => "1",
                    'new_products' => $this->isProductNew($item->getProduct()) ? "1" : "0 ",
                    'on_sale_products' => ($item->getProduct()->getPrice() > $item->getProduct()->getFinalPrice()) ? "1" : "0",
                    'discount_price' => ($item->getProduct()->getPrice() > $item->getProduct()->getFinalPrice()) ? $this->formatPrice($item->getProduct()->getFinalPrice(), true, false) : 0,
                    'discount_percentage' => (($item->getProduct()->getPrice() > $item->getProduct()->getFinalPrice()) && $item->getProduct()->getFinalPrice() !== 0) ? number_format((($item->getProduct()->getPrice() - $item->getProduct()->getFinalPrice()) / $item->getProduct()->getPrice()) * 100, 2, '.', '') : "0",
                    "images" => $this->getImageUrl($item->getProduct()->getSmallImage()),
                    "product_items" => $product_options,
                    "quantity" => "1",
                    "id_product_attribute" => "0",
                );
            }
            $response["wishlist_products"] = $wishlist_products;
        } catch (\Exception $e) {
            $response["status"] = "failure";
            $response["message"] = __('Wishlist could not be created.');
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appGetWishlist', $response);
        return $response;
    }
    
    public function appRemoveWishlist() {
        $response = array();
        if ($this->sp_request->isPost()) {
            $id = (int) $this->sp_request->getParam('product_id');
            $wishlist = $this->wishlistFactory->create();

            $customer = $this->customerSession->getCustomer();
            $customerId = $this->customerSession->getCustomerId();

            if ($customerId) {
                $wishlist->loadByCustomerId($customerId, true);
                if (!$wishlist) {
                    $response['status'] = "failure";
                    $response['message'] = __("Requested wishlist doesn't exist");
                } else {
                    $wishlist_id = $wishlist->getData('wishlist_id');
                    $item_collection = $wishlist->getItemCollection()
                            ->addFieldToFilter('product_id', $id)
                            ->addFieldToFilter('wishlist_id', $wishlist_id);

                    if ($data = $item_collection->getData()) {
                        $id = $data[0]['wishlist_item_id'];
                        $item = $this->sp_objectManager->create('Magento\Wishlist\Model\Item')->load($id);
                        if (!$item->getId()) {
                            $response['status'] = "failure";
                            $response['message'] = __('An error occurred while deleting the item from wishlist.');
                        } else {
                            try {
                                $item->delete();
                                $wishlist->save();
                                $response['status'] = "success";
                                $response['install_module'] = '';
                                $response["wishlist_count"] = $this->wishlistFactory->create()->loadByCustomerId($customer->getId(), true)->getItemsCount();
                                $response['message'] = __('Wishlist item has been successfully removed.');
                            } catch (\Exception $e) {
                                $response['status'] = "failure";
                                $response['message'] = __('An error occurred while deleting the item from wishlist: %s', $e->getMessage());
                            } catch (\Exception $e) {
                                $response['status'] = "failure";
                                $response['message'] = __('An error occurred while deleting the item from wishlist.');
                            }
                        }
                    } else {
                        $response['status'] = "success";
                        $response['install_module'] = '';
                        $response["wishlist_count"] = $this->wishlistFactory->create()->loadByCustomerId($customer->getId(), true)->getItemsCount();
                        $response['message'] = __('Wishlist item has been successfully removed.');
                    }
                }
            } else {
                $response["status"] = "failure";
                $response["message"] = __("Please login to add this product to wishlist.");
            }
        } else {
            $response['status'] = "failure";
            $response['message'] = __('An error occurred while deleting the item from wishlist.');
        }

        $this->dataHelper->logresponse($this->sp_request, 'appRemoveWishlist', $response);
        return $response;
    }
    
    public function appReorder()
    {
        $response = array();
        if ($this->sp_request->isPost()) {
            $orderId = (int) $this->sp_request->getParam('order_id');
            if ($orderId) {
                $order = $this->_orderCollectionFactory->create()
                                ->addFieldToSelect('*')
                                ->addFieldToFilter('entity_id', $orderId)
                                ->getFirstItem();
                $cart = $this->sp_objectManager->create('Magento\Checkout\Model\Cart');
                $cartTruncated = false;
                $items = $order->getItemsCollection();
                foreach ($items as $item) {
                    try {
                        $cart->addOrderItem($item);
                    } catch (\Exception $e) {
                        $response["status"] = "failure";
                        $response["message"] = __('Cannot add the item to shopping cart.');
                    }
                }
                $cart->save();
                $response = $this->appGetCartDetails(true);
                $response["status"] = "success";
                $response["message"] = __("Item has been successfully added to the cart.");
                
            } else {
                $response["status"] = "failure";
                $response["message"] = __("Something went wrong.");
            }
        } else {
            $response["status"] = "failure";
            $response["message"] = __("Something went wrong.");
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appReorder', $response);
        return $response;
    }
    
    public function appGetShippingMethod($array = false)
    {

        $session = $this->quote;
        $quote = $session->getQuote();
        $shipping_method = array();
        $address = $quote->getShippingAddress();
        $defaultCountryId = $this->sp_scopeConfig->getValue('general/country/default');
        $country = $address->getCountryId() ? $address->getCountryId() : $defaultCountryId;
        $address->setCountryId($country)->setCollectShippingRates(true)->collectShippingRates();
        $_shippingRateGroups = $quote->getShippingAddress()->getGroupedAllShippingRates();
        $quote->save();
        foreach ($_shippingRateGroups as $code => $_rates) {
            $shipping_method_title = $this->getCarrierName($code);
            foreach ($_rates as $_rate) {
                if ($_rate->getErrorMessage()) {
                    $shipping_method_error = $_rate->getErrorMessage();
                } else {
                    $helper_tax = $this->sp_objectManager->create('\Magento\Tax\Helper\Data');
                    $_excl = $this->getShippingPrice($_rate->getPrice(), $helper_tax->displayShippingPriceIncludingTax());
                    $_incl = $this->getShippingPrice($_rate->getPrice(), true);
                    if ($helper_tax->displayShippingBothPrices() && ($_incl != $_excl)) {
                        $incl_string = '(' . __('Incl. Tax') . $_incl . ')';
                    }
                    $shipping_method["shipping_methods"][] = array(
                        'name' => $shipping_method_title,
                        'code' => $_rate->getCode(),
                        'label' => $_rate->getMethodTitle(),
                        'price' => strip_tags($_excl) ? strip_tags($_excl) : strip_tags($incl_string),
                    );
                }
            }
        }
        if ($array) {
            return $shipping_method;
        } else {
            $shipping_method["SID"] = $this->getSid();
            $shipping_method["version"] = $this->dataHelper->getVersion();
            $shipping_method["session_data"] = "";
            $shipping_method["install_module"] = '';
            $this->dataHelper->logresponse($this->sp_request, 'appGetShippingMethod', $shipping_method);
            return $shipping_method;
        }
    }
    
    public function getCarrierName($carrierCode)
    {
        if ($name = $this->sp_scopeConfig->getValue('carriers/' . $carrierCode . '/title')) {
            return $name;
        }
        return $carrierCode;
    }

    public function getShippingPrice($price, $flag)
    {
        $session = $this->quote;
        $quote = $session->getQuote();
        return $this->priceCurrency->convertAndFormat($this->sp_objectManager->create('\Magento\Tax\Helper\Data')->getShippingPrice($price, $flag, $quote->getAddress()), true);
    }
    
    public function appWriteReview()
    {
        $response = array();
        $response["status"] = "failure";
        $response["message"] = __('Unable to post the review.');
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["session_data"] = "";
        $response["install_module"] = '';
        $this->dataHelper->logresponse($this->sp_request, 'appWriteReview', $response);
        return $response;
    }
    
    public function appFCMregister()
    {
        $response = array();
        if ($this->sp_request->isPost()) {
            $email = $this->sp_request->getParam('email');
            $fcm_id = $this->sp_request->getParam('fcm_id');
            $device_type = $this->sp_request->getParam('mobile_platform', 'both');
            if ($email && $fcm_id) {
                $data = array(
                    'kb_email' => $email,
                    'fcm_id' => $fcm_id,
                    'notification_sent_status' => '0',
                    'date_add' => $this->dataHelper->getDate(),
                    'date_upd' => $this->dataHelper->getDate(),
                    'device_type' => $device_type
                );
                $is_details_exist = $this->dataHelper->isFcmAndEmailExist($email, $fcm_id);
                if ($is_details_exist) {
                    $data['fcm_details_id'] = $is_details_exist['fcm_details_id'];
                }
                

                $fcmmodel = $this->fcmModel->setData($data);
                $fcmmodel->save();
                $response['status'] = 'success';
            } else {
                $response["status"] = "failure";
            }
        } else {
            $response["status"] = "failure";
        }
        
        $response["SID"] = $this->getSid();
        $response["version"] = $this->dataHelper->getVersion();
        $response["install_module"] = "";
        $response["session_data"] = "";
        $this->dataHelper->logresponse($this->sp_request, 'appFCMregister', $response);
        return $response;
    }
    
    public function sendAbandonedCartNotification()
    {
        if ($list = $this->dataHelper->getAbandonedCartList()) {
            $list_data = $list->getData();
            if ($list_data) {
                foreach ($list_data as $data) {
                    if ($fcm_id = $this->dataHelper->getFcmIdByEmail($data['customer_email'])) {
                        $this->dataHelper->sendNotificationRequest('kb_abandoned_cart', $data['customer_email']);
                        $this->dataHelper->updateNotificationStatus($data['customer_email']);
                    }
                }
            }
        }
    }
    
    public function forceLogin($email) {
        try{
            $websiteId = $this->sp_storeManager->getWebsite()->getWebsiteId();
            $customer = $this->customer->setWebsiteId($websiteId)->loadByEmail($email);
            $this->customerSession->setCustomerAsLoggedIn($customer);
        } catch (\Exception $ex) {
            return ['status' => 'failure', 'message' => $ex->getMessage()];
        }
    }

}
