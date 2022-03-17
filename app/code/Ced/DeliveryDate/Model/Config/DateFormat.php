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
 * @package     Ced_DeliveryDate
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\DeliveryDate\Model\Config;

use Magento\Framework\Model\AbstractModel;
use Magento\Shipping\Model\Config\Source\Allmethods;

/**
 * @package Ced\DeliveryDate\Model
 */
class DateFormat extends AbstractModel
{
    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $dateTime;
     /**
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     */
    public function __construct(\Magento\Framework\Stdlib\DateTime\Timezone $dateTime)
    {
        $this->dateTime = $dateTime;
    }
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options= [ 'dd-mm-yy' , 'mm-dd-yy' , 'yy-mm-dd', 'dd/mm/yy' , 'mm/dd/yy' , 'yy/mm/dd' ];
        return $options;
    }
    /*public function toOptionArray()
    {
        $formaterTypes= [ \IntlDateFormatter::FULL , \IntlDateFormatter::LONG , \IntlDateFormatter::MEDIUM , \IntlDateFormatter::SHORT , \IntlDateFormatter::GREGORIAN ];
        $options = [];
        foreach ($formaterTypes as $key=>$type){
            $options[] = $this->dateTime->getDateFormat($type);
        }
        return $options;
    }*/
}