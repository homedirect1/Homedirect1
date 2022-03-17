<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package  Ced_GShop
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GShop\Helper;

use Ced\GShop\Block\Extensions;
use Magento\Backend\Model\Session;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\Message\Manager;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Data
 * @package Ced\GShop\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_PATH_PRODUCT_PRICE = 'gshop_config/product_upload/product_price';
    const CONFIG_PATH_PRODUCT_PRICE_INCREASE_FIXED = 'gshop_config/product_upload/fix_price';
    const CONFIG_PATH_PRODUCT_PRICE_INCREASE_PERCENTAGE = 'gshop_config/product_upload/percentage_price';
    const GXPRESS_DEBUGMODE = 'gshop_config/product_upload/debugmode';
    const GXPRESS_SALEPRICE_FLAG = 'gshop_config/product_upload/sale_price';
    const GXPRESS_PRODUCT_EXPIREON = 'gshop_config/product_upload/product_expire';
    const GXPRESS_PRODUCT_SALEPRICE_EXPIREON = 'gshop_config/product_upload/sale_price_expire';
    const GXPRESS_PRODUCT_TAXPRICE = 'gshop_config/product_upload/tax_price';
    const GXPRESS_PRODUCT_TAXPERCENTAGE = 'gshop_config/product_upload/tax_percentage';
    const GXPRESS_PRODUCT_MSI_FLAG = 'gshop_config/product_upload/msi_qty';
    const GXPRESS_PRODUCT_BUFFER = 'gshop_config/product_upload/buffer_qty';
    const GXPRESS_PRODUCT_BUFFER_ATTR = 'gshop_config/product_upload/buffer_attribute';

    /**
     * @var mixed
     */
    public $adminSession;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;
    /**
     * @var mixed
     */
    public $debugMode;

    /**
     * @var Manager
     */
    public $messageManager;
    /**
     * @var DirectoryList
     */
    public $directoryList;
    /**
     * Json Parser
     * @var \Magento\Framework\Json\Helper\Data
     */
    public $json;
    /**
     * @var int
     */
    public $compatLevel;
    /**
     * @var mixed
     */
    public $siteID;
    /**
     * @var mixed
     */
    public $devID;
    /**
     * @var mixed
     */
    public $environment;
    /**
     * @var mixed
     */
    public $token;
    /**
     * @var mixed
     */
    public $developer;

    /**
     * @var mixed
     */
    public $appId;

    /**
     * @var mixed
     */
    public $certID;
    /**
     * @var mixed
     */
    public $ruNameID;

    /**
     * @var Filesystem
     */
    public $filesystem;

    /**
     * @var Feed
     */
    public $feedHelper;

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var Config
     */
    public $configResourceModel;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    public $backendHelper;

    /**
     * @var \Ced\GShop\Helper\MultiAccount
     */
    protected $multiAccountHelper;

    public $destinations;
    /**
     * @var mixed
     */
    public $processMode;
    public $chunkSize;
//    public $nodes;
    public $skus;

    public $category;

    /**
     * Data constructor.
     * @param Context $context
     * @param Manager $manager
     * @param DirectoryList $directoryList
     * @param \Magento\Framework\Json\Helper\Data $json
     * @param Session $session
     * @param Filesystem $filesystem
     * @param Feed $feedHelper
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     * @param \Magento\Backend\Helper\Data $helper
     * @param Logger
     */
    public function __construct(
        Context $context,
        Manager $manager,
        DirectoryList $directoryList,
        \Magento\Framework\Json\Helper\Data $json,
        Session $session,
        Filesystem $filesystem,
        Feed $feedHelper,
        StoreManagerInterface $storeManager,
        Config $config,
        \Magento\Framework\UrlInterface $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Filesystem\Io\File $fileIo,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Registry $registry,
        \Ced\GShop\Helper\MultiAccount $multiAccountHelper,
        \Ced\GShop\Helper\GXpress $GXpress,
        \Ced\GShop\Helper\Logger $logger
    )
    {
        parent::__construct($context);
        $this->_coreRegistry = $registry;
        $this->messageManager = $manager;
        $this->objectManager = $objectManager;
        $this->directoryList = $directoryList;
        $this->json = $json;
        $this->adminSession = $session;
        $this->configResourceModel = $config;
        $this->backendHelper = $helper;
        $this->filesystem = $filesystem;
        $this->feedHelper = $feedHelper;
        $this->storeManager = $storeManager;
        $this->fileIo = $fileIo;
        $this->dateTime = $dateTime;
        $this->multiAccountHelper = $multiAccountHelper;
        $this->timestamp = (string)$this->dateTime->gmtTimestamp();
        $this->devID = $this->scopeConfig->getValue('gshop_config/gxpress_setting/dev_id');
        $this->developer = $this->scopeConfig->getValue('gshop_config/gxpress_setting/dev_acc');
        $this->appId = $this->scopeConfig->getValue('gshop_config/gxpress_setting/app_id');
        $this->certID = $this->scopeConfig->getValue('gshop_config/gxpress_setting/cert_id');
        $this->ruNameID = $this->scopeConfig->getValue('gshop_config/gxpress_setting/ru_name');
        $this->processMode = $this->scopeConfig->getValue('gshop_config/product_upload/process');
        $this->chunkSize = $this->scopeConfig->getValue('gshop_config/product_upload/chunk_size');
        $this->compatLevel = 989;
        $this->GXpress = $GXpress;
        $this->logger = $logger;
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $this->debugMode = $this->scopeConfig->getValue(self::GXPRESS_DEBUGMODE, $storeScope);
        $this->skus = [];
    }

    public function updateAccountVariable()
    {
        $account = false;
        if ($this->_coreRegistry->registry('google_account')) {
            $account = $this->_coreRegistry->registry('google_account');
        }
        $this->environment = ($account) ? trim($account->getAccountEnv()) : '';
        $this->token = ($account) ? trim($account->getAccountToken()) : '';
    }

    /**
     * @return $this|bool
     */
    public function checkForLicence()
    {
        if ($this->_request->getModuleName() != 'gxpress') {
            return $this;
        }
        $modules = $this->feedHelper->getCedCommerceExtensions();
        foreach ($modules as $moduleName => $releaseVersion) {
            $m = strtolower($moduleName);
            if (!preg_match('/ced/i', $m)) {
                return $this;
            }

            $h = $this->scopeConfig->getValue(Extensions::HASH_PATH_PREFIX . $m . '_hash');

            for ($i = 1; $i <= (int)$this->scopeConfig->getValue(Extensions::HASH_PATH_PREFIX . $m . '_level'); $i++) {
                $h = base64_decode($h);
            }

            $h = json_decode($h, true);
            if ($moduleName == "Magento2_Ced_GShop") {
                if (is_array($h) && isset($h['domain']) && isset($h['module_name']) && isset($h['license']) && strtolower($h['module_name']) == $m && $h['license'] == $this->scopeConfig->getValue(\Ced\GShop\Block\Extensions::HASH_PATH_PREFIX . $m)) {
                    return $this;
                } else {
                    return false;
                }
            }
        }
        return $this;
    }

    /**
     * @param $path
     * @param null $storeId
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreConfig($path, $storeId = null)
    {
        $store = $this->storeManager->getStore($storeId);
        return $this->scopeConfig->getValue($path, 'store', $store->getCode());
    }

    /**
     * @param $path
     * @param string $code
     * @param string $type
     * @return bool|mixed|string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function loadFile($path, $code = '', $type = '')
    {
        if (!empty($code)) {
            $path = $this->directoryList->getPath($code) . "/" . $path;
        }
        if (file_exists($path)) {
            $pathInfo = pathinfo($path);
            if ($pathInfo['extension'] == 'json') {
                $myfile = fopen($path, "r");
                $data = fread($myfile, filesize($path));
                fclose($myfile);
                if (!empty($data)) {
                    $data = empty($type) ? $this->json->jsonDecode($data) : $data;
                    return $data;
                }
            }
        }
        return false;
    }

    /**
     * @param $ids
     * @param bool $async
     * @param bool $release
     * @return array|bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createProductOnGXpress($ids, $async = false, $release = false)
    {
        $returnData = [];
        $response['error'] = [];
        $response['success'] = [];
        if (isset($ids) && count($ids) > 0) {
            $productToUpload = [];
            $key = 1;
            if (isset($ids)) {
                foreach ($ids as $accId => $accountid) {
                    foreach ($accountid as $key => $id) {
                        $this->updateAccountVariable();
                        /** @var \Magento\Catalog\Model\Product $product */
                        $product = $this->objectManager->create('Magento\Catalog\Model\Product')
                            ->load($id);
                        $url = [];
                        foreach ($product->getMediaGalleryImages() as $gallery) {
                            /*$url[] = $this->storeManager->getStore()->getBaseUrl().'media/catalog/product'
                            .$gallery->getFile();*/
                            $url[] = $gallery->getUrl();
                        }
                        $attributes = $this->getMappedNodes($product, $accId);
                        $errorsForChild = false;
                        if ($product->getTypeId() == "configurable") {
                            $productType = $product->getTypeInstance();
                            $configProd = $productType->getUsedProducts($product);
                            if (!count($configProd)) {
                                $response['error']['sku'][] = $product->getSku();
                                $errors['sdk']['id'] = $product->getId();
                                $errors['sdk']['sku'] = $product->getSku();
                                $errors['sdk']['errors'] = ['Child Product not found'];
                                $this->updateAttribute($product, 'google_listing_error_' . $accId, json_encode($errors));
                                continue;
                            }
                            foreach ($configProd as $childKey => $childprod) {
                                $productToUpload[] = $this->getSimpleProduct($childprod, $attributes, $accId, $url, $product->getId());
                            }
                            if ($this->scopeConfig->getValue(self::GXPRESS_PRODUCT_EXPIREON)) {
                                $nextUploadOn = $this->scopeConfig->getValue(self::GXPRESS_PRODUCT_EXPIREON);
                            } else {
                                $nextUploadOn = date('Y-m-d', strtotime(date('Y-m-d')));
                            }
                            $this->updateAttribute($product, 'google_listing_error_' . $accId, '');
                            $this->updateAttribute($product, 'google_product_expires', $nextUploadOn);
                            $this->updateStatus($product->getId(), 4);
                        } else {
                            $productToUpload[] = $this->getSimpleProduct($product, $attributes, $accId, $url);
                        }
                    }
                    try {
                        if ($this->getProcessMode() == 'csv') {
                            $account = $this->objectManager->create(\Ced\GShop\Model\Accounts::class)->load($accId);
                            $accountCode = $account->getAccountCode();
                            $targetCountry = $account->getTargetCountry();
                            $contentLanguage = $account->getContentLanguage();
                            $fileName = 'googleshopping_' . $accountCode . '_' . $contentLanguage . '_' . $targetCountry . '.txt';
                            $dirPath = BP . '/pub/media/ced_google';
                            $filePath = $dirPath . '/' . $fileName;
                            if (!file_exists($dirPath)) {
                                mkdir($dirPath, 0777, true);
                            } else {
                                chmod($dirPath, 0777);
                            }
                            $fp = fopen($filePath, "a");
                            foreach ($productToUpload as $productData) {
                                foreach ($productData as $datakey => $data) {
                                    if ($datakey == 'shippingHeight' ||
                                        $datakey == 'shippingLength' ||
                                        $datakey == 'shippingWeight' ||
                                        $datakey == 'shippingWidth'
                                    ) {
                                        $data = is_array($data) ? implode(" ", $data) : $data;
                                    } else {
                                        $data = is_array($data) ? implode(",", $data) : $data;
                                    }
                                    $productData[$datakey] = $data;
                                }
                                fputcsv($fp, $productData, chr(9));
                            }
                            $response['success']['sku'] = $this->skus;
                        } else {
                            $this->adminSession->setAccountId($accId);
                            $returnData = $this->objectManager->get('Ced\GShop\Helper\GXpresslib')
                                ->uploadProductOnGXpress($productToUpload);
                            if (is_bool($returnData)) {
                                $response['error']['sku'][] = $product->getSku();
                                $response['success'] = [];
                            } elseif (get_class($returnData) == 'Google_Service_ShoppingContent_ProductsCustomBatchResponse' ||
                                get_class($returnData) == 'Google\Service\ShoppingContent\ProductsCustomBatchResponse') {
                                foreach ($returnData['entries'] as $datum => $entry) {
                                    $response['success']['sku'][] = isset($returnData['entries'][$datum]['product']['offerId'])
                                        ? $returnData['entries'][$datum]['product']['offerId'] : '';
                                }
                            } else {
                                $errors['sdk']['errors'] = ['Please install Google SDK first'];
                                $response['error']['sku'][] = $product->getSku();
                                $errors['sdk']['id'] = $product->getId();
                                $errors['sdk']['sku'] = $product->getSku();
                                $this->updateAttribute($product, 'google_listing_error_' . $accId, json_encode($errors));
                            }
                        }
                    } catch (\Exception $e) {
                        $this->logger(
                            "Google Express",
                            "upload Product",
                            json_encode($returnData),
                            1
                        );
                        continue;
                    }
                }
            }
            return $response;
        }
        return $response;
    }

    public function getMappedNodes($product = null, $accId = null)
    {
        $profileAccountAttr = $this->multiAccountHelper->getProfileAttrForAcc($accId);
        $profileId = $product->getData($profileAccountAttr);
        $profile = $this->objectManager->create('Ced\GShop\Model\Profile')->load($profileId);
        $category = $profile->getData('profile_category');
        $this->category = implode(" > ",array_filter(explode(",",str_replace("[","",str_replace("]","",str_replace('"',"",$category))))));
        $attributes = $customAttrs = [];
        //First check for the parent product - High priority
        if (isset($product) && sizeof($product->getData()) > 0) {
            $customAttrs = $this->getGXpressAttributes(
                $product,
                $profile,
                [
                    'required' => false, 'mapped' => true, 'validation' => true
                ]
            );
            $attributes = $this->getGXpressAttributes(
                $product,
                $profile,
                [
                    'required' => false, 'mapped' => true, 'validation' => false
                ]
            );
        }
        //Second check for the child product - Low priority
        if (!$attributes) {
            $customAttrs = $this->getGXpressAttributes(
                $product->getId(),
                $profile,
                [
                    'required' => false, 'mapped' => true, 'validation' => true
                ]
            );
            $attributes = $this->getGXpressAttributes(
                $product->getId(),
                $profile,
                [
                    'required' => false, 'mapped' => true, 'validation' => false
                ]
            );
        }

        $randomCounter = 0;

        foreach ($customAttrs as $k => $customAttr) {
            if ($customAttr['magento_attribute_code'] == 'default') {
                $product->setData($randomCounter . '_ced', $customAttr['default']);
                $attributes[$customAttr['gxpress_attribute_name']] = $randomCounter . '_ced';
                $randomCounter++;
            }
        }
        return $attributes;
    }

    public function getSimpleProduct($product, $attributes, $accId, $url, $parent = null)
    {
        $account = $this->objectManager->get(\Ced\GShop\Model\Accounts::class)->load($accId);
        $returnData = [];
        $productToUpload = [];
        $productIdentifier = [];
        /** @var \Ced\GShop\Helper\Barcode $barcode */
        $barcode = $this->objectManager->create('Ced\GShop\Helper\Barcode');
        $barcode->setBarcode(
            $product->getData($attributes['productId'])
        );

        if ($barcode->isValid()) {
            $productIdentifier = [
                'productIdentifier' => [
                    'productIdType' => 'gtin',
                    'productId' => $barcode->getBarcode(),
                ]
            ];
        } else {
            $productIdentifier = [
                'productIdentifier' => [
                    'productIdType' => 'mpn',
                    'productId' => $product->getData('pim_sku'),
                ]
            ];
        }

        $imageUrl = isset($url[0]) ? $url[0] : '';
        $product->setData(
            $attributes['description'],
            strip_tags(substr($product->getData($attributes['description']), 0, 4999))
        );
        try {
            foreach ($attributes as $googleexpress_attribute_name => $magento_attribute_code) {
                if ($googleexpress_attribute_name == "price") {
                    $currencysymbol = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface');
                    if ($product->getData('google_map_price') > $product->getData($magento_attribute_code)) {
                        $price = $product->getData('google_map_price');
                    } else {
                        $price = $product->getData($magento_attribute_code);
                    }
                    if ($this->getProcessMode() == 'csv') {
                        $productToUpload[$googleexpress_attribute_name] =
                            (float)$price . ' ' . $currencysymbol->getStore()->getCurrentCurrencyCode();
                        continue;
                    }
                    $productToUpload[$googleexpress_attribute_name]['value'] = $price;
                    $productToUpload[$googleexpress_attribute_name]['currency'] = $currencysymbol->getStore()->getCurrentCurrencyCode();
                    continue;
                }

                if ($googleexpress_attribute_name == "shippingWeight" && $product->getData($magento_attribute_code)) {
                    if ($this->getProcessMode() == 'csv') {
                        $productToUpload[$googleexpress_attribute_name] =
                            (float)$product->getData($magento_attribute_code) . ' lb';
                        continue;
                    }
                    $productToUpload[$googleexpress_attribute_name]['value'] = (float)$product->getData($magento_attribute_code);
                    $productToUpload[$googleexpress_attribute_name]['unit'] = 'lb';
                    continue;
                }

                if ($googleexpress_attribute_name == "shippingLength" && $product->getData($magento_attribute_code)) {
                    if ($this->getProcessMode() == 'csv') {
                        $productToUpload[$googleexpress_attribute_name] =
                            $product->getData($magento_attribute_code);
                        continue;
                    }
                    $productToUpload[$googleexpress_attribute_name]['value'] = $product->getData($magento_attribute_code);
                    $productToUpload[$googleexpress_attribute_name]['unit'] = 'cm';
                    continue;
                }

                if ($googleexpress_attribute_name == "productId") {
                    if ($productIdentifier['productIdentifier']['productIdType'] == 'gtin') {
                        $productToUpload['mpn'] = $product->getData('pim_sku');
                    } else {
                        $productToUpload['gtin'] = null;
                    }
                    $productToUpload[$productIdentifier['productIdentifier']['productIdType']] =
                        $productIdentifier['productIdentifier']['productId'];
                    continue;
                }
                if ($googleexpress_attribute_name == "promotionIds") {
                    $productToUpload[$googleexpress_attribute_name] =
                        [$this->getMappedAttributeValue($magento_attribute_code, $product)];
                    continue;
                }
                $productToUpload[$googleexpress_attribute_name] =
                    $this->getMappedAttributeValue($magento_attribute_code, $product);
            }
//            }

        } catch (\Exception $e) {
        } catch (\Error $e) {
        }

        if ($productToUpload) {
            if (array_key_exists('salePrice', $productToUpload) &&
                isset($productToUpload['salePrice'])) {
                $salePrice = $productToUpload['salePrice'];
                unset($productToUpload['salePrice']);
                if (!$salePrice) {
                    $salePrice = $productToUpload['price']['value'];
                }
                if ($this->getProcessMode() == 'csv') {
                    $productToUpload['salePrice'] =
                        $salePrice . ' ' . $currencysymbol->getStore()->getCurrentCurrencyCode();
                } else {
                    $productToUpload['salePrice']['value'] = $salePrice;
                    $productToUpload['salePrice']['currency'] = $currencysymbol->getStore()->getCurrentCurrencyCode();
                }
            }
            $productToUpload['imageLink'] = "$imageUrl";
            $qty = $this->getQuantity($product, $account->getData('account_store'));
            $productToUpload['availability'] = $qty ? 'in stock' : 'out of stock';
//            $productToUpload['link'] = $product->getProductUrl();
//            $admin = $this->objectManager->create(\Magento\Backend\Helper\Data::class)->getAreaFrontName();
//            if (strpos($product->getProductUrl(), $admin) === true) {
//                $productToUpload['link'] = $this->storeManager->getStore()->getBaseUrl() .
//                    'catalog/product/view/id/' . $product->getId();
//            }
//            $productToUpload['mobileLink'] = $productToUpload['link'];
            if (count($url) > 1 && $this->getProcessMode() == 'csv') {
                $productToUpload['additionalImageLinks'] = implode(",", array_splice($url, 1));
            } elseif ($this->getProcessMode() == 'api') {
                $productToUpload['additionalImageLinks'] = array_splice($url, 1);
            } else {
                $productToUpload['additionalImageLinks'] = '    ';
            }

            if ($this->getProcessMode() == 'api') {
                $productToUpload['targetCountry'] = $account->getTargetCountry();
                $productToUpload['contentLanguage'] = $account->getContentLanguage();
            }
            $productToUpload['mpn'] = $product->getData('pim_sku');
            if ($this->getProcessMode() == 'api') {
                $productToUpload['includedDestinations'] = explode(",", $account->getIncludedDestination());
                $productToUpload['identifierExists'] = true;
                $productToUpload['channel'] = 'online';
                $productToUpload['offerId'] = $productToUpload['id'];
            }
            $productToUpload['google_product_category'] = $product->getData('google_category_id');
            if(array_key_exists('google_product_category',$productToUpload) &&
                (empty($productToUpload['google_product_category']) ||
                    is_null($productToUpload['google_product_category']))) {
                $productToUpload['google_product_category'] = $this->category;
            }
            if ($parent) {
                $productToUpload['itemGroupId'] = $parent;
            } else {
                $productToUpload['itemGroupId'] = $product->getId();
            }
            if ($product->getData('google_is_bundle')) {
                $productToUpload['isBundle'] = $product->getData('google_is_bundle');
            } else {
                $productToUpload['isBundle'] = '';
            }
            $targetDestination = explode(',', $account->getIncludedDestination());
            if ($this->scopeConfig->getValue(self::GXPRESS_PRODUCT_TAXPRICE)) {
                $taxPercentage = isset($productToUpload['taxes']) ?
                    $productToUpload['taxes'] : $this->scopeConfig->getValue(self::GXPRESS_PRODUCT_TAXPERCENTAGE);
                if ($this->getProcessMode() == 'csv') {
                    $taxes = $account->getTargetCountry() .
                        '::' . $taxPercentage . ':' .
                        (bool)$this->scopeConfig->getValue(self::GXPRESS_PRODUCT_TAXPRICE);
                } else {
                    $taxes = [[
                        'country' => $account->getTargetCountry(),
                        'rate' => $taxPercentage,
                        'taxShip' => $this->scopeConfig->getValue(self::GXPRESS_PRODUCT_TAXPRICE) ? 'yes' : 'no'
                    ]];
                }
                $productToUpload['taxes'] = $taxes;
            } elseif (isset($productToUpload['taxes'])) {
                unset($productToUpload['taxes']);
            }

            if ($this->scopeConfig->getValue(self::GXPRESS_SALEPRICE_FLAG) &&
                !empty($this->scopeConfig->getValue(self::GXPRESS_PRODUCT_SALEPRICE_EXPIREON))) {
                $productToUpload['salePriceEffectiveDate'] = date("Y-m-d") . '/' .
                    $this->scopeConfig->getValue(self::GXPRESS_PRODUCT_SALEPRICE_EXPIREON);
            }
            $productToUpload['sale price effective date'] =
                $product->getData('adwords_tp_sale_price_effective_date') ?
                    $product->getData('adwords_tp_sale_price_effective_date') : '';
            if (isset($productToUpload['sale price effective date']) &&
                empty($productToUpload['sale price effective date'])) {
                unset($productToUpload['sale price effective date']);
            }
            if (array_key_exists('material', $productToUpload)
                && isset($productToUpload['material'])
                && is_array($productToUpload['material'])
            ) {
                $productToUpload['material'] = implode(',', $productToUpload['material']);
            }

            if (array_key_exists('shippingWidth', $productToUpload) &&
                isset($productToUpload['shippingWidth'])) {
                $shippingWidth['value'] = $productToUpload['shippingWidth'];
                $shippingWidth['unit'] = 'cm';
                if ($this->getProcessMode() == 'csv') {
                    $productToUpload['shippingWidth'] = $shippingWidth['value'] . ' ' . $shippingWidth['unit'];
                } else {
                    $productToUpload['shippingWidth'] = $shippingWidth;
                }
            }

            if (array_key_exists('shippingLength', $productToUpload) &&
                isset($productToUpload['shippingLength'])) {
                $shippingLength['value'] = $productToUpload['shippingLength'];
                $shippingLength['unit'] = 'cm';
                if ($this->getProcessMode() == 'csv') {
                    $productToUpload['shippingLength'] = $shippingLength['value'] . ' ' . $shippingLength['unit'];
                } else {
                    $productToUpload['shippingLength'] = $shippingLength;
                }
            }
            if (array_key_exists('shippingHeight', $productToUpload) &&
                isset($productToUpload['shippingHeight'])) {
                $shippingHeight['value'] = $productToUpload['shippingHeight'];
                $shippingHeight['unit'] = 'cm';
                if ($this->getProcessMode() == 'csv') {
                    $productToUpload['shippingHeight'] = $shippingHeight['value'] . ' ' . $shippingHeight['unit'];
                } else {
                    $productToUpload['shippingHeight'] = $shippingHeight;
                }
            }
            if (array_key_exists('shippingWeight', $productToUpload) &&
                isset($productToUpload['shippingWeight'])) {
                $shippingWeight['value'] = (float)$productToUpload['shippingWeight'];
                $shippingWeight['unit'] = 'lb';
                if ($this->getProcessMode() == 'csv') {
                    $productToUpload['shippingWeight'] = $shippingWeight['value'] . ' ' . $shippingWeight['unit'];
                } else {
                    $productToUpload['shippingWeight'] = $shippingWeight;
                }
            }

            if (isset($errorsForChild[$product->getSku()])) {
                unset($errorsForChild[$product->getSku()]);
            }
            if (empty($errorsForChild)) {
                $errorsForChild = '["valid"]';
            }
        }

        if (count($productToUpload) <= 1) {
            return $returnData;
        }
        try {
            if (isset($productToUpload['sizes']) && !is_array($productToUpload['sizes'])) {
                $productToUpload['sizes'] = [$productToUpload['sizes']];
            }
            if ($this->scopeConfig->getValue(self::GXPRESS_PRODUCT_EXPIREON)) {
                $nextUploadOn = $this->scopeConfig->getValue(self::GXPRESS_PRODUCT_EXPIREON);
            } else {
                $nextUploadOn = date('Y-m-d', strtotime(date('Y-m-d')));
            }
            $this->updateAttribute($product, 'google_listing_error_' . $accId, '');
            $this->updateAttribute($product, 'google_product_expires', $nextUploadOn);
            $response['success']['sku'][] = isset($productToUpload['offerId'])
                ? $productToUpload['offerId'] : $product->getSku();
            $this->updateStatus($product->getId(), 4);
        } catch (\Exception $e) {
            $this->logger(
                "Google Express",
                "upload Product",
                json_encode($returnData),
                1
            );
        }
        array_push($this->skus, $productToUpload['id']);
        ksort($productToUpload);
        return $productToUpload;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param $store
     * @return int
     */
    public function getQuantity($product, $store)
    {
        $msiQtyFlag = $this->scopeConfig->getValue(self::GXPRESS_PRODUCT_MSI_FLAG);//getMSIQtyFlag();
        $bufferQtyFlag = $this->scopeConfig->getValue(self::GXPRESS_PRODUCT_BUFFER);
        $msiModulesEnabledFlag = $this->checkMsiModulesEnabled();
        $stockItemRepository = $this->objectManager->create('\Magento\CatalogInventory\Api\StockStateInterface');
        $stockQty = $stockItemRepository->getStockQty($product->getId());
        $qty = $product->getData('quantity_and_stock_status');
        if (!$stockQty || $stockQty == 0) {
            $stockQty = isset($qty['qty']) ? $qty['qty'] : 0;
        }

        if ($msiModulesEnabledFlag) {
            if ($msiQtyFlag) {
                $stockId = $this->objectManager->create('\Magento\InventorySalesApi\Model\GetAssignedStockIdForWebsiteInterface')
                    ->execute($product->getStore()->getWebsite()->getCode());
                $saleQty = $this->objectManager->create('Magento\InventorySalesApi\Api\GetProductSalableQtyInterface')
                    ->execute($product->getSku(), $stockId);
                $stockQty = $saleQty;
            }
        }
        if ($bufferQtyFlag) {
            $bufferQty = (int)$product->getData(
                $this->scopeConfig->getValue(self::GXPRESS_PRODUCT_BUFFER_ATTR)
            );
            $stockQty = $stockQty - $bufferQty;
        }
        if ($stockQty < 0) {
            $stockQty = 0;
        }
        return $stockQty;
    }

    public function checkMsiModulesEnabled()
    {
        if ($this->_moduleManager->isEnabled('Magento_InventoryApi')
            && $this->_moduleManager->isEnabled('Magento_InventorySalesApi')
            && $this->_moduleManager->isEnabled('Magento_InventorySalesAdminUi')
            && $this->_moduleManager->isEnabled('Magento_InventoryCatalogAdminUi')
            && $this->_moduleManager->isEnabled('Magento_InventoryReservationsApi')
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function getMappedAttributeValue($magentoAttribute, $product)
    {
        if (isset($magentoAttribute) && !empty($magentoAttribute)) {
            if ($product->getData($magentoAttribute) == "") {
                return null;
            }

            $attr = $product->getResource()->getAttribute($magentoAttribute);
            if ($attr && ($attr->usesSource() || $attr->getData('frontend_input') == 'select')) {
                if ($magentoAttribute == "quantity_and_stock_status") {
                    return $product->getData($magentoAttribute);
                }
                $productAttributeValue = $attr->getSource()->getOptionText($product->getData($magentoAttribute));
            } else {
                $productAttributeValue = $product->getData($magentoAttribute);
            }
            return $productAttributeValue;
        } else {
            return $product->getData($magentoAttribute);
        }
    }

    public function logger(
        $type = "Test",
        $subType = "Test",
        $response = [],
        $comment = "",
        $forcedLog = false
    )
    {
        if ($this->debugMode || $forcedLog) {
            $this->objectManager->get('Ced\GShop\Helper\Logger')
                ->addError($type . $response, ['path' => __METHOD__]);
            return true;
        }

        return false;
    }

    public function updateAttribute($product, $code, $value, $accountStoreId = null)
    {
        // Saving in default store i.e admin store id = 0.

        if (!$accountStoreId) {
            $accountStoreId = $this->storeManager->getStore()->getId();
        }

        $product->addAttributeUpdate(
            $code,
            $value,
            $accountStoreId
        );

        // Saving mapped store in case it is different.
        if ($this->storeManager->getStore()->getId() != $accountStoreId) {
            $product->addAttributeUpdate(
                $code,
                $value,
                $this->storeManager->getStore()->getId()
            );
        }
    }

    public function updateStatus(
        $productIds = [],
        $status = 1
    )
    {
        if (is_string($productIds)) {
            $productIds = [$productIds];
        }

        try {
            $account = $this->_coreRegistry->registry('google_account');
            $accountId = ($account) ? $account->getId() : '';
            $prodStatusAttr = $this->multiAccountHelper->getProdStatusAttrForAcc($accountId);
            $productIds = array_unique($productIds);
            $productAction = $this->objectManager->get('Magento\Catalog\Model\Product\Action');
            $productAction->updateAttributes(
                $productIds,
                [$prodStatusAttr => $status],
                $this->storeManager->getStore()->getId()
            );

            if ($this->storeManager->getStore()->getId() != $this->storeManager->getStore()->getId()) {
                $productAction->updateAttributes(
                    $productIds,
                    [$prodStatusAttr => $status],
                    $this->storeManager->getStore()->getId()
                );
            }
        } catch (\Exception $e) {
            $this->logger('Product status update.', 'Failure', $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function getGXpressAttributes(
        $productId,
        $profile,
        $params =
        ['required' => true, 'mapped' => false, 'validation' => false]
    )
    {
        $attributes = false;
        if ($productId) {
            if (empty($profile) || (isset($profile['profile_status']) && !$profile['profile_status'])) {
                return false;
            }
            $profileData = json_decode($profile->getData('profile_cat_attribute'), true);

            $googleexpressAttributes = isset($profileData['required_attributes']) ?
                $profileData['required_attributes'] : [];

            if (isset($params['required']) && isset($profileData['optional_attributes']) && $params['required'] == false) {
                $googleexpressAttributes = array_merge($googleexpressAttributes, $profileData['optional_attributes']);
            }

            if ($params['validation'] == true) {
                $attributes = $googleexpressAttributes;
            } else {
                foreach ($googleexpressAttributes as $value) {
                    $attributes[$value['gxpress_attribute_name']] = $value['magento_attribute_code'];
                }
            }
            return $attributes;
        }

        return false;
    }

    public function responseParse($response = '', $type = null, $filePath = '')
    {
        if ($type) {
            try {
                $accountId = 0;
                $currentAccount = $this->_coreRegistry->registry('google_account');
                if ($currentAccount) {
                    $accountId = $currentAccount->getId();
                }
                $feedModel = $this->objectManager->create('\Ced\GShop\Model\Feeds');
                $feedModel->setData('feed_date', date('Y-m-d H:i:s'));
                $feedModel->setData('feed_type', $type);
                $feedModel->setData('feed_source', isset($response->Ack) ? $response->Ack : 'Unknown');
                $feedModel->setData('feed_errors', $this->json->jsonEncode($response));
                $feedModel->setData('feed_file', $filePath);
                $feedModel->setData('account_id', $accountId);
                $feedModel->save();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $name
     * @param string $code
     * @return array|string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function createDir($name = 'gxpress', $code = 'var')
    {
        $path = $this->directoryList->getPath($code) . "/" . $name;
        if (file_exists($path)) {
            return ['status' => true, 'path' => $path, 'action' => 'dir_exists'];
        } else {
            try {
                $this->fileIo->mkdir($path, 0775, true);
                return ['status' => true, 'path' => $path, 'action' => 'dir_created'];
            } catch (\Exception $e) {
                return $code . '/' . $name . "Directory Creation Failed.";
            }
        }
    }

    public function setAccountSession()
    {
        $accountId = '';
        $this->adminSession->unsAccountId();
        $params = $this->_getRequest()->getParams();
        if (isset($params['account_id']) && $params['account_id'] > 0) {
            $accountId = $params['account_id'];
        } else {
            $accountId = $this->scopeConfig->getValue('gshop_config/gshop_setting/primary_account');
            if (!$accountId) {
                $accounts = $this->multiAccountHelper->getAllAccounts();
                if ($accounts) {
                    $accountId = $accounts->getFirstItem()->getId();
                }
            }
        }
        $this->adminSession->setAccountId($accountId);
        return $accountId;
    }

    public function getAccountSession()
    {
        $accountId = '';
        $accountId = $this->adminSession->getAccountId();
        if (!$accountId) {
            $accountId = $this->setAccountSession();
        }
        return $accountId;
    }

    public function getNoticeurl()
    {
        $admin = $this->objectManager->create(\Magento\Backend\Helper\Data::class)->getAreaFrontName();
        /** @var \Magento\Backend\Model\UrlInterface $urlInterface */
        $urlInterface = $this->objectManager->create(\Magento\Backend\Model\UrlInterface::class);
        $route = "gxpress/request/help";
        $url = $urlInterface->getRouteUrl($route, ['key' => $urlInterface->getSecretKey("gxpress", "request", "help")]);
        return __(
            'If you want any help or need to manage google shopping orders, please contact with <a href="' . $url . '">cedcommerce.</a>'
        );
    }

    public function getProcessMode()
    {
        return $this->processMode;
    }

    public function getchunkSize()
    {
        return $this->chunkSize;
    }
}
