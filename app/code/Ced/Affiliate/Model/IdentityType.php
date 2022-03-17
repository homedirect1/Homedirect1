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
  
class IdentityType extends \Magento\Framework\DataObject 
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
    	return [
    	['value' => 'driving',
    	'label' => __('Driving License')
    	],
    	[
    	'value' => 'pan',
    	'label' => __('Pan Card')
    	],
    	[
    	'value' => 'passport',
    			'label' => __('Passport')
    	],
    	[
    	'value' => 'other',
    			'label' => __('Other')
    	]
    	];
        
        
    }
  
}