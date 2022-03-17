<?php

namespace Knowband\Mobileappbuilder\Model;

class Banner extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'kb_sliders_banners';
    protected $_cacheTag = 'kb_sliders_banners';
    protected $_eventPrefix = 'kb_sliders_banners';

    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\ResourceModel\Banner');
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
