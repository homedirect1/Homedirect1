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
namespace Ced\DeliveryDate\Helper;

class ConfigData extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path
        );
    }
    /*
     * Delivery date field show on selected places
     * return bool(true/false)
     * used to check either field is enable or not in current place
     * */

    public function ddShowOn($check)
    {
        $showOn = $this->scopeConfig->getValue(
            'deliverydate/deliverydate_general/ddshowon'
        );
        $showOn = explode(",",$showOn);
        $showOn = in_array($check,$showOn);
        return $showOn;
    }

    /* check if module is enable or not
     * return true/false
     */
    public function moduleEnabled()
    {
        return $this->scopeConfig->getValue(
            'deliverydate/deliverydate_general/deliverydate_config'
        );
    }
}