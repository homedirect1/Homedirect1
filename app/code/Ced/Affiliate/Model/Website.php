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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Affiliate\Model;
 
use \Magento\Store\Model\StoreRepository;
  
class Website extends \Magento\Framework\DataObject 
    implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var Rate
     */
    protected $_storeRepository;
    protected $_storeManager;
      
    /**
     * @param StoreRepository      $storeRepository
     */
    public function __construct(
        StoreRepository $storeRepository,
    	\Magento\Store\Model\StoreManagerInterface $storemanager
    ) {
    	$this->_storeManager =$storemanager;
        $this->_storeRepository = $storeRepository;
    }
   
    public function toOptionArray()
    {
      //  $stores = $this->_storeRepository->getWebsites();
        $websites =$this->_storeManager->getWebsites();
       // print_r($web->getData());die;
        $websiteIds = array();
        $storeList = array();
        foreach ($websites as $store) {
        	//print_r($store);
            $websiteId = $store["website_id"];
            $storeId = $store["website_id"];
            $storeName = $store["name"];
            $storeList[$storeId] = $storeName;
            array_push($websiteIds, $websiteId);
        }
        
        return $storeList;
    }
    public function getWebsites() {
    	return $this->_storeManager->getWebsites();
    }
}