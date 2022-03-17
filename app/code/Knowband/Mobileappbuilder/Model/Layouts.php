<?php

namespace Knowband\Mobileappbuilder\Model;

class Layouts extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'kb_mobileapp_layouts';
    protected $_cacheTag = 'kb_mobileapp_layouts';
    protected $_eventPrefix = 'kb_mobileapp_layouts';

    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\ResourceModel\Layouts');
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
