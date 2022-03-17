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

use Magento\Framework\App\Helper\Context;

class GXpresslib extends \Magento\Framework\App\Helper\AbstractHelper
{
    public $multiAccountHelper;
    public $scopeConfig;
    public $debugMode;
    public $_objectManager;
    public $contentLanguage;
    public $targetCountry;
    public $includeDestination;
    public $_storeManager;
    public $logger;
    public $session;
    public $dataHelper;

    const GXPRESS_FETCH_TOKEN = 'gxpress_configuration/gxpresssetting/token';
    const GXPRESS_API_URL = 'https://www.googleapis.com/content/';
    const CONFIG_PATH_MERCHANT_ID = 'gxpress_configuration/gxpresssetting/gxpress_merchantId';
    const GOOGLE_REDIRECT_URI = 'gxpress_configuration/gxpresssetting/gxpress_admin_website';
    const GXPRESS_DEBUGMODE = 'gshop_config/product_upload/debugmode';

    public function __construct(
        \Ced\GShop\Helper\MultiAccount $multiAccountHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\Session $session,
        Logger $logger,
        Data $dataHelper,
        Context $context
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->multiAccountHelper = $multiAccountHelper;
        $this->scopeConfig = $scopeConfig;
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $this->session = $session;
        /** @var /Ced/GShop/Helper/Config $config */
        //$config = $this->_objectManager->create('Ced\GShop\Helper\Config');
        $this->debugMode = $this->scopeConfig->getValue(self::GXPRESS_DEBUGMODE, $storeScope);
        $this->logger = $logger;
        $this->dataHelper = $dataHelper;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function uploadProductOnGXpress($productToUpload)
    {
        $count = 1;
        $account = $this->multiAccountHelper->getAccountRegistry();
        $merchantId = ($account) ? $account->getMerchantId() : $this->getStoreConfig(self::CONFIG_PATH_MERCHANT_ID);
        $productChunk = [];
        $productError = [];
        $accountId = ($account) ? $account->getId() : '';
        try {
            $errors = [];
            $googleClient = $this->authClient();
            $service = '';
            $response = '';
            if (!is_bool($googleClient)) {
                $service = new \Google_Service_ShoppingContent($googleClient);
                foreach ($productToUpload as $keys => $values) {
                    $productChunk[$keys] = [
                        'method' => 'insert',
                        'merchantId' => $merchantId,
                        'batchId' => $count++,
                        'product' => $values
                    ];
                }
                $entries = new \Google_Service_ShoppingContent_ProductsCustomBatchRequest();
                $entries->setEntries($productChunk);
                $response = $service->products->custombatch($entries);
                foreach ($response->entries as $entry) {
                    if (isset($entry->errors) && isset($entry->errors->errors)) {
                        foreach ($entry->errors->errors as $error) {
                            if (isset($productChunk[$entry->batchId - 1]['product']['id'])) {
                                /** @var \Magento\Catalog\Model\Product $product */
                                $product = $this->_objectManager->create(\Magento\Catalog\Model\Product::class)
                                    ->loadByAttribute('sku', $productChunk[$entry->batchId - 1]['product']['id']);
                                $productError['sdk']['errors'] = [$error->message];
                                $productError['sdk']['id'] = $product->getId();
                                $productError['sdk']['sku'] = $product->getSku();
                                $this->dataHelper->updateAttribute($product, 'google_listing_error_' . $accountId, json_encode($productError));
                                $errors[$productChunk[$entry->batchId - 1]['product']['id']][] = $error->message;
                            }
                        }
                    }
                }
                $feedModel = $this->_objectManager->get('Ced\GShop\Model\Feeds');
                $feedModel->setData('account_id', $accountId);
                $feedModel->setData('feed_type', 'Product Upload');
                $feedModel->setData('feed_date', date('Y-m-d H:i:s'));
                $feedModel->setData('feed_source', json_encode($productToUpload));
                $feedModel->setData('feed_errors', is_array($errors) && count($errors) ? json_encode($errors) : json_encode(['response' =>'no error']));
                $feedModel->save();
                if ($this->debugMode) {
                    $this->logger(
                        'UPLOADED',
                        json_encode($productToUpload),
                        true
                    );
                }

                if (count($errors) > 0) {
                    throw new \Exception('Upload has errors', 300);
                }
            } else {
                return ['Please install Google SDK first'];
            }
            return $response;
        } catch (\Exception $e) {
            if ($e->getCode() != 300) {
                foreach ($productChunk as $value) {
                    $product = $this->_objectManager->create(\Magento\Catalog\Model\Product::class)
                        ->loadByAttribute('sku', $value['product']['id']);
                    $error = json_decode($e->getMessage(), true);
                    $err = '';
                    foreach ($error['error']['errors'] as $errorKey => $errorValue) {
                        $err .= $errorValue['location'] . ' => ' . $errorValue['message'];
                    }
                    $productError['sdk']['errors'] = [$err];
                    $productError['sdk']['id'] = $product->getId();
                    $productError['sdk']['sku'] = $product->getSku();
                    $this->dataHelper->updateAttribute($product, 'google_listing_error_' . $accountId, json_encode($productError));
                    $confProduct = $this->_objectManager->create(\Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable::class)->getParentIdsByChild($product->getId());
                    if (isset($confProduct[0])) {
                        $confProduct = $this->_objectManager->create(\Magento\Catalog\Model\Product::class)->load($confProduct[0]);
                        $this->dataHelper->updateAttribute($confProduct, 'google_listing_error_' . $accountId, json_encode($productError));
                    }
                }
            }
            if ($this->debugMode) {
                $this->logger(
                    'Upload Product-' . json_encode($productToUpload),
                    'Response (Post Request)',
                    $e->getMessage(),
                    true
                );
            }
            return false;
        }
    }

    public function authClient()
    {
        $client = $this->getGoogleClient();
        try {
            if ($client) {
                $account = $this->multiAccountHelper->getAccountRegistry();
                $token = ($account) ? $account->getAccountToken() : $this->scopeConfig(self::GXPRESS_FETCH_TOKEN);
                $client->refreshToken($token);
                return $client;
            }
            return false;
        } catch (\Exception $e) {
            if ($this->debugMode) {
                $this->logger(
                    'Client Error-' . $e->getMessage(),
                    'Response (Post Request)' . $e->getMessage()
                );
            }
            return false;
        }
    }

    public function getStatusOfProducts()
    {
        $response = '';
        try {
            $googleClient = $this->authClient();
            $service = '';
            if (!is_bool($googleClient)) {
                $service = new \Google_Service_ShoppingContent($googleClient);
                $account = $this->multiAccountHelper->getAccountRegistry();
                $merchantId = ($account) ? $account->getMerchantId() :
                    $this->scopeConfig(self::CONFIG_PATH_MERCHANT_ID);
                $response = $service->datafeeds->listDatafeeds($merchantId);
                return $response;
            }
            return false;
        } catch (\Exception $e) {
            if ($this->debugMode) {
                $this->logger(
                    'Client Error-' . json_encode($response),
                    'Response (Post Request)' . json_encode($e->getMessage())
                );
            }
            return false;
        }
    }

    public function getGoogleClient()
    {
        $secretFile = '';
        try {
            $account = $this->multiAccountHelper->getAccountRegistry();
            if ($account) {
                $secretFile = $account->getaccountFile();
            }
            $client = new \Google_Client();
            $client->setAuthConfigFile($secretFile);
            $redirectUri = $this->_urlBuilder->getBaseUrl() . 'gxpress/index';
            $client->setRedirectUri($redirectUri);
            $client->addScope(\Google_Service_ShoppingContent::CONTENT);
            $client->setAccessType("offline");
            if ($account->getaccount_token()) {
                $client->refreshToken($account->getaccount_token());
            }
            return $client;
        } catch (\Exception $e) {
            if ($this->debugMode) {
                $this->logger('Fetch Product error', 'error ' . json_encode($e->getMessage()));
            }
        } catch (\Error $e) {
            if ($this->debugMode) {
                $this->logger('Fetch Product error', 'error ' . json_encode($e->getMessage()));
            }
        }
        return false;
    }

    /**
     * @param string $type
     * @param string $subType
     * @param array $response
     * @param string $comment
     * @param bool $forcedLog
     * @return bool
     */
    public function logger($type = "Test", $subType = "Test", $response = [], $comment = "", $forcedLog = false, $mode = null)
    {
        if ($this->debugMode || $forcedLog) {
            if ($mode = 'info') {
                $this->_objectManager->get('Ced\GShop\Helper\Logger')
                    ->addInfo($type . ' ' . json_encode($response), ['path' => __METHOD__]);
            } else {
                $this->_objectManager->get('Ced\GShop\Helper\Logger')
                    ->addError($type . ' ' . json_encode($response), ['path' => __METHOD__]);
            }
            return true;
        }

        return false;
    }

    public function getProductFromGoogleExpress()
    {
        try {
            $googleClient = $this->authClient();
            $merchantId = $this->scopeConfig(self::CONFIG_PATH_MERCHANT_ID);
            $service = '';
            if (!is_bool($googleClient)) {
                $service = new \Google_Service_ShoppingContent($googleClient);
            }
            $products = $service->products->listProducts($merchantId);
            return $products;
        } catch (\Exception $e) {
            if ($this->debugMode) {
                $this->logger('Fetch Product error', 'error ' . json_encode($e->getMessage()));
            }
            return false;
        }
    }

    public function fetchOrderFromGXpress()
    {
        try {
            $googleClient = $this->authClient();
            $account = $this->multiAccountHelper->getAccountRegistry();
            $sandBox = $account->getAccountEnv();
            $merchantId = ($account) ? $account->getMerchantId() : $this->scopeConfig(self::CONFIG_PATH_MERCHANT_ID);
            $service = '';
            $order = 'please fetch the token';
            if (!is_bool($googleClient) && $sandBox == "production") {
                $service = new \Google_Service_ShoppingContent($googleClient);
                $listOrder['statuses'] = ["active", /*"completed",*/
                    "inProgress"];
                $order = $service->orders->listOrders($merchantId, $listOrder);
            } elseif (!is_bool($googleClient) && $sandBox == "sandbox") {
                $service = new \Google_Service_ShoppingContent($googleClient);
                $order = $service->orders->gettestordertemplate($merchantId, "template1");
            }
            return $order;
        } catch (\Exception $e) {
            if ($this->debugMode) {
                $this->logger->addError('Fetch Order error ' . $e->getMessage(), ['path' => __METHOD__]);
            }
            return false;
        }
    }

    public function orderAcknowledgement($purchaseOrderid)
    {
        try {
            $orderAck = new \Google_Service_ShoppingContent_OrdersAcknowledgeRequest();
            $orderAck->setOperationId($purchaseOrderid);
            return $orderAck;
        } catch (\Exception $e) {
            if ($this->debugMode) {
                $this->logger('Order Acknowledgement error', ['path' => __METHOD__]);
            }
            return false;
        }
    }

    public function getProductFromGoogleExpressById($sku)
    {
        try {
            $googleClient = $this->authClient();
            $service = '';
            if (!is_bool($googleClient)) {
                $service = new \Google_Service_ShoppingContent($googleClient);
            }
            $account = $this->multiAccountHelper->getAccountRegistry();
            $merchantId = ($account) ? $account->getMerchantId() : '';
            $this->targetCountry = ($account) ? $account->getTargetCountry() : '';
            $this->contentLanguage = ($account) ? $account->getContentLanguage() : '';
            $product = $this->_objectManager->create('Magento\Catalog\Model\Product')
                ->loadByAttribute('sku', $sku);
            $productId = 'online:' . $this->contentLanguage . ':' . $this->targetCountry . ':' . $product->getSku();
            $product = $service->products->get($merchantId, $productId);
            return $product;
        } catch (\Exception $e) {
            if ($this->debugMode) {
                $this->logger('Fetch Product By Id-' . $product, 'Sku ' . $sku);
            }
            return false;
        }
    }

    public function getFeeds()
    {
        try {
            $googleClient = $this->authClient();
            $service = '';
            if (!is_bool($googleClient)) {
                $service = new \Google_Service_ShoppingContent($googleClient);
            }
            $account = $this->multiAccountHelper->getAccountRegistry();
            $merchantId = ($account) ? $account->getMerchantId() : $this->scopeConfig(self::CONFIG_PATH_MERCHANT_ID);
            $response = $service->datafeeds->listDatafeeds($merchantId);
            return $response;
        } catch (\Exception $e) {
            if ($this->debugMode) {
                $this->logger('Fetch Feed Error ', json_encode($e->getMessage()));
            }
            return false;
        }
    }

    public function deleteRequest($id)
    {
        try {
            $googleClient = $this->authClient();
            $account = $this->multiAccountHelper->getAccountRegistry();
            $merchantId = ($account) ? $account->getMerchantId() : $this->scopeConfig(self::CONFIG_PATH_MERCHANT_ID);
            $this->targetCountry = ($account) ? $account->getTargetCountry() : '';
            $this->contentLanguage = ($account) ? $account->getContentLanguage() : '';
            $service = '';
            $sku = '';
            if (!is_bool($googleClient)) {
                $service = new \Google_Service_ShoppingContent($googleClient);
                $product = $this->_objectManager->create('Magento\Catalog\Model\Product')
                    ->load($id);
                $sku = $product->getSku();
                $googleid = 'online:' . $this->contentLanguage . ':' . $this->targetCountry . ':' . $sku;
                $res = $service->products->delete($merchantId, $googleid);
                $this->logger('Delete Request-' . $googleid, 'Sku ' . $sku);
                $response['type'] = 'success';
                $response['data'] = $sku;
                return $response;
            }
            $response['data'] = $sku;
            return $response;
        } catch (\Exception $e) {
            if ($this->debugMode) {
                $this->logger('Delete Request-' . $googleid, 'Sku ' . $sku);
            }
            $msg = json_decode($e->getMessage(), true);
            $code = $msg['error']['code'];
            $msgs = $msg['error']['message'];
            if ($code == 404) {
                $response['data'] = $sku . ' ' . $msgs . ' , ';
            } else {
                $response['data'] = $sku;
            }
            return $response;
        }
    }

    public function UpdateInventory($ids)
    {
        try {
            $googleClient = $this->authClient();
            $account = $this->multiAccountHelper->getAccountRegistry();
            $this->targetCountry = ($account) ? $account->getTargetCountry() : '';
            $this->contentLanguage = ($account) ? $account->getContentLanguage() : '';
            $merchantId = ($account) ? $account->getMerchantId() : '';
            $service = '';
            $response = [];
            if (!is_array($ids)) {
                $ids = [$ids];
            }
            if (!is_bool($googleClient)) {
                foreach ($ids as $id) {
                    try {
                        $service = new \Google_Service_ShoppingContent($googleClient);
                        //$inventory = new \Google_Service_ShoppingContent_PosInventoryRequest();//InventorySetRequest();
                        $inventory = new \Google_Service_ShoppingContent_InventorySetRequest();
                        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')
                            ->load($id);
                        /*$stock = $this->_objectManager->get('\Magento\CatalogInventory\Api\StockStateInterface')
                            ->getStockQty($product->getId(), $product->getStore()->getWebsiteId());*/
                        $stock = $product->getData('quantity_and_stock_status');
                        $stock = $stock['qty'];
                        $sku = $product->getData('sku');
                        $googleId = 'online:' . $this->contentLanguage . ':' . $this->targetCountry . ':' . $sku;
                        if ($stock) {
                            $inventory->setSellOnGoogleQuantity($stock);
                            $inventory->setAvailability('in stock');
                        } else {
                            $inventory->setSellOnGoogleQuantity(0);
                            $inventory->setAvailability('out of stock');
                        }
                        $res = $service->inventory->set($merchantId, 'online', $googleId, $inventory);
                        $response['type'] = 'success';
                        $response['data'] = $sku;
                    } catch (\Exception $e) {
                        $response['data'] = $sku;
                        continue;
                    }
                }
                return $response;
            }
            return false;
        } catch (\Exception $e) {
            if ($this->debugMode) {
                $this->logger('Inventory Update-' . json_encode($e->getMessage()), 'Id ' . json_encode($id));
            }
            return false;
        }
    }

    public function updatePriceInventory($ids)
    {
        try {
            $googleClient = $this->authClient();
            $account = $this->multiAccountHelper->getAccountRegistry();
            $merchantId = ($account) ? $account->getMerchantId() : $this->scopeConfig(self::CONFIG_PATH_MERCHANT_ID);
            $this->targetCountry = ($account) ? $account->getTargetCountry() : '';
            $this->contentLanguage = ($account) ? $account->getContentLanguage() : '';
            $service = '';
            $response = [];
            if (!is_array($ids)) {
                $ids = [$ids];
            }
            if (!is_bool($googleClient)) {
                foreach ($ids as $id) {
                    try {
                        $service = new \Google_Service_ShoppingContent($googleClient);
                        $inventory = new \Google_Service_ShoppingContent_InventorySetRequest();
                        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')
                            ->load($id);
                        /*$stock = $this->_objectManager->get('\Magento\CatalogInventory\Api\StockStateInterface')
                            ->getStockQty($product->getId(), $product->getStore()->getWebsiteId());*/
                        $stock = $product->getData('quantity_and_stock_status');
                        $stock = $stock['qty'];
                        $sku = $product->getSku();
                        $googleId = 'online:' . $this->contentLanguage . ':' . $this->targetCountry . ':' . $sku;
                        if ($stock) {
                            $inventory->setSellOnGoogleQuantity($stock);
                            $inventory->setAvailability('in stock');
                        } else {
                            $inventory->setSellOnGoogleQuantity(0);
                            $inventory->setAvailability('out of stock');
                        }
                        $price = new \Google_Service_ShoppingContent_Price();
                        $price->setValue($product->getPrice());
                        $currency = $this->_storeManager->getStore()->getCurrentCurrencyCode();
                        $price->setCurrency(isset($currency) ? $currency : 'USD');
                        $inventory->setPrice($price);
                        $res = $service->inventory->set($merchantId, 'online', $googleId, $inventory);
                        $response['type'] = 'success';
                        $response['data'] = $sku;
                    } catch (\Exception $e) {
                        $response['data'] = $sku;
                        continue;
                    }
                }
                return $response;
            }
            return false;
        } catch (\Exception $e) {
            if ($this->debugMode) {
                $this->logger('Inventory Update-' . json_encode($e->getMessage()), 'Id ' . json_encode($ids));
            }
            return false;
        }
    }

    public function updatePrice($skus)
    {
        try {
            $googleClient = $this->authClient();
            $account = $this->multiAccountHelper->getAccountRegistry();
            $merchantId = ($account) ? $account->getMerchantId() : $this->scopeConfig(self::CONFIG_PATH_MERCHANT_ID);
            $this->targetCountry = ($account) ? $account->getTargetCountry() : '';
            $this->contentLanguage = ($account) ? $account->getContentLanguage() : '';
            $service = '';
            $response = [];
            if (!is_bool($googleClient)) {
                foreach ($skus as $sku) {
                    try {
                        $service = new \Google_Service_ShoppingContent($googleClient);
                        $price = new \Google_Service_ShoppingContent_InventorySetRequest();
                        $priceObj = new \Google_Service_ShoppingContent_Price();
                        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')
                            ->loadByAttribute('sku', $sku);
                        $priceObj->setValue($product->getPrice());
                        $currency = $this->_storeManager->getStore()->getCurrentCurrencyCode();
                        $priceObj->setCurrency(isset($currency) ? $currency : 'USD');
                        $price->setPrice($priceObj);
                        $googleId = 'online:' . $this->contentLanguage . ':' . $this->targetCountry . ':' . $sku;
                        $res = $service->inventory->set($merchantId, 'online', $googleId, $price);
                        $response['type'] = 'success';
                        $response['data'] = $sku;
                    } catch (\Exception $e) {
                        $response['data'] = $sku;
                        continue;
                    }
                }
            }
        } catch (\Exception $e) {
            if ($this->debugMode) {
                $this->logger('Price Update-' . json_encode($service), 'Sku ' . $sku);
            }
            return false;
        }
        return $response;
    }

    public function updateOrderStatus($data_ship)
    {
        $response = [];
        try {
            if ($data_ship['noCallToGenerateShipment']) {

                //$shipData = array();
                foreach ($data_ship['shipments'] as $key => $value) {
                    $purchaseOrderId = $data_ship['shipments'][$key]['purchaseOrderId'];
                    $shipmentTrackingNumber = $data_ship['shipments'][$key]['shipment_tracking_number'];
                    $carrier = $data_ship['shipments'][$key]['carrier'];
                    //$shipData['operationId'] = $purchaseOrderId;
                    /*$shipData['carrier'] = $carrier;
                    $shipData['trackingId'] = $shipmentTrackingNumber;*/

                    foreach ($value['shipment_items'] as $shipmentKey => $shipmentValue) {
                        $shipmentId = $shipmentValue['shipment_item_id'];
                        break;
                    }
                }

                $orderData = $this->fetchOrderFromGoogleExpressByOrderId($purchaseOrderId);
                $lineItem = [];
                foreach ($orderData->getLineItems() as $key => $value) {
                    $lineItem[] = [
                        'lineItemId' => $value->getId(),
                        'quantity' => $value->getQuantityOrdered()
                    ];
                }

                //$shipData['lineItems'] = $lineItem;
                $googleClient = $this->authClient();
                $account = $this->multiAccountHelper->getAccountRegistry();
                $merchantId = ($account) ? $account->getMerchantId() : $this->scopeConfig(self::CONFIG_PATH_MERCHANT_ID);
                $orderObj = new \Google_Service_ShoppingContent_OrdersShipLineItemsRequest();
                $orderObj->setOperationId($purchaseOrderId);

                $shipmentInfos[] = [
                    "shipmentId" => $shipmentId,
                    "trackingId" => $shipmentTrackingNumber,
                    "carrier" => $carrier
                ];
                $this->logger('Shipment Logging -' . $purchaseOrderId . json_encode($shipmentInfos));
                $orderObj->setShipmentInfos($shipmentInfos);
                $orderObj->setLineItems($lineItem);

                $service = '';
                if (!is_bool($googleClient)) {
                    $this->logger('Shipment-' . $purchaseOrderId, 'Google Client ' . $googleClient->getClientId(), [], '', false, 'info');
                    $service = new \Google_Service_ShoppingContent($googleClient);
                }
                $response = $service->orders->shiplineitems($merchantId, $purchaseOrderId, $orderObj);
            }
        } catch (\Exception $e) {
            if ($this->debugMode) {
                $this->logger('Shipment-' . $purchaseOrderId, 'Purchase Order Id ' . $e->getMessage());
            }
            return false;
        }
        return $response;
    }

    public function fetchOrderFromGoogleExpressByOrderId($orderId)
    {
        try {
            $googleClient = $this->authClient();
            $account = $this->multiAccountHelper->getAccountRegistry();
            $merchantId = ($account) ? $account->getMerchantId() : $this->scopeConfig(self::CONFIG_PATH_MERCHANT_ID);
            $service = '';
            if (!is_bool($googleClient)) {
                $service = new \Google_Service_ShoppingContent($googleClient);
                $order = $service->orders->get($merchantId, $orderId);
                return $order;
            }
            return false;
        } catch (\Exception $e) {
            if ($this->debugMode) {
                $this->logger('Order id Fetch-' . $orderId, 'Purchase Order Id ' . $e->getMessage());
            }
            return false;
        }
    }

    public function cancelOrderOnGXpress($purchaseOrderId)
    {
        try {
            $googleClient = $this->authClient();
            $account = $this->multiAccountHelper->getAccountRegistry();
            $merchantId = ($account) ? $account->getMerchantId() : $this->scopeConfig(self::CONFIG_PATH_MERCHANT_ID);
            $service = '';
            if (!is_bool($googleClient)) {
                $service = new \Google_Service_ShoppingContent($googleClient);
                $cancelOrder = new \Google_Service_ShoppingContent_OrdersCancelRequest();
                $cancelOrder->setOperationId($purchaseOrderId . '0');
                $response = $service->orders->cancel($merchantId, $purchaseOrderId, $cancelOrder);
                return $response;
            }
            return false;
        } catch (\Exception $e) {
            if ($this->debugMode) {
                $this->logger('Shipment-' . $purchaseOrderId, 'Purchase Order Id ' . $e->getMessage());
            }
            return false;
        }
    }

    public function refundOrder($purchaseOrderId, $orderData)
    {
        try {
            $googleClient = $this->authClient();
            $account = $this->multiAccountHelper->getAccountRegistry();
            $merchantId = ($account) ? $account->getMerchantId() : $this->scopeConfig(self::CONFIG_PATH_MERCHANT_ID);
            $service = '';
            if (!is_bool($googleClient)) {
                $service = new \Google_Service_ShoppingContent($googleClient);
                $refund = $service->orders->refund($merchantId);
                return $refund;
            }
            return false;
        } catch (\Exception $e) {
            if ($this->debugMode) {
                $this->logger('refund error', $e->getMessage());
            }
            return false;
        }
    }
}
