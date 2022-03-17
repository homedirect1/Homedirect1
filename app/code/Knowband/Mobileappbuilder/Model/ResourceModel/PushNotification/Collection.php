<?php

namespace Knowband\Mobileappbuilder\Model\ResourceModel\PushNotification;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'kb_notification_id';
    protected $_eventPrefix = 'kb_push_notifications_history_collection';
    protected $_eventObject = 'push_notifications_history_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\PushNotification', 'Knowband\Mobileappbuilder\Model\ResourceModel\PushNotification');
    }

}
