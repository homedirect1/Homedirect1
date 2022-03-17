<?php

namespace Knowband\Mobileappbuilder\Model;

class Layoutcomponent extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'kb_mobileapp_layout_component';
    protected $_cacheTag = 'kb_mobileapp_layout_component';
    protected $_eventPrefix = 'kb_mobileapp_layout_component';

    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\ResourceModel\Layoutcomponent');
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
