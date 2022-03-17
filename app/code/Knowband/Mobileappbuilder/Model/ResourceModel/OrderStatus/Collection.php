<?php

namespace Knowband\Mobileappbuilder\Model\ResourceModel\OrderStatus;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'kb_orderstatus_id';
    protected $_eventPrefix = 'kb_orderstatus_details_collection';
    protected $_eventObject = 'orderstatus_details_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\OrderStatus', 'Knowband\Mobileappbuilder\Model\ResourceModel\OrderStatus');
    }

}
