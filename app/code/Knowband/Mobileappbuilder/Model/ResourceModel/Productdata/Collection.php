<?php

namespace Knowband\Mobileappbuilder\Model\ResourceModel\Productdata;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'kb_mobileapp_product_data_collection';
    protected $_eventObject = 'kb_mobileapp_product_data_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\Productdata', 'Knowband\Mobileappbuilder\Model\ResourceModel\Productdata');
    }

}
