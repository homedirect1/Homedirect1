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
 * @package     Ced_CsGst
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsGst\Observer;

use Magento\Framework\Event\ObserverInterface;

class SetTax implements ObserverInterface
{

    public function __construct(\Ced\CsGst\Helper\Data $helper){
        $this->helper = $helper;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $_product = $observer->getProduct();
        $gstrate = $_product->getGstRate();
        try{
            if($gstrate){
                $taxclassid = $this->helper->getDefaultTax($_product,$gstrate);
                $_product->setTaxClassId($taxclassid);
            }else{
                $_product->setTaxClassId(0);
            }
        }catch(\Exception $e){
            return $this;
        }
    }
}