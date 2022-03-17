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
 * @category  Ced
 * @package   Ced_CsFreeshipping
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
 

namespace Ced\CsFreeshipping\Helper;

/**
 * Configuration data of carrier
 */
class Config
{
    

    protected $_helper;
    protected $_scopeConfig;
    
    protected $_objectManager;
    protected $_vsettingsFactory;
    
    public function __construct(\Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager
        //\Ced\CsMultiShipping\Model\VsettingsFactory  $vsettingsFactory
    ) {
    
        $this->_scopeConfig = $scopeConfig;
        $this->_objectManager = $objectManager;
        //$this->_vsettingsFactory = $vsettingsFactory;
        //parent::__construct($context);
        $this->_helper = $this->_objectManager->get('Ced\CsFreeshipping\Helper\Config');
    }
    
    public function isEnabled($storeId=0)
    {
        
        if($storeId == 0) {
            $storeId = $this->_helper->getStore()->getId(); 
        }
        
        return $this->_scopeConfig->getValue('ced_csmarketplace/multishipping/csfreeshipping', $storeId);
        //return $this->_helper->getStoreConfig('csfreeshipping/general/active', $storeId);
    }
    
    
}