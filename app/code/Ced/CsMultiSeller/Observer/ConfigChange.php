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
 * @package     Ced_CsMarketplace
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMultiSeller\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory as vproductsCollectionFactory;
use Magento\Catalog\Model\Product\Action as productAction;
use Magento\Catalog\Model\ProductFactory;

/**
 * Class ConfigChange
 * @package Ced\CsMultiSeller\Observer
 */
class ConfigChange implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var vproductsCollectionFactory
     */
    protected $vproductsCollectionFactory;

    /**
     * @var productAction
     */
    protected $productAction;

    /**
     * @var ProductFactory
     */
    protected $productFactory;


    /**
     * ConfigChange constructor.
     * @param RequestInterface $request
     * @param WriterInterface $configWriter
     * @param vproductsCollectionFactory $vproductsCollectionFactory
     * @param productAction $productAction
     */
    public function __construct(
        RequestInterface $request,
        WriterInterface $configWriter,
        vproductsCollectionFactory $vproductsCollectionFactory,
        productAction $productAction,
        productFactory $productFactory
    ) {
        $this->request = $request;
        $this->configWriter = $configWriter;
        $this->vproductsCollectionFactory = $vproductsCollectionFactory;
        $this->productAction = $productAction;
        $this->productFactory = $productFactory;
    }

    /**
     * Set Products Visibility
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $meetParams = $this->request->getParam('groups');
        $visibility = $meetParams['ced_csmultiseller']['fields']['catalogsearchindividually']['value'];
        $allowConfigurable=$meetParams['ced_csmultiseller']['fields']['configurable_product']['value'];
        $disable_child = $meetParams['ced_csmultiseller']['fields']['disable_child']['value'];
        $parent_ids=[];
        if ($disable_child == '1') {
            $parent_ids = $this->vproductsCollectionFactory->create()->addFieldToFilter('is_multiseller', ['eq' => 1])->getColumnValues('parent_id');
            foreach ($parent_ids as $col) {
                $parentProduct = $this->productFactory->create()->load($col);
                $parentProductStatus = $parentProduct->getStatus();
                if ($parentProductStatus == '2') {
                    $childDisableCollection = $this->vproductsCollectionFactory->create()->addFieldToFilter('parent_id', ['eq' => $col])->getColumnValues('product_id');
                    $this->productAction->updateAttributes($childDisableCollection, ['status' => 2], 0);
                }
            }
        } else{
            $parent_ids = $this->vproductsCollectionFactory->create()->addFieldToFilter('is_multiseller', ['eq' => 1])->getColumnValues('parent_id');
            foreach ($parent_ids as $col) {
                $parentProduct = $this->productFactory->create()->load($col);
                $parentProductStatus = $parentProduct->getStatus();
                if ($parentProductStatus == '2') {
                    $childDisableCollection = $this->vproductsCollectionFactory->create()->addFieldToFilter('parent_id', ['eq' => $col])->getColumnValues('product_id');
                    $this->productAction->updateAttributes($childDisableCollection, ['status' => 1], 0);
                }
            }

        }

        $collection = $this->vproductsCollectionFactory->create()->getColumnValues('product_id');
        $configurableChildCollection = $this->vproductsCollectionFactory->create()->addFieldToFilter('is_configurable_child', "1")->getColumnValues('product_id');
        if ($visibility == '0') {
            foreach ($parent_ids as $col) {
                $parentProduct = $this->productFactory->create()->load($col);
                $parentProductStatus = $parentProduct->getStatus();
                $childCollection = $this->vproductsCollectionFactory->create()->addFieldToFilter('is_multiseller', "1")->getColumnValues('product_id');
                $this->productAction->updateAttributes($childCollection, ['visibility' => 1], 0);

            }
            if($allowConfigurable=='1' && !empty($configurableChildCollection)){
                $this->productAction->updateAttributes($configurableChildCollection, ['visibility' => 1], 0);
            }
        } else {
            $this->productAction->updateAttributes($collection, ['visibility' => 4], 0);
            if(!empty($configurableChildCollection)){
                $this->productAction->updateAttributes($configurableChildCollection, ['visibility' => 4], 0);
            }
        }

        return $this;
    }
}
