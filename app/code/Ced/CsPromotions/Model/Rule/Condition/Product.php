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
 * @package     Ced_CsPromotions
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsPromotions\Model\Rule\Condition;

use Magento\Catalog\Model\ProductCategoryList;

/**
 * Class Product
 * @package Ced\CsPromotions\Model\Rule\Condition
 */
class Product extends \Magento\CatalogRule\Model\Rule\Condition\Product
{
    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $backendUrl;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Magento\Framework\App\AreaList
     */
    protected $areaList;

    /**
     * Product constructor.
     * @param \Magento\Backend\Model\Url $backendUrl
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Framework\App\AreaList $areaList
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param array $data
     * @param ProductCategoryList|null $categoryList
     */
    public function __construct(
        \Magento\Backend\Model\Url $backendUrl,
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\AreaList $areaList,
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        array $data = [],
        ProductCategoryList $categoryList = null
    )
    {
        $this->backendUrl = $backendUrl;
        $this->state = $state;
        $this->areaList = $areaList;

        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data,
            $categoryList
        );
    }

    /**
     * @return bool|mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getValueElementChooserUrl()
    {
        $this->setData('area', 'adminhtml');
        $urlbuilder = $this->backendUrl;
        $url = false;
        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
                $url = 'catalog_rule/promo_widget/chooser/attribute/' . $this->getAttribute();
                if ($this->getJsFormObject()) {
                    $url .= '/form/' . $this->getJsFormObject();
                }
                break;
            default:
                break;
        }
        if ($this->state->getAreaCode() == 'frontend') {
            $url !== false ? $this->_backendData->getUrl($url) : '';
            $url = $urlbuilder->getUrl($url);
            $replace = $this->areaList->getFrontName('adminhtml') . '/catalog_rule';
            $url = str_replace($replace, '/cspromotions', $url);
            return $url;

        }

        return $url !== false ? $this->_backendData->getUrl($url) : '';
    }

}
