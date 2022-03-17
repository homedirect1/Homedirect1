<?php

namespace Knowband\Mobileappbuilder\Model;

class Verification extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'kb_mobileapp_unique_verification';
    protected $_cacheTag = 'kb_mobileapp_unique_verification';
    protected $_eventPrefix = 'kb_mobileapp_unique_verification';

    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\ResourceModel\Verification');
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
