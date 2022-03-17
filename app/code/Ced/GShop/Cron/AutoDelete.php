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
 * @package   Ced_GShop
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GShop\Cron;

class AutoDelete
{
    /** @var \Ced\GShop\Helper\Logger $logger */
    public $logger;

    /** @var \Ced\GShop\Helper\MultiAccount $multiAccountHelper */
    public $multiAccountHelper;

    /** @var \Magento\Catalog\Model\Product */
    public $productModel;

    /** @var \Ced\GShop\Helper\Data $dataHelper */
    public $dataHelper;

    /** @var \Ced\GShop\Helper\GXpresslib $gxpressLib */
    public $gxpressLib;

    /**
     * AutoDelete constructor.
     * @param \Ced\GShop\Helper\Logger $logger
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Ced\GShop\Helper\MultiAccount $multiAccountHelper
     * @param \Ced\GShop\Helper\Data $dataHelper
     * @param \Ced\GShop\Helper\GXpresslib $gxpressLib
     */
    public function __construct(
        \Ced\GShop\Helper\Logger $logger,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\GShop\Helper\MultiAccount $multiAccountHelper,
        \Ced\GShop\Helper\Data $dataHelper,
        \Ced\GShop\Helper\GXpresslib $gxpressLib
    )
    {
        $this->logger = $logger;
        $this->objectManager = $objectManager;
        $this->productModel = $productModel;
        $this->multiAccountHelper = $multiAccountHelper;
        $this->dataHelper = $dataHelper;
        $this->gxpressLib = $gxpressLib;
    }

    public function execute()
    {
        $scopeConfigManager = $this->objectManager
            ->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $autoDelete = $scopeConfigManager->getValue('gshop_config/gshop_cron/autoupload_cron');
        $productIdsToUpload = [];
        $response = [];
        if ($autoDelete && $this->dataHelper->getProcessMode() == "api") {
            $accounts = $this->multiAccountHelper->getAllAccounts(true)
                ->addFieldToFilter('account_type', 'ads')
                ->getAllIds();
            $productCollection = $this->productModel->getCollection()
//                ->addAttributeToFilter('google_product_expires', ['notnull' => true])
                ->addAttributeToFilter('adwords_tp_status', 0)->getAllIds();
//            $profileAttrs = $this->multiAccountHelper->getAllGAdsProfileAttr();
            $profileAttrs = $this->multiAccountHelper->getAllEnabledProfileAttr();
            try {
                /*foreach ($productCollection as $index => $productId) {
                    foreach ($profileAttrs as $profileAttr) {
                        $productProfile = $this->productModel->load($productId)->getData($profileAttr);
                        if ($productProfile) {
                            $profileAttr = explode('_', $profileAttr);
                            if (in_array($profileAttr[2], $accounts)) {
                                $productIdsToUpload[$profileAttr[2]][] = $productId;
                            }
                        }
                    }
                }*/
                foreach ($profileAttrs as $profileAttr) {
                    $collection = $productCollection;
                    $profileAccAttr = explode('_', $profileAttr)[2];
                    $productIdsToUpload[$profileAccAttr] = $collection->addAttributeToFilter($profileAttr, $profileAccAttr)->getAllIds();
                }
                foreach ($productIdsToUpload as $key => $ids) {
                    $this->multiAccountHelper->getAccountRegistry($key);
                    $response = $this->gxpressLib->deleteRequest($ids);
                }

            } catch (\Exception $e) {
                $this->dataHelper->logger("Adwords", "delete Product Exception",
                    $e->getMessage(), 1);
            } catch (\Error $e) {
                $this->dataHelper->logger("Adwords", "delete Product Error",
                    $e->getMessage(), 1);
            }
            return $response;
        }
        $this->logger->addError('In AutoDelete Cron: Disable', ['path' => __METHOD__]);
        return $response;
    }
}
