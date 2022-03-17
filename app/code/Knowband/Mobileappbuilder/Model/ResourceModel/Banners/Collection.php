<?php

namespace Knowband\Mobileappbuilder\Model\ResourceModel\Banners;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'kb_mobileapp_banners_collection';
    protected $_eventObject = 'kb_mobileapp_banners_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\Banners', 'Knowband\Mobileappbuilder\Model\ResourceModel\Banners');
    }

}
