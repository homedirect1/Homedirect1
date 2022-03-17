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
 * @package     Ced_ReferralSystem
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */ 
namespace Ced\ReferralSystem\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class Dataview implements ArgumentInterface
{
    public function __construct(
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\Stdlib\DateTime\Timezone $dateTime
    ) {
        $this->priceHelper  = $priceHelper;
        $this->dateTime  = $dateTime;
    }

    public function generatePromoCode() 
    {
        $length = 6;
        $rndId = hash('md5', uniqid(rand(),1));
        $rndId = strip_tags($rndId); 
        $rndId = str_replace([".", "$"],"",$rndId);
        $rndId = strrev(str_replace("/","",$rndId));
        if ($rndId !==null ){
            return strtoupper(substr($rndId, 0, $length));
        } 
        return strtoupper($rndId);
    }

    public function CurrencyFormatter($price) {
        
        return $this->priceHelper->currency($price,true,false);
    }

    public function getDateTime($datetime) {
        return $this->dateTime->formatDateTime(
            $datetime, 
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::NONE,
             null,
             null,
             'yyyy-MM-dd hh:mm:ss'
         );
     }
}
