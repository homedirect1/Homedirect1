<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_GroupBuying
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GroupBuying\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Registry;
use Magento\Framework\Url\Helper\Data;
use Magento\Setup\Exception;
use Magento\Customer\Model\Session;

/**
 * Product list
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ListProduct extends \Magento\Catalog\Block\Product\AbstractProduct implements IdentityInterface
{

    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = Toolbar::class;

    /**
     * Product Collection
     *
     * @var AbstractCollection
     */
    protected $_productCollection;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;

    /**
     * @var Data
     */
    protected $urlHelper;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;


    /**
     * @param Context                                   $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param Resolver                                  $layerResolver
     * @param CategoryRepositoryInterface               $categoryRepository
     * @param Data                                      $urlHelper
     * @param Registry                                  $registry
     * @param ProductFactory                            $_productloader
     * @param ObjectManagerInterface                    $objectManager
     * @param array                                     $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        Registry $registry,
        ProductFactory $_productloader,
        ObjectManagerInterface $objectManager,
        array $data=[]
    ) {
        $this->_catalogLayer      = $layerResolver->get();
        $this->_postDataHelper    = $postDataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->urlHelper          = $urlHelper;
        $this->_coreRegistry      = $registry;
        $this->_productloader     = $_productloader;
        $this->objectManager      = $objectManager;
        parent::__construct(
            $context,
            $data
        );

    }//end __construct()


    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $layer = $this->getLayer();
            // @var $layer \Magento\Catalog\Model\Layer
            if ($this->getShowRootCategory()) {
                $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if ($this->_coreRegistry->registry('product')) {
                // get collection of categories this product is associated with
                $categories = $this->_coreRegistry->registry('product')->getCategoryCollection()->setPage(1, 1)->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                try {
                    $category = $this->categoryRepository->get($this->getCategoryId());
                } catch (NoSuchEntityException $e) {
                    $category = null;
                }

                if ($category) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                }
            }

            $this->_productCollection = $layer->getProductCollection();

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }//end if

        return $this->_productCollection;

    }//end _getProductCollection()


    /**
     * Get catalog layer model
     *
     * @return \Magento\Catalog\Model\Layer
     */
    public function getLayer()
    {
        return $this->_catalogLayer;

    }//end getLayer()


    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();

    }//end getLoadedProductCollection()


    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChildBlock('toolbar')->getCurrentMode();

    }//end getMode()


    /**
     * Returns if product has group buy enabled or not
     *
     * @param integer|string $id Catalog Product ID.
     *
     * @return mixed|null
     */
    public function getnew($id)
    {
        $productCollection = $this->objectManager->get(Product::class)->load($id);

        return $productCollection->getData('group_buy');

    }//end getnew()


    /**
     * Checks if customer is logged in or not
     *
     * @return boolean Returns if customer logged in or not.
     */
    public function getcustomerinfo()
    {
        $custom = $this->objectManager->get(Session::class)->isLoggedIn();

        return $custom;

    }//end getcustomerinfo()


    /**
     * Get last date for special price
     *
     * @param integer|string $id Product ID.
     *
     * @return mixed
     */
    public function getproductdate($id)
    {
        $productCollection = $this->objectManager->get(Product::class)->load($id);
        return $productCollection->getSpecialToDate();

    }//end getproductdate()


    /**
     * Need use as _prepareLayout - but problem in declaring collection from another block (was problem with search result)
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getProductCollection();

        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }

        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }

        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }

        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $this->_getProductCollection()]
        );

        $this->_getProductCollection()->load();

        return parent::_beforeToHtml();

    }//end _beforeToHtml()


    /**
     * Retrieve Toolbar block
     *
     * @return Toolbar
     * @throws LocalizedException Exception.
     */
    public function getToolbarBlock()
    {
        $blockName = $this->getToolbarBlockName();
        if ($blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block) {
                return $block;
            }
        } else {
            $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, uniqid(microtime(), true));
        }

        return $block;

    }//end getToolbarBlock()


    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');

    }//end getAdditionalHtml()


    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');

    }//end getToolbarHtml()


    /**
     * Sets collection to product collection
     *
     * @param AbstractCollection $collection
     *
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;

    }//end setCollection()


    /**
     * Returns attributes from product collection
     *
     * @param array|string|integer|\Magento\Framework\App\Config\Element $code
     *
     * @return $this
     * @throws LocalizedException
     */
    public function addAttribute($code)
    {
        $this->_getProductCollection()->addAttributeToSelect($code);
        return $this;

    }//end addAttribute()


    /**
     * Returns Price block template
     *
     * @return mixed
     */
    public function getPriceBlockTemplate()
    {
        return $this->_getData('price_block_template');

    }//end getPriceBlockTemplate()


    /**
     * Retrieve Catalog Config object
     *
     * @return \Magento\Catalog\Model\Config
     */
    protected function _getConfig()
    {
        return $this->_catalogConfig;

    }//end _getConfig()


    /**
     * Prepare Sort By fields from Category Data
     *
     * @param  \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Block\Product\ListProduct
     */
    public function prepareSortableFieldsByCategory($category)
    {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($category->getAvailableSortByOptions());
        }

        $availableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            $categorySortBy = $this->getDefaultSortBy() ?: $category->getDefaultSortBy();
            if ($categorySortBy) {
                if (!$availableOrders) {
                    $availableOrders = $this->_getConfig()->getAttributeUsedForSortByArray();
                }

                if (isset($availableOrders[$categorySortBy])) {
                    $this->setSortBy($categorySortBy);
                }
            }
        }

        return $this;

    }//end prepareSortableFieldsByCategory()


    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $mergedId = [];
        $identities = [];
        foreach ($this->_getProductCollection() as $item) {
            $mergedId[] = $item->getIdentities();
        }
        $identities = array_merge(...$mergedId);

        $category = $this->getLayer()->getCurrentCategory();
        if ($category) {
            $identities[] = Product::CACHE_PRODUCT_CATEGORY_TAG.'_'.$category->getId();
        }

        return $identities;

    }//end getIdentities()


    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data'   => [
                'product'                                                      => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($url),
            ],
        ];

    }//end getAddToCartPostParams()


    /**
     * Return the final product price
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        $priceRender = $this->getPriceRender();

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container'     => true,
                    'display_minimal_price' => true,
                    'zone'                  => Render::ZONE_ITEM_LIST,
                ]
            );
        }

        return $price;

    }//end getProductPrice()


    /**
     * Get price render
     *
     * @return Render
     * @throws LocalizedException
     */
    protected function getPriceRender()
    {
        return $this->getLayout()->getBlock('product.price.render.default');

    }//end getPriceRender()


}//end class
