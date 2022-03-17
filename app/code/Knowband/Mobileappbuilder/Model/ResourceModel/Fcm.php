<?php

namespace Knowband\Mobileappbuilder\Model\ResourceModel;

class Fcm extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context)
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('kb_fcm_details', 'fcm_details_id');
    }

}
