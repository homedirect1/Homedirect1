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
 * @package     Ced_GShop
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GShop\Model\Source\Config;

class MagentoAttributes implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $attributeOptions */
        $attributeOptions =  $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection')
            ->getItems();
        $option = [];
        $option[] = ['label' => '-- Select Buffer Attribute --',
            'value' => ''];
        foreach ($attributeOptions as $key => $attribute) {
            $option[$key+1] = ['label' => $attribute->getAttributecode(),
                'value' => $attribute->getAttributecode()];
        }
        return $option;
    }
}
