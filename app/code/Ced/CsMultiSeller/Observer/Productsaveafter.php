<?php


namespace Ced\CsMultiSeller\Observer;


use Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory as vproductsCollectionFactory;
use Magento\Catalog\Model\Product\Action as productAction;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Productsaveafter implements ObserverInterface
{
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagementInterface,
        vproductsCollectionFactory $vproductsCollectionFactory,
        productAction $productAction,
        productFactory $productFactory,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->vproductsCollectionFactory = $vproductsCollectionFactory;
        $this->productAction = $productAction;
        $this->productFactory = $productFactory;
        $this->scopeConfig = $scopeConfig;
        $this->productRepository = $productRepository;
        $this->categoryLinkManagement = $categoryLinkManagementInterface;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getProduct();
        $id = $product->getId();
        $parent_ids = $this->vproductsCollectionFactory->create()->addFieldToFilter('is_multiseller', ['eq' => 1])->getColumnValues('parent_id');
        $category_ids = [];
        if(in_array($id, $parent_ids)){
            $attributes = $product->getTypeInstance(true)->getEditableAttributes($product);
            foreach ($attributes as $key => $value) {
                if ($product->dataHasChangedFor($key)) {
                    if($key == 'category_ids')
                    {
                        $category_ids = $product->getData($key);
                        break;
                    }
                }
            }
        }
        if(!empty($category_ids)){

            $childproduct = $this->vproductsCollectionFactory->create()->addFieldToFilter('parent_id', ['eq' => $id])->getColumnValues('product_id');
            foreach ($childproduct as $key) {
                $product = $this->productRepository->getById($key);
                $this->categoryLinkManagement->assignProductToCategories(
                    $product->getSku(),
                    $category_ids
                );
            }

        }
        $disable_child = $this->scopeConfig->getValue("ced_csmarketplace/ced_csmultiseller/disable_child",ScopeInterface::SCOPE_STORE);
        if ($disable_child == '1') {

            if(in_array($id, $parent_ids))
            {
                $parentProduct = $this->productFactory->create()->load($id);
                $parentProductStatus = $parentProduct->getStatus();
                $childDisableCollection = $this->vproductsCollectionFactory->create()->addFieldToFilter('parent_id', ['eq' => $id])->getColumnValues('product_id');
                if ($parentProductStatus == '2') {
                    $this->productAction->updateAttributes($childDisableCollection, ['status' => 2], 0);
                }else{
                    $this->productAction->updateAttributes($childDisableCollection, ['status' => 1], 0);
                }

            }
        } else{
            if(in_array($id, $parent_ids))
            {
                $parentProduct = $this->productFactory->create()->load($id);
                $parentProductStatus = $parentProduct->getStatus();
                if ($parentProductStatus == '2') {
                    $childDisableCollection = $this->vproductsCollectionFactory->create()->addFieldToFilter('parent_id', ['eq' => $id])->getColumnValues('product_id');
                    $this->productAction->updateAttributes($childDisableCollection, ['status' => 1], 0);
                }

            }

        }

    }
}
