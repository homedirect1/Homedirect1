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
 * @package     Ced_CsGst
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
namespace Ced\CsGst\Helper;
 
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    public function __construct(\Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Tax\Model\ClassModelFactory $classModelFactory,
        \Ced\CsMarketplace\Model\Vendor $marketplaceVendor,
        \Magento\Sales\Model\Order\Address $orderAddress,
        \Magento\Catalog\Model\Product $catalogProduct,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {

        $this->_objectManager = $objectManager;
        parent::__construct($context);
        $this->classModelFactory = $classModelFactory;
        $this->marketplaceVendor = $marketplaceVendor;
        $this->catalogProduct = $catalogProduct;
        $this->orderAddress = $orderAddress;
        $this->pricingHelper = $pricingHelper;
        $this->_storeManager = $storeManager;
    }
    
    public function moduleEnable()	{
    	return $this->scopeConfig->getValue('cedgst/general/active',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Tax Class Id for Tax
     * @return int
     */
    public function getTaxClassIdForVendor($product,$source,$vendorId)	{
    	$classid = '';
    	if ($product && $source) {
    		$warehouse = $this->getWarehouseForVendor($vendorId);
    		$pid = $product->getEntityId();
    		$rate = ltrim($this->gstRate($pid),0);
    		if ($source == $warehouse) {
    			$collection = $this->classModelFactory->create()->getCollection()
    			->setClassTypeFilter('PRODUCT')
    			->addFieldToFilter('class_name',array('eq' => 'GST'.$rate))->getFirstItem();
    			$classid = $collection['class_id'];
    			return $classid;
    		}else{
    			$collection = $this->classModelFactory->create()->getCollection()
    			->setClassTypeFilter('PRODUCT')
    			->addFieldToFilter('class_name',array('like' => '%IGST'.$rate))->getFirstItem();
    			$classid = $collection['class_id'];
    			return $classid;
    		}
    	}
    }
    
    public function getTaxClassIdForAdmin($product,$source)	{
    	$classid = '';
    	if ($product && $source) {
    		$warehouse = $this->getWarehouseForAdmin();
    		$pid = $product->getEntityId();
    		$rate = ltrim($this->gstRate($pid),0);
    		if ($source == $warehouse) {
    			$collection = $this->classModelFactory->create()->getCollection()
    			->setClassTypeFilter('PRODUCT')
    			->addFieldToFilter('class_name',array('eq' => 'GST'.$rate))->getFirstItem();
    			$classid = $collection['class_id'];
    			return $classid;
    		}else{
    			$collection = $this->classModelFactory->create()->getCollection()
    			->setClassTypeFilter('PRODUCT')
    			->addFieldToFilter('class_name',array('like' => '%IGST'.$rate))->getFirstItem();
    			$classid = $collection['class_id'];
    			
    			return $classid;
    		}
    	}
    }
    
    public function getDefaultTax($product,$rate){
        $rate = ltrim($rate, '0');
        $collection = $this->classModelFactory->create()->getCollection()
    			->setClassTypeFilter('PRODUCT')
    			->addFieldToFilter('class_name',array('eq' => 'GST'.$rate))->getFirstItem();
    			$classid = $collection['class_id'];
    			return $classid;
    }
    
    public function getWarehouseForVendor($vendorId)	{
    	$vendor = $this->marketplaceVendor->load($vendorId);
    	return $vendor->getRegionId();
    }
    
    /**
     * Get Product GST Rate
     * @return int
     */
    public function gstRate($id)	{
    	$product = $this->catalogProduct->load($id);
    	return $product->getGstRate();
    }

   
    /**
     * Function for getting Config value of current store
     *
     * @param string $path,
     */
    public function getStoreConfig($path,$storeId=null)
    {
        $store=$this->_storeManager->getStore($storeId);
        return $this->scopeConfig->getValue($path, 'store', $store->getCode());
    }

    public function getFormattedPrice($price){
    	$formattedPrice = $this->pricingHelper->currency($price, true, false);
    	return $formattedPrice;
    }
    
    public function getGstinNo(){
    	return $this->scopeConfig->getValue('cedgst/general/gstin_no',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getHsn($id){
    	$product = $this->catalogProduct->load($id);
    	return $product->getHsn();
    }

    public function getShippingSource($shippingId)	{
    	$id = '';
    	$address = $this->orderAddress->load($shippingId);
    	$id = $address->getRegionId();
    	return $id;
    }

    public function getHsnByProductId($pid)	{
    	$hsn = '';
    	$hsn = $this->catalogProduct->load($pid)->getHsn();
    	return $hsn;
    }

    public function getWarehouseForAdmin()	{
    	return $this->scopeConfig->getValue('cedgst/general/warehouse',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function create($class){
    	return $this->_objectManager->create($class);
    }
    
    public function getGstinForAdmin(){
    	return $this->scopeConfig->getValue('cedgst/general/gstin_no',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getObjectManager($class){
        return $this->_objectManager->get($class);
    }
}
