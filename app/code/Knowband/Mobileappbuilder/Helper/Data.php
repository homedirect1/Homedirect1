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
class Data extends \Magento\Framework\App\Helper\AbstractHelper
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
    
    const COOKIE_PERIOD = 2592000;//30 days
    const TRANSLATION_RECORD_FILE = 'Vss_Mobileappbuilder_Record.csv';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configResource,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,
        \Magento\Framework\ObjectManagerInterface $objectManager,
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
        \Knowband\Mobileappbuilder\Model\Layouts $kbLayoutModel, 
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        CookieManagerInterface $cookieManager,
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        $this->sp_storeManager = $storeManager;
        $this->moduleManager = $context->getModuleManager();
        $this->sp_scopeConfig = $context->getScopeConfig();
        $this->sp_request = $context->getRequest();
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
        $this->customerSession = $customerSession;
        $this->wishlistFactory = $wishlistFactory;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sp_layoutModel = $kbLayoutModel;
        parent::__construct($context);
    }

    public function getCMSPages(){
        $result = array();
        $page = $this->pageFactory->create();
        foreach($page->getCollection() as $item)
        {
            $result[$item->getId()] = $item->getTitle();
        }
        return $result;
    }
    
    /*
     * Function to get categories list on store
     */

    public function getCategories()
    {
        $categoryFactory = $this->sp_objectManager->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
        $_categories = $categoryFactory->create()                              
            ->addAttributeToSelect('*')
                ->addIsActiveFilter()
            ->setStore($this->sp_storeManager->getStore()); 

        $allCategories = array();
        $allCategories[] = array(
            'value' => "0",
            'label' => __('--Select Category--')
        );
        if (count($_categories) > 0) {
            foreach ($_categories as $_category) {
                $_subcategories = $_category->getChildrenCategories();
                if (count($_subcategories) > 0) {
                    $allCategories[] = array(
                        'value' => $_category->getId(),
                        'label' => $_category->getName()
                    );
                    foreach ($_subcategories as $_subcategory) {
                        $allCategories[] = array(
                            'value' => $_subcategory->getId(),
                            'label' => $_category->getName() . ' > ' . $_subcategory->getName()
                        );
                    }
                }
            }
        }
        return $allCategories;
    }
    
    public function getProductCollection() {
        $collection = $this->_productCollectionFactory->create()
                ->addFieldToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                ->addAttributeToSelect('id')
                ->addAttributeToSelect('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                ->addAttributeToSelect('type')
                ->addAttributeToSelect('name');
        return $collection;
    }
    
    public function getFirebaseServerKey()
    {
        $push_notification_settings = $this->getSettings('push_notification_settings');
        if (isset($push_notification_settings['firebase_server_key']) && trim($push_notification_settings['firebase_server_key'] != '')) {
            return $push_notification_settings['firebase_server_key'];
        } else {
            return false;
        }
    }
    
    
    public function getDate() {
        return $this->date->date();
    }

    
    
    public function getMediaUrl()
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance(); //instance of\Magento\Framework\App\ObjectManager
        $storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
        $currentStore = $storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }
    
    public function getSavedSettings($key = 'knowband/mobileappbuilder/settings', $scope = "default", $scope_id = 0, $checkMarketPlace = false)
    {
        if ($this->sp_request->getParam('store')) {
            $scope_id = $this->sp_storeManager->getStore($this->sp_request->getParam('store'))->getId();
            $scope = "stores";
        } elseif ($this->sp_request->getParam('website')) {
            $scope_id = $this->sp_storeManager->getWebsite($this->sp_request->getParam('website'))->getId();
            $scope = "websites";
        } elseif ($this->sp_request->getParam('group')) {
            $scope_id = $this->sp_storeManager->getGroup($this->sp_request->getParam('group'))->getWebsite()->getId();
            $scope = "groups";
        } else {
            $scope = "default";
            $scope_id = 0;
        }
         $area = $this->sp_state->getAreaCode();
        if ($area == 'frontend') {            
            $settings_json = $this->sp_scopeConfig->getValue($key, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        } else {
            $settings_json = $this->sp_scopeConfig->getValue($key, $scope, $scope_id);
        }
        $settings_array = json_decode($settings_json, true);
        if (($settings_array === false || $settings_array === NULL) && $key == 'knowband/mobileappbuilder/settings') {
            $default_settings = $this->getDefaultSettings();
            $this->sp_resource->saveConfig("knowband/mobileappbuilder/settings", json_encode($default_settings), $scope, $scope_id);
            return $default_settings;
        } else {
            return $settings_array;
        }
    }
    
    public function getDefaultSettings()
    {
        $this->insertDefaultImages();
        return array(
            "general_settings" => array(
                'enable' => 0,
                'enabledlog' => 1,
                'enabledlivechat' => 0,
                'livechatkey' => '',
                'custom_css' => '',
                'app_button_color' => '#00a781',
                'app_button_text_color' => '#ffffff',
                'app_theme_color' => '#c3000f',
                'app_background_color' => '#ffffff',
                'home_product_list' => ['new_arrival', 'special', 'best_seller'],
                'home_product_list_count' => 5
            ),
            "push_notification_settings" => [
                'firebase_server_key' => '',
                'order_create_enable' => 0,
                'order_create_title' => __('Order Successfully Created'),
                'order_create_message' => __('Hi Thanks for your interest. Keep shopping with us to become our premium customer.'),
                'order_status_change_enable' => 0,
                'order_status_change_title' => __('Order status update'),
                'order_status_change_message' => __('Hi Your order status has been changed to {{STATUS}}.'),
                'abandoned_cart_enable' => 0,
                'abandoned_cart_title' => __('Hurry!'),
                'abandoned_cart_message' => __('Hi, Complete your order to get extra benefits on your next order.'),
                'abandoned_cart_interval' => 1
            ],
        );
        
    }
    
    public function insertDefaultImages(){
        $url = $this->assetRepo->getUrl('Knowband_Mobileappbuilder', array()).'/images/sliders/';
        
        $connection  = $this->_resource->getConnection();
        $table = $this->_resource->getTableName('kb_sliders_banners');
        $query = 'UPDATE ' . $table . ' set image_url = "'.$url.'sample-slider1.jpg'.'" where kb_banner_id = 1';
        $connection->query($query);
        $query = 'UPDATE ' . $table . ' set image_url = "'.$url.'sample-slider2.jpg'.'" where kb_banner_id = 2';
        $connection->query($query);
        $query = 'UPDATE ' . $table . ' set image_url = "'.$url.'sample-slider3.jpg'.'" where kb_banner_id = 3';
        $connection->query($query);
        $query = 'UPDATE ' . $table . ' set image_url = "'.$url.'sample-banner1.jpg'.'" where kb_banner_id = 4';
        $connection->query($query);
        $query = 'UPDATE ' . $table . ' set image_url = "'.$url.'sample-banner2.jpg'.'" where kb_banner_id = 5';
        $connection->query($query);
        
        $table = $this->_resource->getTableName('kb_mobileapp_banners');
        $query = 'UPDATE ' . $table . ' set image_url = "'.$url.'banner1.jpg'.'" where id = 1';
        $connection->query($query);
        $query = 'UPDATE ' . $table . ' set image_url = "'.$url.'banner2.jpg'.'" where id = 2';
        $connection->query($query);
        $query = 'UPDATE ' . $table . ' set image_url = "'.$url.'slider1.jpg'.'" where id = 3';
        $connection->query($query);
        $query = 'UPDATE ' . $table . ' set image_url = "'.$url.'slider2.jpg'.'" where id = 4';
        $connection->query($query);
        $query = 'UPDATE ' . $table . ' set image_url = "'.$url.'slider3.jpg'.'" where id = 5';
        $connection->query($query);
//        $connection->closeConnection();
    }

        public function getStoreIdDetails() {
        if ($this->sp_request->getParam('store')) {
            $storeId = $this->sp_storeManager->getStore($this->sp_request->getParam('store'))->getId();
            $websiteId = 0;
            $scope = "stores";
        } elseif ($this->sp_request->getParam('website')) {
            $websiteId = $this->sp_storeManager->getWebsite($this->sp_request->getParam('website'))->getId();
            $storeId = 0;
            $scope = "websites";
        } elseif ($this->sp_request->getParam('group')) {
            $websiteId = $this->sp_storeManager->getGroup($this->sp_request->getParam('group'))->getWebsite()->getId();
            $storeId = 0;
            $scope = "groups";
        } else {
            $scope = "default";
            $websiteId = 0;
            $storeId = 0;
        }
        return $websiteId?$websiteId:$storeId;
    }
    
    public function getBaseUrl($param) {
        if($param == 'URL_TYPE_MEDIA'){
            return $this->sp_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        }
    }
    
    public function getSettings($key) {
        $settings = $this->getSavedSettings();
        return isset($settings[$key])?$settings[$key]:array();
    }
    
    public function getNotificationDetails($id) {
        $collection = $this->pushNotificationModel->load($id);
        return $collection->getData();
    }
    public function getBannerSliderDetails($id) {
        $collection = $this->bannerModel->load($id);
        return $collection->getData();
    }
    public function getPaymentMethodDetails($id) {
        $collection = $this->paymentModel->load($id);
        return $collection->getData();
    }
    
    public function getVersion()
    {
        return $this->sp_request->getParam('version', '1.7');
    }
    
    /*
     * function to get helper name according to api version
     */

    public function getHelperName($api_verion)
    {
        $data = array(
            '1.2' => 'OneTwo',
            '1.3' => 'OneThree',
            '1.4' => 'OneFour',
            '1.5' => 'OneFive',
            '1.6' => 'OneSix',
            '1.7' => 'OneSeven'
        );

        if (isset($data[$api_verion])) {
            return $data[$api_verion];
        } else {
            return false;
        }
    }
    
    public function logresponse($request, $api_name, $response)
    {
        $configData = $this->getSavedSettings('knowband/mobileappbuilder/settings');
        if (isset($configData['general_settings']['enabledlog'])) {
            $logfile = fopen(BP.'/var/log/KB_App.log', "a+");
            $postParams = array();
            foreach ($request->getPost() as $key => $value) {
                $postParams[$key] = $value;
            }
            
            $getParams = array();
            foreach ($request->getParams() as $key => $value) {
                $getParams[$key] = $value;
            }
            
            $message = date("Y-m-d H:i:s") . "	" . $api_name . "	" . $_SERVER["REMOTE_ADDR"] . "	" . $_SERVER["REQUEST_URI"] . "	" . json_encode($getParams) . "	" . json_encode($postParams) . "	" . json_encode($response) . "\n";
            fwrite($logfile, $message);
            fclose($logfile);
        }
    }
    
    public function returnLanguageRecordAsArray()
    {
        $lang_arr = array();
        $file_path = $this->getRootPath().'/var/log/'. self::TRANSLATION_RECORD_FILE;
        if (file_exists($file_path) && is_readable($file_path)) {
            $file_handle = fopen($file_path, 'r');
            $count = 0;
            while (!feof($file_handle)) {
                $csv_line = fgetcsv($file_handle);
                if (!isset($csv_line[0]) || $csv_line[0] == '') {
                    continue;
                }
                
                $lang_arr[$count]['iso_code'] = $csv_line[0];
                if (isset($csv_line[1])) {
                    $lang_arr[$count]['timestamp'] = $csv_line[1];
                } else {
                    $lang_arr[$count]['timestamp'] = time();
                }
                $count++;
            }
        }
        
        return $lang_arr;
    }
    
    public function getRootPath() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        return $directory->getRoot();
    }
    
    /**
     * Function to check status of marketplace module
     * 
     * @retun bool
     */
    public function isMarketplaceEnabled() {
        $moduleName = 'Knowband_Marketplace';
        
        if (!$this->moduleManager->isEnabled($moduleName)) {
            return false;
        }
        if (!$this->moduleManager->isOutputEnabled($moduleName)) {
            return false;
        }
        if (!$this->sp_scopeConfig->getValue("vss/marketplace/active", \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return false;
        }
        
        return true;
    }
    
    
    /**
     * Function to check status of review module
     * 
     * @retun bool
     */
    public function isReviewEnabled() {
        $moduleName = 'Magento_Review';
        
        if (!$this->moduleManager->isEnabled($moduleName)) {
            return false;
        }
        if (!$this->moduleManager->isOutputEnabled($moduleName)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Function to check status of review module
     * 
     * @retun bool
     */
    public function isSpinWinEnabled() {
        $moduleName = 'Knowband_Spinandwin';
        
        if (!$this->moduleManager->isEnabled($moduleName)) {
            return false;
        }
        if (!$this->moduleManager->isOutputEnabled($moduleName)) {
            return false;
        }        
       
        
        return true;
    }
    
    /**
     * Function to check product exist in customer wishlist or not
     * @return bool
     */

    public function isInWishlist($product_id)
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        if ($customerId) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            
            $wishlist = $this->wishlistFactory->create();
            
            $wishlist->loadByCustomerId($customerId, true);
            if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
                return false;
            }
            
            $wishlist_products = array();
            $wishListItemCollection = $wishlist->getItemCollection();
            foreach ($wishListItemCollection as $item) {
                $wishlist_products[] = $item->getProduct()->getId();
            }

            if (in_array($product_id, $wishlist_products)) {
                return true;
            } else {
                return false;
            }
            
        } else {
            return false;
        }
        
    }
    
    /**
     * Get form key cookie
     *
     * @return string
     */
    public function getCookie($key)
    {
        return $this->cookieManager->getCookie($key);
    }

    /**
     * @param string $value
     * @param PublicCookieMetadata $metadata
     * @return void
     */
    public function setCookie($key, $value)
    {
        $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                    ->setDuration(self::COOKIE_PERIOD)
                    ->setPath('/')
                    ->setSecure($this->sp_request->isSecure())
                   ->setHttpOnly(true);
        $this->cookieManager->setPublicCookie(
            $key,
            $value,
            $publicCookieMetadata
        );
    }
    
    public function getAllTextKeys()
    {
        return array(
            'app_text_category_products',
            'app_text_no_internet',
            'app_text_product_image_viewer',
            'app_text_payment_methods',
            'app_text_seller_comments',
            'app_text_home',
            'app_text_category',
            'app_text_login',
            'app_text_logout',
            'app_text_login_signup',
            'app_text_account',
            'app_text_contact',
            'app_text_about',
            'app_text_add_to_wishlist',
            'app_text_add_to_cart',
            'app_text_off',
            'app_text_all',
            'app_text_languages',
            'app_text_sort',
            'app_text_filter',
            'app_text_sort_order',
            'app_text_price_asce',
            'app_text_price_desc',
            'app_text_product',
            'app_text_select',
            'app_text_instock',
            'app_text_outstock',
            'app_text_product_info',
            'app_text_customization',
            'app_text_accessories',
            'app_text_view_less',
            'app_text_view_more',
            'app_text_provide_details',
            'app_text_discount_price',
            'app_text_discount_percentage',
            'app_text_rating',
            'app_text_bag',
            'app_text_apply_voucher',
            'app_text_entry_voucher_code',
            'app_text_apply',
            'app_text_gift',
            'app_text_entry_message',
            'app_text_available_first',
            'app_text_continue_shopping',
            'app_text_continue_checkout',
            'app_text_update_quantity',
            'app_text_enter_quantity',
            'app_text_empty_cart',
            'app_text_goto_home',
            'app_text_review_your_order',
            'app_text_add_new_shipping_address',
            'app_text_history',
            'app_text_billing_details',
            'app_text_same_as_shipping_details',
            'app_text_summary',
            'app_text_shipping_methods',
            'app_text_comment',
            'app_text_payment_summary',
            'app_text_proceed',
            'app_text_make_payment',
            'app_text_congratulations',
            'app_text_shipping_addresses',
            'app_text_billing_addresses',
            'app_text_addresses',
            'app_text_order_detail',
            'app_text_update_personal',
            'app_text_update_password',
            'app_text_current_password',
            'app_text_new_password',
            'app_text_confirm_password',
            'app_text_cancel',
            'app_text_save',
            'app_text_order_ref',
            'app_text_status',
            'app_text_placed',
            'app_text_reorder',
            'app_text_order_details',
            'app_text_status_history',
            'app_text_shipping_address',
            'app_text_billing_address',
            'app_text_order_summary',
            'app_text_shipping_method',
            'app_text_payment_method',
            'app_text_gift_wrapping',
            'app_text_no_shipping_address',
            'app_text_no_order_details',
            'app_text_signup',
            'app_text_continue_guest',
            'app_text_email',
            'app_text_password',
            'app_text_forgot_password',
            'app_text_login_social_account',
            'app_text_login_with_google',
            'app_text_login_with_facebook',
            'app_text_signup_with_google',
            'app_text_signup_with_facebook',
            'app_text_msg_enter_valid_email',
            'app_text_msg_enter_email',
            'app_text_msg_enter_password',
            'app_text_msg_password_characters_less_than_3',
            'app_text_msg_first_name',
            'app_text_msg_last_name',
            'app_text_msg_dob',
            'app_text_msg_confirm_password',
            'app_text_msg_personal_details',
            'app_text_msg_pass_not_matched',
            'app_text_sold_by',
            'app_text_write_review',
            'app_text_view_review',
            'app_text_view_seller_products',
            'app_text_wait',
            'app_text_loading',
            'app_text_msg_went_wrong',
            'app_text_msg_no_internet_found',
            'app_text_msg_no_internet_title',
            'app_text_msg_request',
            'app_text_msg_order_placed_email',
            'app_text_msg_product_look',
            'app_text_msg_search_product',
            'app_text_msg_view_cart',
            'app_text_msg_logout',
            'app_text_msg_view_product',
            'app_text_msg_sort_product',
            'app_text_msg_filter_product',
            'app_text_text_next',
            'app_text_text_okay',
            'app_text_required',
            'app_text_msg_rating',
            'app_text_msg_add_cart',
            'app_text_msg_add_wishlist',
            'app_text_msg_product_price',
            'app_text_msg_product_not_available',
            'app_text_msg_combination',
            'app_text_msg_product_no_stock',
            'app_text_msg_out_stock',
            'app_text_msg_checkout_products',
            'app_text_msg_apply_voucher',
            'app_text_msg_update_quantity',
            'app_text_msg_view_details',
            'app_text_msg_success',
            'app_text_msg_failure',
            'app_text_msg_enter_quantity',
            'app_text_quantity',
            'app_text_remove',
            'app_text_customization_details',
            'app_text_msg_add_address',
            'app_text_msg_update_address',
            'app_text_msg_select_address',
            'app_text_select_address_text',
            'app_text_msg_no_product',
            'app_text_msg_no_comment_available',
            'app_text_msg_reset_password',
            'app_text_msg_login',
            'app_text_msg_login_fb',
            'app_text_msg_login_google',
            'app_text_msg_update_profile',
            'app_text_product_info_and_care',
            'app_text_download',
            'app_text_minimal_quantity',
            'app_text_seller_details',
            'app_text_pack_content',
            'app_text_submit',
            'app_text_msg_forget_password',
            'app_text_enter_your_email',
            'app_text_reset_password',
            'app_text_view_selected_filter',
            'app_text_clear_selected_filter',
            'app_text_no_filter',
            'app_text_phone_number_required',
            'app_text_refresh',
            'app_text_internet_connection_failed',
            'app_text_internet_connection_problem',
            'app_text_cancel_transaction',
            'app_text_middle_of_payment',
            'app_text_yes',
            'app_text_no',
            'app_text_enter_your_comment',
            'app_text_msg_shipping_methods_unavailable',
            'app_text_msg_select_shipping_methods',
            'app_text_share_log',
            'app_text_error_minimum_value',
            'app_text_payment_failed_contact_support',
            'app_text_currency',
            'app_text_choose_payment_method',
            'app_text_no_payment_method',
            'app_text_no_payment_currently',
            'app_text_no_make_payment',
            'app_text_make_payment_after_select',
            'app_text_title_paypal_payment',
            'app_text_order_total_price',
            'app_text_user_cancelled_the_payment',
            'app_text_invalid_payment_try_again',
            'app_text_text_skip',
            'app_text_wishlist_item_removed',
            'app_text_wishlist_item_added',
            'app_text_subject',
            'app_text_text_extra_share',
            'app_text_share_intent_title',
            'app_text_error_product_name_url',
            'app_text_tag_error',
            'app_text_demo_app_user_form',
            'app_text_full_name_demo_app_user',
            'app_text_email_demo_app_user',
            'app_text_store_url_demo_app_user',
            'app_text_continue_demo_app_user_form',
            'app_text_invalid_url',
            'app_text_invalid_email',
            'app_text_sellers',
            'app_text_seller',
            'app_text_seller_shipping_policy',
            'app_text_seller_return_policy',
            'app_text_review_title',
            'app_text_review_comment',
            'app_text_review_rating',
            'app_text_filter_products',
            'app_text_select_category',
            'app_text_select_sort',
            'app_text_clear',
            'app_text_edit',
            'app_text_cart',
            'app_text_move_to_cart',
            'app_text_no_shipping_method_available',
            'app_text_module_not_installed',
            'app_text_no_data_found',
            'app_text_enter_coupon_code',
            'app_text_apply_coupon',
            'app_text_add_more_in_cart',
            'app_text_shipping_details',
            'app_text_choose_app_theme',
            'app_text_enter_store_url',
            'app_text_sample_store_url',
            'app_text_downloads',
            'app_text_done',
            'app_text_posted_on',
            'app_text_commented_by',
            'app_text_seller_comment',
            'app_text_open',
            'app_text_order_status',
            'app_text_special_products',
            'app_text_product_information',
            'app_text_featured_products',
            'app_text_requested_gift_order',
            'app_text_step_one_of_three',
            'app_text_review_checkout',
            'app_text_your_wishlist_is_empty',
            'app_text_no_shipping_method',
            'app_text_no_order_found',
            'app_text_percent_off',
            'app_text_coupon_length_validation',
            'app_text_coupon_code_removed',
            'app_text_facebook_login_cancelled',
            'app_text_facebook_login_failure',
            'app_text_login_failed',
            'app_text_wrong_url_install_mab',
            'app_text_install_module',
            'app_text_text_loading',
            'app_text_text_exception',
            'app_text_msg_reset_password_click',
            'app_text_login_from_here',
            'app_text_login_from_facebook_account',
            'app_text_login_from_google_account',
            'app_text_text_warning',
            'app_text_update_address_if_exists',
            'app_text_continue_to_checkout_click_msg',
            'app_text_please_enter_store_url',
            'app_text_product_not_desired_quantity',
            'app_text_select_shipping_methods',
            'app_text_send_email',
            'app_text_error_in_google_sign_in',
            'app_text_process_payment_problem',
            'app_text_text_state',
            'app_text_update_address',
            'app_text_unit_price',
            'app_text_total_price',
            'app_text_text_totals',
            'app_text_text_success',
            'app_text_sale',
            'app_text_new',
            'app_text_share_api_log',
            'app_text_report_via_email',
            'app_text_view_your_store',
            'app_text_view_demo',
            'app_text_search',
            'app_text_wishlist',
            'app_text_select_language',
            'app_text_select_font',
            'app_text_share_feedback',
            'app_text_enter_state',
            'app_text_choose_your_app_theme',
            'app_text_terms_and_conditions',
            'app_text_wishlist_only',
            'app_text_wishlist_remove',
            'app_text_state_not_required',
            'app_text_currencies',
            'app_text_payment_failed',
            'app_text_entered_wrong_url',
            'app_text_fill_all_required_fields',
            'app_text_extra_share',
            'app_text_intent_title',
            'app_text_ok',
            'app_text_explore_the_app',
            'app_text_hour',
            'app_text_min',
            'app_text_sec',
            'app_text_install_whatsapp',
            'app_text_no_account_registered',
            'app_text_confirm_for_authentication',
            'app_text_login_with_fingerprint',
            'app_text_register_for_fingerprint',
            'app_text_biometric_not_available',
            'app_text_fingerprint_not_recognized',
            'app_text_face_not_recognizes',
            'app_text_fingerprint_or_face_not_enrolled',
            'app_text_biometry_locked_out',
            'app_text_register_for_fingerpring',
            'app_text_register_for_fingerprint_details',
            'app_text_register',
            'app_text_verify',
            'app_text_get_otp',
            'app_text_mobile_number',
            'app_text_mobile_number_optional',
            'app_text_error_number_verification',
            'app_text_phone_number_already_exists',
            'app_text_number_verification',
            'app_text_number_verification_description',
            'app_text_login_with_phone_number',
            'app_text_forget_password_description',
            'app_text_enter_email_or_mobile_number',
            'app_text_phone_number_change_message',
            'app_text_enter_otp',
            'app_text_related_products',
            'app_text_view_reviews',
            'app_text_write_reviews',
            'app_text_please_fill_all_details',
            'app_text_close',
            'app_text_add_review',
            'app_text_write_a_review',
            'app_text_nick',
            'app_text_title',
            'app_text_no_reviews',
            'app_text_number_of_reviews',
            'app_text_minimum_rating',
            'app_text_error',
            'app_text_no_reviews'
        );
    }
    
    /*
     * Function to check duplicate data in fcm table
     */

    public function isFcmAndEmailExist($email, $fcm_id)
    {
        $collection = $this->fcmModel->getCollection()
                ->addFieldToFilter('kb_email', $email)
                ->addFieldToFilter('fcm_id', $fcm_id);
        $col_data = $collection->getFirstItem()->getData();
        if (!empty($col_data)) {
            return $col_data;
        } else {
            return false;
        }
    }
    
    public function getAbandonedCartList()
    {

        $push_notification_settings =  $this->getSettings('push_notification_settings');
        if (isset($push_notification_settings['abandoned_cart_enable']) && $push_notification_settings['abandoned_cart_enable'] == '1') {
            if (isset($push_notification_settings['abandoned_cart_interval']) && $push_notification_settings['abandoned_cart_interval'] > 0) {
                $interval = (int) $push_notification_settings['abandoned_cart_interval'];
                $from_seconds = 60 * 60 * $interval;
                $from = date("Y-m-d H:i:s", (time() - $from_seconds));
                $to = date("Y-m-d H:i:s", time());

                $collection = $this->sp_objectManager->get('\Magento\Reports\Model\ResourceModel\Quote\CollectionFactory')->create();
                $collection->addFieldToFilter('main_table.is_active', array(
                    'eq' => 1,
                ));

                $collection->addFieldToFilter('main_table.items_count', array(
                    'neq' => 0,
                ));

                $collection->addFieldToFilter('main_table.updated_at', array(
                    'from' => $from,
                    'to' => $to,
                    'datetime' => true
                ));

                $collection->prepareForAbandonedReport($this->sp_storeManager->getStore()->getStoreId());

                return $collection;
            }
        }

        return false;
    }

    /*
     * function to update notification status
     */

    public function updateNotificationStatus($email)
    {
        $model = $this->fcmModel->load($email, 'kb_email');
        $model->setNotificationSentStatus(1);
        $model->setDateUpd($this->getDate());
        $model->save();
        $model->unsetData();
    }
    
    /*
     * Function to send Push notifications
     */

    public function sendNotificationRequest($type, $email = null, $order_id = null, $order_status = null)
    {
        $notification_data = array();

        $firebase_server_key = '';
        $push_notification_settings = $this->getSettings('push_notification_settings');
        $push_type = '';
        $title = '';
        $message = '';
        if (isset($push_notification_settings['firebase_server_key']) && trim($push_notification_settings['firebase_server_key'] != '')) {
            $firebase_server_key = $push_notification_settings['firebase_server_key'];
        } else {
            $firebase_server_key =  false;
        }
        if ($firebase_server_key) {
            if ($type == 'order_create') {
                if (!isset($push_notification_settings['order_create_enable']) || $push_notification_settings['order_create_enable'] == 0) {
                    return false;
                } else {
                    $title = $push_notification_settings['order_create_title'];
                    $message = $push_notification_settings['order_create_message'];
                    $push_type = 'order_placed';
                }
                
            } elseif ($type == 'order_status_change') {
                if (!isset($push_notification_settings['order_status_change_enable']) || $push_notification_settings['order_status_change_enable'] == 0) {
                    return false;
                } else {
                    $title = $push_notification_settings['order_status_change_title'];
                    $message = str_replace(
                            '{{STATUS}}', $order_status, $push_notification_settings['order_status_change_message']
                    );
                    $push_type = 'order_status_changed';
                }
                
            } elseif ($type == 'kb_abandoned_cart') {
                if (!isset($push_notification_settings['abandoned_cart_enable']) || $push_notification_settings['abandoned_cart_enable'] == 0) {
                    return false;
                } else {
                    $title = $push_notification_settings['abandoned_cart_title'];
                    $message = $push_notification_settings['abandoned_cart_message'];
                    $push_type = 'kb_abandoned_cart';
                }
                
            }


            $user_id = "";

            $firebase_data = array();
            $firebase_data['data']['title'] = $title;
            $firebase_data['data']['is_background'] = false;
            $firebase_data['data']['message'] = $message;
            $firebase_data['data']['image'] = '';
            $firebase_data['data']['payload'] = '';
            $firebase_data['data']['user_id'] = $user_id;
            $firebase_data['data']['push_type'] = $push_type;
            $firebase_data['data']['cart_id'] = '';
            $firebase_data['data']['order_id'] = $order_id;
            $firebase_data['data']['email_id'] = $email;

            if ($fcm_ids = $this->getFcmIdByEmail($email)) {
                foreach ($fcm_ids as $data) {
                    $this->sp_objectManager->get('\Knowband\Mobileappbuilder\Helper\Firebase')->sendMultiple($data['fcm_id'], $firebase_data, $firebase_server_key, $data['device_type']);
                }
            }
            
        }
    }

    /*
     * function to get fcm details by email
     */

    public function getFcmIdByEmail($email)
    {
        if ($email) {
             $fcm_collection = $this->fcmModel->getCollection()
                    ->addFieldToFilter("kb_email", ["eq" => $email]);
            $fcm_collection->getSelect()->group("fcm_id");
            if ($fcm_collection->getSize() > 0) {
                return $fcm_collection->getData();
            } else {
                return false;
            }
        }
    }
    
    /*
     * Function to get order status detail id from order id
     */

    public function getOrderStatusIdByOrderId($order_id)
    {
        $orderstatus_collection = $this->orderStatusModel->getCollection();
        $where = ' main_table.order_id = ' . $order_id;
        $orderstatus_collection->getSelect()->where($where);
        if ($orderstatus_collection->getData()) {
            $data = $orderstatus_collection->getData();
            return $data[0]['kb_orderstatus_id'];
        } else {
            return false;
        }
        
    }
    
    /**
     * Function to get the layouts of home page
     * @return array
     */
    public function getHomePageLayout(){
        $result = [];
        $collection = $this->sp_layoutModel->getCollection();
        if($collection->getSize()){
            foreach($collection  as $layout){
                $result[$layout->getIdLayout()] = $layout->getLayoutName();
            }
        }
        unset($collection);
        return $result;
    }
    
    /**
     * Function to get list of shipping methods
     * @param int $store store
     * @return array
     */
    public function getShippingMethods($store = null) {
        $carriers = [];
        $shipping_methods = [];
        try {
            $config = $this->sp_scopeConfig->getValue('carriers', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
            foreach (array_keys($config) as $carrierCode) {
                if ($this->scopeConfig->isSetFlag('carriers/' . $carrierCode . '/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store)) {
                    $carrierModel = $this->sp_objectManager->create('\Magento\Shipping\Model\CarrierFactory')->create($carrierCode, $store);
                    if ($carrierModel) {
                        $carriers[$carrierCode] = $carrierModel;
                    }
                }
            }
            foreach ($carriers as $_ccode => $_carrier) {
                if ($_methods = $_carrier->getAllowedMethods()) {
                    foreach ($_methods as $_mcode => $_method) {
                        $_code = $_ccode . '_' . $_mcode;
                    }
                    $_title = $this->sp_scopeConfig->getValue("carriers/$_ccode/title");
                    if (!$_title) {
                        $_title = $_ccode;
                    }
                    $shipping_methods[$_code] = $_title;
                }
            }
        } catch (\Magento\Framework\Exception\InputException $ex) {
        } catch (\Exception $ex) {
        }
        return $shipping_methods;
    }

}
