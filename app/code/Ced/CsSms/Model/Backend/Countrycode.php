<?php

namespace Ced\CsSms\Model\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;
use Magento\Framework\Serialize\Serializer\Json;
class Countrycode extends ArraySerialized
{
    /**
     * @var \Magento\Framework\View\DesignInterface|null
     */
    protected $_design = null;
    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    protected $serialize;

    /**
     * Countrycode constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\View\DesignInterface $design
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \Magento\Framework\Serialize\Serializer\Serialize $serialize
     * @param Json|null $serializer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize,
        Json $serializer = null,

        array $data = []
    ) {
        $this->_design = $design;
        $this->serialize = $serialize;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data,$serializer);
    }

    /**
     * Process data after load
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $arr   = json_decode($value,true);

        if(!is_array($arr)) return '';

        foreach ($arr as $k=>$val) {
            if(!is_array($val)) {
                unset($arr[$k]);
                continue;
            }
        }
        $this->setValue($arr);
    }

    /**
     * Prepare data before save
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        $exixting=array();
        foreach ($value as $key => $val) {
            if($key=='__empty')continue;
            if(in_array(trim($val['country']),$exixting)){
                unset($value[$key]);
            }else{
                array_push($exixting,trim($val['country']));
            }
        }
        $value = json_encode($value);
        $this->setValue($value);
    }
}
