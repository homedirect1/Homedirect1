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

namespace Ced\CsFreeshipping\Model\Vsettings\Shipping\Methods;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Ced\DomesticAustralianShipping\Helper\Config;

class Free extends \Ced\CsMultiShipping\Model\Vsettings\Shipping\Methods\AbstractModel
{
    protected $_code = 'freeshipping';
    protected $_fields = array();
    protected $_codeSeparator = '-';
    protected $_scopeConfig;
    protected $_countryFactory;
    /**
     * Retreive input fields
     *
     * @return array
     */
    
    
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Directory\Model\Config\Source\CountryFactory $countryFactory,
        array $data = []
    ) {
        $this->_countryFactory = $countryFactory;
        $this->_scopeConfig = $scopeConfig;
    
    }
    
    public function getFields() 
    {
        $fields['active'] = array('type'=>'select',
                             'required'=>true,
                             'values'=>array(
                                 array('label'=>__('Yes'),'value'=>1),
                                 array('label'=>__('No'),'value'=>0)
                             )
        );

        $fields['min_order_amount'] = array('type'=>'text');
        
        $alloptions = $this->_countryFactory->create()->toOptionArray();
        
        if($this->_scopeConfig->getValue('carriers/freeshipping/sallowspecific', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            
            
            $availableCountries = explode(',', $this->_scopeConfig->getValue('carriers/freeshipping/specificcountry', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
            foreach($alloptions as $key => $value){
                if(in_array($value['value'], $availableCountries)) {
                    $allcountry[] = $value;
                }
            }
        }else{
            $allcountry = $alloptions;
        }
    
        
        $fields['allowed_country'] = array('type'=>'multiselect',
                                 'values'=>$allcountry
                                 );        
        return $fields;
    }
    
    /**
     * Retreive labels
     *
     * @param  string $key
     * @return string
     */
    public function getLabel($key) 
    {
        switch($key) {
        case 'label' : 
            return __('Free Shipping');break;
        case 'min_order_amount': 
            return __('Minimum Order Amount For Free Shipping');break;
        case 'allowed_country': 
            return __('Allowed Country');break;
        case 'active': 
            return __('Active');break;
        default : 
            return parent::getLabel($key); break;
        }
    }
    
}
