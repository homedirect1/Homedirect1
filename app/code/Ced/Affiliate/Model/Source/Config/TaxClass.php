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
namespace Ced\Affiliate\Model\Source\Config;
 
use \Magento\Tax\Model\Calculation\Rate;
  
class TaxClass extends \Magento\Framework\DataObject 
    implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var Rate
     */
    protected $_taxModelConfig;
      
    /**
     * @param Rate               $taxModelConfig
     */
    public function __construct(
        Rate $taxModelConfig
    ) {
        $this->_taxModelConfig = $taxModelConfig;
    }
   
    public function toOptionArray()
    {
        $taxRates = $this->_taxModelConfig->getCollection()->getData();
        $taxArray = array();
        foreach ($taxRates as $tax) {
            $taxRateId = $tax['tax_calculation_rate_id'];
            $taxCode = $tax["code"];
            $taxRate = $tax["rate"];
            $taxName = $taxCode.'('.$taxRate.'%)';
            $taxArray[$taxRateId] = $taxName;
        }
        return $taxArray;
    }
}