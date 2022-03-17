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
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class Massupload
 * @package Ced\GShop\Controller\Adminhtml\Product
 */
class Massupload extends Action
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;
    /**
     * @var CollectionFactory
     */
    public $catalogCollection;
    /**
     * @var Filter
     */
    public $filter;
    /** @var \Ced\GShop\Helper\Data $dataHelper */
    public $dataHelper;

    /**
     * @var \Ced\GShop\Helper\MultiAccount
     */
    protected $multiAccountHelper;

    const ADMIN_RESOURCE = 'Ced_GShop::GXpress';

    public $nodes;

    public $scopeConfig;

    /**
     * Massupload constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CollectionFactory $collectionFactory
     * @param \Ced\GShop\Helper\MultiAccount $multiAccountHelper
     * @param \Ced\GShop\Helper\Data $dataHelper
     * @param Filter $filter
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CollectionFactory $collectionFactory,
        \Ced\GShop\Helper\MultiAccount $multiAccountHelper,
        \Ced\GShop\Helper\Data $dataHelper,
        Filter $filter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->catalogCollection = $collectionFactory;
        $this->multiAccountHelper = $multiAccountHelper;
        $this->dataHelper = $dataHelper;
        $this->filter = $filter;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        try {
            $productids = $cids = $sids = [];
            $accountId = $this->_session->getAccountId();
            if ($this->dataHelper->getProcessMode() == 'csv') {
                $nodeChange = [
                    'productId' =>'gtin',
                    'ageGroup' =>'age group',
                    'customLabel0' =>'custom label 0',
                    'customLabel1' =>'custom label 1',
                    'customLabel2' =>'custom label 2',
                    'customLabel3' =>'custom label 3',
                    'customLabel4' =>'custom label 4',
                    'energyEfficiencyClass' =>'energy efficiency class',
                    'maxEnergyEfficiencyClass' =>'max energy efficiency class',
                    'minEnergyEfficiencyClass' =>'min energy efficiency class',
                    'maxHandlingTime' =>'max handling time',
                    'minHandlingTime' =>'min handling time',
                    'promotionIds' =>'promotion ids',
                    'salePrice' =>'sale price',
                    'shippingHeight' =>'shipping height',
                    'shippingLabel' =>'shipping label',
                    'shippingLength' =>'shipping length',
                    'shippingWeight' =>'shipping weight',
                    'shippingWidth' =>'shipping width',
                    'sizeSystem' =>'size system',
                    'sizeType' =>'size type',
                    'google_product_category' => 'google_product_category',
                    'isBundle' => 'isBundle'
                ];
            }
            $chunkSize = $this->dataHelper->getchunkSize();
            $ids = $this->getRequest()->getParam('selected');
            if (!$ids) {
                $collection = $this->filter->getCollection(
                    $this->_objectManager->create('Magento\Catalog\Model\Product')
                    ->getCollection()
                );
            }
            $ids = $collection->addAttributeToFilter('adwords_tp_status', 1)->getAllIds();
            $ids = array_chunk($ids, $chunkSize);
            foreach ($ids as $prodChunkKey => $prodids) {
                $productids[$prodChunkKey] = [$accountId => $prodids];
            }
            if (!empty($productids)) {
                $this->_session->setUploadChunks($productids);
                if ($this->dataHelper->getProcessMode() == 'csv') {
                    $profile = $this->multiAccountHelper->getProfileAttrForAcc($accountId);
                    $accountProduct = $this->filter->getCollection(
                        $this->_objectManager->create('Magento\Catalog\Model\Product')
                            ->getCollection()->addAttributeToFilter($profile, ['notnull'=>true])
                    );
                    $product = $this->_objectManager->create('Magento\Catalog\Model\Product')
                        ->load($accountProduct->getFirstItem()->getId());
                    $this->nodes = array_keys($this->dataHelper->getMappedNodes($product, $accountId));
                    foreach ($this->nodes as $nodeKey => $node) {
                        if (isset($nodeChange[$node])) {
                            $this->nodes[$nodeKey] = $nodeChange[$node];
                        }
                    }
                    $additional =  [
                        'additional image link', 'image link',
                        'item group id',
                        'mpn','availability','google product category','sale price effective date','isBundle'
                    ];
                    if ($this->scopeConfig->getValue(Data::GXPRESS_PRODUCT_TAXPRICE)) {
                        array_push($additional, 'taxes');
                    }
                    if ($this->scopeConfig->getValue(Data::GXPRESS_PRODUCT_SALEPRICE_EXPIREON)) {
                        array_push($additional, 'sale price effective date');
                    }
                    $this->nodes = array_merge($this->nodes, $additional);
                    sort($this->nodes);
                    $account = $this->_objectManager->get(\Ced\GShop\Model\Accounts::class)->load($accountId);
                    $fileName = 'googleshopping_' . $account->getAccountCode() . '_' .
                        $account->getContentLanguage() . '_' . $account->getTargetCountry() . '.txt';
                    $dirPath = BP . '/pub/media/ced_google';
                    $filePath = $dirPath . '/' . $fileName;
                    if (!file_exists($dirPath)) {
                        mkdir($dirPath, 0777, true);
                    } else {
                        chmod($dirPath, 0777);
                    }
                    $fp = fopen($filePath, "w");
                    fputcsv($fp, $this->nodes, chr(9));
                }
                $resultPage = $this->resultPageFactory->create();
                $resultPage->setActiveMenu('Ced_GShop::product');
                $resultPage->getConfig()->getTitle()->prepend(__('Add Product(s) On Google Shopping'));
                return $resultPage;
            } else {
                $this->messageManager->addErrorMessage(__('No product available for upload.'));
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('No product Assign on Profile'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }
}
