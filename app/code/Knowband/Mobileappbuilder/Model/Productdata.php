<?php

namespace Knowband\Mobileappbuilder\Model;

class Productdata extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'kb_mobileapp_product_data';
    protected $_cacheTag = 'kb_mobileapp_product_data';
    protected $_eventPrefix = 'kb_mobileapp_product_data';

    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\ResourceModel\Productdata');
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
