<?php

namespace Knowband\Mobileappbuilder\Model\ResourceModel;

class Verification extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context)
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('kb_mobileapp_unique_verification', 'id_verification');
    }

}
