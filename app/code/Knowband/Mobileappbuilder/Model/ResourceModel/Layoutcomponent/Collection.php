<?php

namespace Knowband\Mobileappbuilder\Model\ResourceModel\Layoutcomponent;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'id_component';
    protected $_eventPrefix = 'kb_mobileapp_layout_component_collection';
    protected $_eventObject = 'kb_mobileapp_layout_component_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\Layoutcomponent', 'Knowband\Mobileappbuilder\Model\ResourceModel\Layoutcomponent');
    }

}
