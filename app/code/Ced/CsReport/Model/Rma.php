<?php
namespace Ced\CsReport\Model;

class Rma
{
    protected $moduleManager;
    protected $objectManager;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
    }

    public function create(array $data = array())
    {
        if ($this->moduleManager->isEnabled('Ced_CsRma')) {
            $instanceName =  'Ced\CsRma\Model\ResourceModel\Request\CollectionFactory';
        } else {
            return null;
        }
        return $this->objectManager->create($instanceName, $data);
    }
}