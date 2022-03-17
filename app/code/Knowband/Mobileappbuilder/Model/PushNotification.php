<?php

namespace Knowband\Mobileappbuilder\Model;

class PushNotification extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'kb_push_notifications_history';
    protected $_cacheTag = 'kb_push_notifications_history';
    protected $_eventPrefix = 'kb_push_notifications_history';

    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\ResourceModel\PushNotification');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }
}
