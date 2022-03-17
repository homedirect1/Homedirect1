<?php

namespace Ced\GroupBuying\Ui\Component\Listing\Column\Cedgroupbuyinggrid;

use Magento\Framework\Option\ArrayInterface;

class GroupStatusDropdown implements ArrayInterface
{

    /**
     * Dropdown Option for group status
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Disapprove')],
            ['value' => 1, 'label' => __('Approve')],
            ['value' => 2, 'label' => __('Pending')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('Disapprove'), 1 => __('Approve'), 2 =>__('Pending')];
    }
}
