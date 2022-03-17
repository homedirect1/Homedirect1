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
 * @package     Ced_CsHyperlocal
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsHyperlocal\Ui\DataProvider\Product\Form\Modifier;


use Ced\CsHyperlocal\Helper\Data;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\StoreManagerInterface;

class Eav extends AbstractModifier
    {

    const ATT_SHIPPING_PRODUCT_LOCATION = 'shipping_product_location';

    /**
     * @var array
     */
    private $meta = [];

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    private $dataHelper;

    private $storeManager;

    /**
     * @param Data $dataHelper
     * @param StoreManagerInterface $storeManager
     * @param ArrayManager $arrayManager
     * @param LocatorInterface $locator
     */
    public function __construct(
        Data $dataHelper,
        StoreManagerInterface $storeManager,
        ArrayManager $arrayManager,
        LocatorInterface $locator
    ) {
        $this->arrayManager = $arrayManager;
        $this->locator = $locator;
        $this->dataHelper = $dataHelper;
        $this->storeManager = $storeManager;
        }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    private function getProduct() {
        return $this->locator->getProduct();
        }

    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        if ($this->checkForShippingLocationCondition()) {
            $this->meta = $this->arrayManager->remove(
                $this->arrayManager->findPath(
                    static::CONTAINER_PREFIX . self::ATT_SHIPPING_PRODUCT_LOCATION,
                    $this->meta
                ),
                $this->meta
            );
        }

        return $this->meta;
    }

    /**
     * @param array $data
     * @return array
     * @since 100.1.0
     */
    public function modifyData(array $data)
    {
        if ($this->checkForShippingLocationCondition()) {
            $data = $this->arrayManager->remove(
                $this->arrayManager->findPath(
                    static::CONTAINER_PREFIX . self::ATT_SHIPPING_PRODUCT_LOCATION,
                    $data
                ),
                $data
            );
        }

        return $data;
    }

    private function checkForShippingLocationCondition()
    {
        $storeId = $this->locator->getStore()->getId();
        $store = $this->storeManager->getStore($storeId);

        //$hyperLocalEnabled = $$this->dataHelper->getStoreConfig(Data::XML_GENERAL_GROUP_PATH, $store->getCode());
            
        $filterProductsBy = $this->dataHelper->getStoreConfig(
            Data::FILTER_PRODUCTS_BY,
            $store->getCode()
        );

        $filterType = $this->dataHelper->getStoreConfig(
            Data::FILTER_TYPE,
            $store->getCode()
        );

        return ($filterProductsBy != 'product_location' || $filterType == 'distance') ?: false;
    }

}


