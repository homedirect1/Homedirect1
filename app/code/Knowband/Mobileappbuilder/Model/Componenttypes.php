<?php

namespace Knowband\Mobileappbuilder\Model;

class Componenttypes extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'kb_mobileapp_component_types';
    protected $_cacheTag = 'kb_mobileapp_component_types';
    protected $_eventPrefix = 'kb_mobileapp_component_types';

    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\ResourceModel\Componenttypes');
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
