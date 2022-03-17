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
 * @package   Ced_Customnumbers
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Customnumbers\Model\System\Config\Source;

class Reset implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('One counter without reset')],
            ['value' => 'separate', 'label' => __('Separate Counter for Every Format')],
            ['value' => 'y-m-d', 'label' => __('Reset Counter on Daily Basis')],
            ['value' => 'y-m', 'label' => __('Reset Counter on Monthly Basis')],
            ['value' => 'y', 'label' => __('Reset Counter on Yearly Basis')]
        ];
    }
}
