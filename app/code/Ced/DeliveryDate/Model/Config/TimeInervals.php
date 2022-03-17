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

/**
 * @package Ced\DeliveryDate\Model
 */
class TimeInervals extends AbstractModel
{
    public function toOptionArray()
    {
        $time=[];
        for($i=0;$i<24;$i++){
            $time[] = ($i < 10) ? '0'.$i.':00': $i.':00';
        }
        $options = (object)$time;
        return $options;
    }
}