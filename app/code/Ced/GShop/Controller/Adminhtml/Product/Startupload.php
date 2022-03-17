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

namespace Ced\GShop\Controller\Adminhtml\Product;

use Ced\GShop\Helper\Data;
use Ced\GShop\Helper\GXpress;
use Ced\GShop\Helper\Logger;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Startupload
 * @package Ced\GShop\Controller\Adminhtml\Product
 */
class Startupload extends Action
{
    /** @var Logger $logger */
    public $logger;

    /** @var GXpress $gxpressHelper */
    public $gxpressHelper;

    /** @var Data $dataHelper */
    public $dataHelper;

    /** @var \Ced\GShop\Helper\MultiAccount $multiAccountHelper */
    protected $multiAccountHelper;

    /** @var PageFactory $resultPageFactory */
    public $resultPageFactory;

    /** @var \Magento\Framework\Registry $_coreRegistry */
    protected $_coreRegistry;

    /** @var JsonFactory $resultJsonFactory */
    public $resultJsonFactory;

    /**
     * Startupload constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param GXpress $gxpressHelper
     * @param Data $dataHelper
     * @param Logger $logger
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Ced\GShop\Helper\MultiAccount $multiAccountHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        GXpress $gxpressHelper,
        Data $dataHelper,
        Logger $logger,
        \Magento\Framework\Registry $coreRegistry,
        \Ced\GShop\Helper\MultiAccount $multiAccountHelper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->gxpressHelper = $gxpressHelper;
        $this->dataHelper = $dataHelper;
        $this->logger = $logger;
        $this->_coreRegistry = $coreRegistry;
        $this->multiAccountHelper = $multiAccountHelper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $message = [];
        $message['error'] = [];
        $message['success'] = "";
        $productError = [];
        $successids = [];
        $key = $this->getRequest()->getParam('index');
        $totalChunk = $this->_session->getUploadChunks();

        $index = $key + 1;
        if (count($totalChunk) <= $index) {
            $this->_session->unsUploadChunks();
        }
        try {
            if (isset($totalChunk[$key])) {
                $ids = $totalChunk[$key];
                foreach ($ids as $accountId => $prodIds) {
                    $account = $this->_objectManager->get('\Ced\GShop\Model\Accounts')->load($accountId);
                    $status = $account->getData('account_status');

                    if ($status) {
                        if (!is_array($prodIds)) {
                            $prodIds[] = $prodIds;
                        }
                        if ($this->_coreRegistry->registry('google_account')) {
                            $this->_coreRegistry->unregister('google_account');
                        }
                        $this->multiAccountHelper->getAccountRegistry($accountId);
                        $itemIdAccAttr = $this->multiAccountHelper->getItemIdAttrForAcc($accountId);
                        $listingErrorAccAttr = $this->multiAccountHelper->getProdListingErrorAttrForAcc($accountId);
                        $prodStatusAccAttr = $this->multiAccountHelper->getProdStatusAttrForAcc($accountId);
                        $this->dataHelper->updateAccountVariable();
                        $this->gxpressHelper->updateAccountVariable();
                        $successids[$accountId] = [];
                        foreach ($prodIds as $id) {
                            $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($id);

                            if ($product->getTypeId() == 'configurable') {
                                $productType = $product->getTypeInstance();
                                $configProd = $productType->getUsedProducts($product);

                                foreach ($configProd as $childprod) {
                                    $finaldata = $this->gxpressHelper->prepareData($childprod->getId());
                                    $listingError = $this->prepareResponse($childprod->getEntityId(), $childprod->getSku(), $finaldata['error']);
                                    if ($listingError) {
                                        $productError['error'][$product->getSku()][$childprod->getSku()] = $listingError;
                                    }
                                    $childprod->setData($prodStatusAccAttr, 2);
                                    $childprod->setData($listingErrorAccAttr, json_encode($listingError));
                                    $childprod->getResource()->saveAttribute($childprod, $prodStatusAccAttr)->saveAttribute($childprod, $listingErrorAccAttr);
                                }
//                                foreach ($productError['error'] as $productErrorJson) {
                                if (array_key_exists('error', $productError) && isset($productError['error']) & sizeof($productError['error']) > 0) {
                                    $message['error'] = $productError['error'];
                                }
//                                }

                                if (isset($productError['error']) && sizeof($productError['error']) > 0) {
                                    $listingError = $this->prepareResponse($product->getEntityId(), $product->getSku(), $productError['error']);
                                    $product->setData($prodStatusAccAttr, 2);
                                    $product->setData($listingErrorAccAttr, json_encode($listingError));
                                    $product->getResource()->saveAttribute($product, $prodStatusAccAttr)->saveAttribute($product, $listingErrorAccAttr);
                                } else {
                                    $listingError = $this->prepareResponse($product->getEntityId(), $product->getSku(), 'valid');
                                    array_push($successids[$accountId], $id);
                                    $product->setData($prodStatusAccAttr, 2);
                                    $product->setData($listingErrorAccAttr, json_encode($listingError));
                                    $product->getResource()->saveAttribute($product, $prodStatusAccAttr)->saveAttribute($product, $listingErrorAccAttr);
                                }
                            } else {
                                $finaldata = $this->gxpressHelper->prepareData($id, 1);
                                if (isset($finaldata['error']) && sizeof($finaldata['error']) > 0) {
                                    $product->setData($prodStatusAccAttr, 2);
                                    $listingError = $this->prepareResponse($product->getEntityId(), $product->getSku(), $finaldata['error']);
                                    $product->setData($listingErrorAccAttr, json_encode($listingError));
                                    $product->getResource()->saveAttribute($product, $prodStatusAccAttr)->saveAttribute($product, $listingErrorAccAttr);
                                    $message['error'][$product->getSku()] = $finaldata['error'];
                                } else {
                                    $listingError = $this->prepareResponse($product->getEntityId(), $product->getSku(), 'valid');
                                    array_push($successids[$accountId], $id);
                                    $product->setData($prodStatusAccAttr, 2);
                                    $product->setData($listingErrorAccAttr, json_encode($listingError));
                                    $product->getResource()->saveAttribute($product, $prodStatusAccAttr)->saveAttribute($product, $listingErrorAccAttr);
                                }
                            }
                        }
                    } else {
                        $message['error'] = $account->getData('account_code') . " is disabled";
                    }
                }

                if (!empty($successids) &&
                    isset($successids[$accountId]) &&
                    sizeof($successids[$accountId]) > 0) {
                    $responseData = $this->dataHelper->createProductOnGXpress($successids);
                    if ($this->dataHelper->getProcessMode() == 'api') {
                        $message['success'] = "SKU: " .
                            implode(', ', $responseData['success']['sku']) . " successfully uploaded";
                    } else {
                        if (isset($responseData['success']) && sizeof($responseData['success']) > 0) {
                            $message['success'] = "SKU: " .
                                implode(', ', $responseData['success']['sku']) . " successfully uploaded";
                        }
                        if (isset($responseData['error']) && sizeof($responseData['error']) > 0) {
                            foreach ($responseData['error']['sku'] as $sku) {
                                $message['error'][$sku] = "SKU: " . $sku . " not uploaded";
                            }
                        }
                    }
                }
                $errMsg = '';
                if (!empty($message['error']) && is_array($message['error'])) {
                    foreach ($message['error'] as $key => $value) {
                        if (is_array($value) && sizeof($value) > 0) {
                            foreach ($value as $errkey => $errValue) {
                                if (is_array($errValue)) {
                                    foreach ($errValue as $k => $v) {
                                        foreach ($v['errors'] as $errsKey => $errs) {
                                            $errMsg .= "SKU " . "<b>" . $v['sku'] . "</b>: " . $errs . "<br />";
                                        }
                                    }
                                } else {
                                    $errMsg .= "SKU " . "<b>" . $key . "</b>: " . $errValue . "<br />";
                                }
                            }
                        } else {
                            $errMsg .= $value . " <br />";
                        }
                    }
                    $message['error'] = $errMsg;
                }
            } else {
                $message['error'] = "Batch " . $index . ": " . json_encode($message['error']) . " included Product(s) data not found.";
            }
        } catch (\Exception $e) {
            $message['error'] = $e->getMessage();
            $this->logger->addError($message['error'], ['path' => __METHOD__]);
        }
        if (is_array($message['error']) && !sizeof($message['error'])) {
            unset($message['error']);
        }
        return $resultJson->setData($message);
    }

    public function getErrors($invPriceSyncOnGXpress)
    {
        $message = [];
        if (!isset($invPriceSyncOnGXpress->LongMessage)) {
            foreach ($invPriceSyncOnGXpress as $errorMessage) {
                $message[] = $errorMessage->LongMessage;
            }
        } else {
            $message[] = $invPriceSyncOnGXpress->LongMessage;
        }
        return implode(', ', $message);
    }

    public function prepareResponse($id = null, $sku, $errors)
    {
        $response = [];
        if (is_array($errors)) {
            foreach ($errors as $key => $error) {
                $response[$key] =
                    [
                        "id" => $id,
                        "sku" => $sku,
                        /* "url" => "#",*/
                        'errors' => [$error]
                    ];
            }
        }
        return $response;
    }
}
