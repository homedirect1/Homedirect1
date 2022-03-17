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

namespace Ced\GShop\Cron;

use Ced\GShop\Helper\Data;

class AutoUpload
{
    /** @var \Ced\GShop\Helper\Logger $logger */
    public $logger;

    /** @var \Ced\GShop\Helper\MultiAccount $multiAccountHelper */
    public $multiAccountHelper;

    /** @var \Magento\Catalog\Model\Product */
    public $productModel;

    /** @var \Ced\GShop\Helper\Data $dataHelper */
    public $dataHelper;

    public function __construct(
        \Ced\GShop\Helper\Logger $logger,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\GShop\Helper\MultiAccount $multiAccountHelper,
        \Ced\GShop\Helper\Data $dataHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->logger = $logger;
        $this->objectManager = $objectManager;
        $this->productModel = $productModel;
        $this->multiAccountHelper = $multiAccountHelper;
        $this->dataHelper = $dataHelper;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute()
    {
        $productIdsToUpload = [];
        $response = [];
        $nextUploadOn = date('Y-m-d', strtotime(date('Y-m-d')));
        $scopeConfigManager = $this->objectManager
            ->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $autoUpload = $scopeConfigManager->getValue('gshop_config/gshop_cron/autoupload_cron');
        if ($autoUpload) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
            $productCollection = $this->productModel->getCollection()
                ->addAttributeToFilter('visibility', ['eq' => 4])
//                ->addAttributeToFilter('google_product_expires', ['notnull' => true])
                ->addAttributeToFilter('adwords_tp_status', 1);
//                ->getAllIds();
            $profileAttrs = $this->multiAccountHelper->getAllEnabledProfileAttr();
            try {
                foreach ($profileAttrs as $profileAttr) {
                    $collection = $productCollection;
                    $profileAccAttr = explode('_', $profileAttr)[2];
                    $productIdsToUpload[$profileAccAttr] = $collection->addAttributeToFilter($profileAttr, $profileAccAttr)->getAllIds();
                }
                /*foreach ($productCollection as $index => $productId) {
                    foreach ($profileAttrs as $profileAttr) {
                        $productProfile = $this->productModel->load($productId)->getData($profileAttr);
                        if ($productProfile) {
                            $profileAttr = explode('_', $profileAttr);
                            $productIdsToUpload[$profileAttr[2]][] = $productId;
                        }
                    }
                }*/
                if ($this->dataHelper->getProcessMode() == 'csv') {
                    foreach ($productIdsToUpload as $accId => $products) {
                        $this->writeHeaderOnCSV($accId, $products);
                    }
                }
                foreach ($productIdsToUpload as $accId => $products) {
                    $this->multiAccountHelper->getAccountRegistry($accId);
                    $response = $this->dataHelper->createProductOnGXpress([$accId => $products]);
                }
            } catch (\Exception $e) {
                $this->dataHelper->logger(
                    "Google Express",
                    "upload Product Exception",
                    $e->getMessage(),
                    1
                );
            } catch (\Error $e) {
                $this->dataHelper->logger(
                    "Google Express",
                    "upload Product Error",
                    $e->getMessage(),
                    1
                );
            }
            return $response;
        }
        $this->logger->addError('In AutoUpload Cron: Disable', ['path' => __METHOD__]);
        return $response;
    }

    public function writeHeaderOnCSV($accountId, $products)
    {
        $nodeChange = [
            'productId' => 'gtin',
            'ageGroup' => 'age group',
            'customLabel0' => 'custom label 0',
            'customLabel1' => 'custom label 1',
            'customLabel2' => 'custom label 2',
            'customLabel3' => 'custom label 3',
            'customLabel4' => 'custom label 4',
            'energyEfficiencyClass' => 'energy efficiency class',
            'maxEnergyEfficiencyClass' => 'max energy efficiency class',
            'minEnergyEfficiencyClass' => 'min energy efficiency class',
            'maxHandlingTime' => 'max handling time',
            'minHandlingTime' => 'min handling time',
            'promotionIds' => 'promotion ids',
            'salePrice' => 'sale price',
            'shippingHeight' => 'shipping height',
            'shippingLabel' => 'shipping label',
            'shippingLength' => 'shipping length',
            'shippingWeight' => 'shipping weight',
            'shippingWidth' => 'shipping width',
            'sizeSystem' => 'size system',
            'sizeType' => 'size type',
            'isBundle' => 'isBundle'
        ];

        $product = $this->objectManager->create('Magento\Catalog\Model\Product')
            ->load($products[0]);
        $this->nodes = array_keys($this->dataHelper->getMappedNodes($product, $accountId));
        foreach ($this->nodes as $nodeKey => $node) {
            if (isset($nodeChange[$node])) {
                $this->nodes[$nodeKey] = $nodeChange[$node];
            }
        }
        $additional = [
            'additional image link', 'image link',
            'item group id',
            'mpn','availability','google_product_category','sale price effective date','isBundle'
        ];
        if ($this->scopeConfig->getValue(Data::GXPRESS_PRODUCT_TAXPRICE)) {
            array_push($additional, 'taxes');
        }
        if ($this->scopeConfig->getValue(Data::GXPRESS_PRODUCT_SALEPRICE_EXPIREON)) {
            array_push($additional, 'sale price effective date');
        }
        $this->nodes = array_merge($this->nodes, $additional);
        sort($this->nodes);
        $account = $this->objectManager->get(\Ced\GShop\Model\Accounts::class)->load($accountId);
        $fileName = 'googleshopping_' . $account->getAccountCode() . '_' .
            $account->getContentLanguage() . '_' . $account->getTargetCountry() . '.txt';
        $dirPath = BP . '/pub/media/ced_google';
        $filePath = $dirPath . '/' . $fileName;
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
        $fp = fopen($filePath, "w");
        fputcsv($fp, $this->nodes, chr(9));
    }
}
