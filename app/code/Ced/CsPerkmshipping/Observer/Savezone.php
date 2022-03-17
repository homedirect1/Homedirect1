<?php 

/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_CsPerkmshipping
  * @author   CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

namespace Ced\CsPerkmshipping\Observer; 

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

Class Savezone implements ObserverInterface
{
	
	/**
	 * @var \Magento\Framework\ObjectManagerInterface
	 */
	protected $_objectManager;
	
	protected $_coreRegistry;
	
	public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager,
			\Magento\Framework\Registry $registry
								)
    {
    	$this->_coreRegistry = $registry;
        $this->_objectManager = $objectManager;
    }
	
	 /**

     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
	{
	if(isset($_POST) && !$this->_coreRegistry->registry('perkm_shipzone')){
                
                try{
				
				$data = isset($_POST['group']['perkmshipping']['shipping_zones']) ? json_encode($_POST['group']['perkmshipping']['shipping_zones']) : '' ;

				$vendorId = $this->_objectManager->get('Magento\Customer\Model\Session')->getData('vendor_id');
				$key = 'shipping/'.'perkmshipping'.'/shipping_zones';
				$vsetting_model = $this->_objectManager->create('Ced\CsMarketplace\Model\Vsettings')->getCollection()->addFieldToFilter('vendor_id',$vendorId);
           
				$settingid ='';

				foreach($vsetting_model as $value)
				{
				
					
						if($value->getKey()=='shipping/perkmshipping/shipping_zones')
						{
							$settingid = $value->getId();
							break;
						}
				

				}

         if($settingid){
						$vsettingModel = $this->_objectManager->get('Ced\CsMarketplace\Model\Vsettings')->load($settingid);
						$this->_coreRegistry->register('perkm_shipzone', 1);
						$vsettingModel->setData('value',$data)->save();		
						}			
		
	     		}catch(\Exception $e){
	 					return ;
					}
			}
			return $this;
	
		}
}
