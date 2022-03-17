<?php

namespace Knowband\Mobileappbuilder\Model\ResourceModel;

class Layoutcomponent extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context)
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('kb_mobileapp_layout_component', 'id_component');
    }

}
