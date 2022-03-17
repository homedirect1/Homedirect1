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
 * @package     Ced_HelpDesk
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\HelpDesk\Model\System\Config\Source;


class Days implements \Magento\Framework\Option\ArrayInterface
{
   
	
  public function toOptionArray()
    {
        return [
        ['value' => 'Monday', 'label' => __('Monday')],
        ['value' => 'Tuesday', 'label' => __('Tuesday')],
        ['value' => 'Wednesday', 'label' => __('Wednesday')],
        ['value' => 'Thursday', 'label' => __('Thursday')],
        ['value' => 'Friday', 'label' => __('Friday')],
        ['value' => 'Saturday', 'label' => __('Saturday')],
        ['value' => 'Sunday', 'label' => __('Sunday')],


];
    }
}
