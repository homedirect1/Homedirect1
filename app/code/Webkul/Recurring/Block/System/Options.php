<?php
/**
 *  Webkul Softwares
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Block\System;

class Options implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' =>__("All Products"),
                'value' => 'all_products'
            ],
            [
                'label' =>__("Specific Products"),
                'value' => 'specific_products'
            ]
        ];
    }
}
