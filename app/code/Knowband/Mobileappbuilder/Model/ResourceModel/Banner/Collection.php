<?php

namespace Knowband\Mobileappbuilder\Model\ResourceModel\Banner;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'kb_banner_id';
    protected $_eventPrefix = 'kb_sliders_banners_collection';
    protected $_eventObject = 'sliders_banners_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\Banner', 'Knowband\Mobileappbuilder\Model\ResourceModel\Banner');
    }

}
