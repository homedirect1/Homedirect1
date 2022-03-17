<?php

namespace Knowband\Mobileappbuilder\Model\ResourceModel\Verification;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'id_verification';
    protected $_eventPrefix = 'kb_mobileapp_unique_verification_collection';
    protected $_eventObject = 'kb_mobileapp_unique_verification_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\Verification', 'Knowband\Mobileappbuilder\Model\ResourceModel\Verification');
    }

}
