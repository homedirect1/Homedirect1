<?php

namespace Ced\Rewardsystem\Block\Config\Source;

class CartProductOption implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'catalog', 'label' => __('Product')], ['value' => 'shopping', 'label' => __('Cart Rule')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['catalog' => __('Product'), 'shopping' => __('Cart Rule')];
    }
}
