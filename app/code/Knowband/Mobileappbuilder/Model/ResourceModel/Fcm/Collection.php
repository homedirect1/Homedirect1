<?php

namespace Knowband\Mobileappbuilder\Model\ResourceModel\Fcm;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'fcm_details_id';
    protected $_eventPrefix = 'kb_fcm_details_collection';
    protected $_eventObject = 'fcm_details_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\Fcm', 'Knowband\Mobileappbuilder\Model\ResourceModel\Fcm');
    }

}
