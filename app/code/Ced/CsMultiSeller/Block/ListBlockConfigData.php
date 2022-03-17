<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsMultiSeller
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMultiSeller\Block;

use Magento\Catalog\Model\ProductFactory;

/**
 * Class ListBlock
 * @package Ced\CsMultiSeller\Block
 */
class ListBlockConfigData extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\Module\Manager
     */
    public $_moduleManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @var \Ced\CsMultiSeller\Helper\Data
     */
    protected $csmultisellerHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory
     */
    protected $vproductsCollectionFactory;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\VproductsFactory
     */
    protected $vproductsFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $marketplaceHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;

    /**
     * @var \Ced\CsMarketplace\Model\VendorFactory
     */
    public $vendorFactory;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    public $priceCurrency;

    /**
     * @var ProductFactory
     */
    public $productFactory;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    public $formKey;

    /**
     * ListBlock constructor.
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param ProductFactory $productFactory
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Ced\CsMultiSeller\Helper\Data $csmultisellerHelper
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory $vproductsCollectionFactory
     * @param \Ced\CsMarketplace\Model\ResourceModel\VproductsFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Ced\CsMarketplace\Helper\Data $marketplaceHelper
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param array $data
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ced\CsMultiSeller\Helper\Data $csmultisellerHelper,
        \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory $vproductsCollectionFactory,
        \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Ced\CsMarketplace\Helper\Data $marketplaceHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [],
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_moduleManager = $moduleManager;
        $this->csmultisellerHelper = $csmultisellerHelper;
        $this->vproductsCollectionFactory = $vproductsCollectionFactory;
        $this->vproductsFactory = $vproductsFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->priceCurrency = $priceCurrency;
        $this->vendorFactory = $vendorFactory;
        $this->productFactory = $productFactory;
        $this->formKey = $formKey;
        parent::__construct($context, $data);
        $this->setTabTitle();
    }

    /**
     * Set Tab Title
     */
    public function setTabTitle()
    {
        $title = __('More Seller');
        $this->setTitle($title);
    }

    /**
     * prepare list layout
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    public function ConfigurableChild($product_id, $parentId){

        $product_collection = [];
        $products = [];
        if ($parentId > 0) {
            $products = $this->vproductsCollectionFactory->create()
                ->addFieldToFilter(
                    ['configurable_product_id'],
                    [
                        ['eq' => $parentId]
                    ]
                )->addFieldToFilter('id', ['neq' => $parentId])
                ->addFieldToFilter('parent_id',['eq' => $product_id])
                ->addFieldToFilter('is_multiseller', ['eq' => 1])
                ->getColumnValues('product_id');

        } else {
            $products = $this->vproductsCollectionFactory->create()->addFieldToFilter('parent_id', ['eq' => $parentId])
                ->addFieldToFilter('is_multiseller', ['eq' => 1])
                ->getColumnValues('product_id');
        }
        if (count($products) > 0) {
            $product_collection = $this->productCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', ['in' => $products])
                ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

            if ($this->_moduleManager->isEnabled('Magento_CatalogInventory')) {
                $product_collection->joinField(
                    'qty',
                    'cataloginventory_stock_item',
                    'qty',
                    'product_id=entity_id',
                    '{{table}}.stock_id=1',
                    'left'
                );
                $product_collection->joinField(
                    'is_in_stock',
                    'cataloginventory_stock_item',
                    'is_in_stock',
                    'product_id=entity_id',
                    '{{table}}.stock_id=1',
                    'left'
                );
            }
            $product_collection->joinField('check_status', 'ced_csmarketplace_vendor_products', 'check_status', 'product_id=entity_id', null, 'left');
            $product_collection->joinField('vendor_id', 'ced_csmarketplace_vendor_products', 'vendor_id', 'product_id=entity_id', null, 'left');
            $showInStock = $this->scopeConfig->getValue('cataloginventory/options/show_out_of_stock', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($showInStock == '1') {
                $product_collection->addAttributeToFilter('is_in_stock', ['in' => [0,1]]);
            }else{
                $product_collection->addAttributeToFilter('qty', ['gt' => 0])->addAttributeToFilter('is_in_stock', ['eq' => 1]);
            }
            $showMinPrice = $this->scopeConfig->getValue('ced_csmarketplace/ced_csmultiseller/minprice', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($showMinPrice) {
                $product_collection->addAttributeToSort('price', 'ASC');
            } else {
                $product_collection->addAttributeToSort('price', 'DESC');
            }
        }
        return $product_collection;
    }


}
