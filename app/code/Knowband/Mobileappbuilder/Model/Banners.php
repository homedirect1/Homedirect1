<?php

namespace Knowband\Mobileappbuilder\Model;

class Banners extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'kb_mobileapp_banners';
    protected $_cacheTag = 'kb_mobileapp_banners';
    protected $_eventPrefix = 'kb_mobileapp_banners';

    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\ResourceModel\Banners');
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
