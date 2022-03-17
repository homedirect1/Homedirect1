<?php 
namespace Ced\CsMultiSeller\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
class UpdateProduct implements ObserverInterface
{
  public function __construct(
    \Ced\CsMarketplace\Model\VproductsFactory $modelVproducts,
    \Ced\CsMarketplace\Model\ResourceModel\Vproducts $resourceVproducts,
    \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory $vproductsCollectionFactory,
  \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
  {
    $this->vproductsCollectionFactory = $vproductsCollectionFactory;
    $this->scopeConfig = $scopeConfig;
    $this->modelVproducts = $modelVproducts;
    $this->resourceVproducts = $resourceVproducts;
  }

  public function execute(\Magento\Framework\Event\Observer $observer)
  {
    $product = $observer->getData('product');
    
    $productId=$product->getId();
    $productType=$product->getTypeId();
   if($productType==ConfigurableType::TYPE_CODE){
    try{
      
      $_children = $product->getTypeInstance()->getUsedProducts($product);
      foreach ($_children as $child){
        $collection = $this->vproductsCollectionFactory->create()
        ->addFieldToFilter('product_id', ['eq' => $child->getID()]);
        foreach ($collection as $k => $product){
          $product->setIsConfigurableChild('1');
          $product->setConfigurableProductId($productId);  
        }
        $this->resourceVproducts->save($product);
      }
    
  }catch(\Exception $e){
    echo $e;die;
  }
   }
    
  }
}
