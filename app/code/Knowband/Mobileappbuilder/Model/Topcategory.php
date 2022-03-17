<?php

namespace Knowband\Mobileappbuilder\Model;

class Topcategory extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'kb_mobileapp_top_category';
    protected $_cacheTag = 'kb_mobileapp_top_category';
    protected $_eventPrefix = 'kb_mobileapp_top_category';

    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\ResourceModel\Topcategory');
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
