<?php

namespace Knowband\Mobileappbuilder\Model\ResourceModel\Componenttypes;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'kb_mobileapp_component_types_collection';
    protected $_eventObject = 'kb_mobileapp_component_types_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\Componenttypes', 'Knowband\Mobileappbuilder\Model\ResourceModel\Componenttypes');
    }

}
