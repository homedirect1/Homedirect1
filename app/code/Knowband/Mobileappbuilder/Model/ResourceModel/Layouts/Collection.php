<?php

namespace Knowband\Mobileappbuilder\Model\ResourceModel\Layouts;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'id_layout';
    protected $_eventPrefix = 'kb_mobileapp_layouts_collection';
    protected $_eventObject = 'kb_mobileapp_layouts_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Knowband\Mobileappbuilder\Model\Layouts', 'Knowband\Mobileappbuilder\Model\ResourceModel\Layouts');
    }

}
