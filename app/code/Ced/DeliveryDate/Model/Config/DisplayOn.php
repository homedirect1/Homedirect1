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

class DisplayOn extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $fields = ['order','invoice','shipment'];
        $options = array();
        foreach ($fields as $key => $value) {
            $options[] = array(
                'label' => $value,
                'value' => $value
            );
        }
        return $options;
    }

}