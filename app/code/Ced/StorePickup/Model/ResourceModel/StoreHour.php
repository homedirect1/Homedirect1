<?php
namespace Ced\StorePickup\Model\ResourceModel;

class StoreHour extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct() 
    {
        $this->_init('ced_store_pickup_hour', 'id');
    }
}