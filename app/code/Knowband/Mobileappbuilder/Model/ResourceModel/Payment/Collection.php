<?php

namespace Knowband\Mobileappbuilder\Model\ResourceModel\Payment;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'kb_payment_id';
    protected $_eventPrefix = 'kb_payment_details_collection';
    protected $_eventObject = 'payment_details_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\Payment', 'Knowband\Mobileappbuilder\Model\ResourceModel\Payment');
    }

}
