<?php

namespace Knowband\Mobileappbuilder\Model\ResourceModel\Topcategory;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'kb_mobileapp_top_category_collection';
    protected $_eventObject = 'kb_mobileapp_top_category_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\Topcategory', 'Knowband\Mobileappbuilder\Model\ResourceModel\Topcategory');
    }

}
