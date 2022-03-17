<?php

namespace Knowband\Mobileappbuilder\Model\ResourceModel;

class OrderStatus extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context)
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('kb_orderstatus_details', 'kb_orderstatus_id');
    }

}
