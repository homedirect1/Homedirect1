<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_Customnumbers
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Customnumbers\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
     protected $resource;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */

    protected $storeManager;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory
     */

    protected $coreDataValue;
	
	/**
     * @var scopeConfig
     */

    protected $_scopeConfig;

    /**
     * @param Magento\Framework\App\Helper\Context
     * @param Magento\Store\Model\StoreManagerInterface
     * @param Magento\Framework\App\ResourceConnection
     * @param Magento\Framework\ObjectManagerInterface
     * @param Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory
     */

    public function __construct(
        Context $context,
        AppResource $appResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $coreDataValue
    ) {
        $this->objectManager = $objectManager;
        $this->resource = $this->objectManager
                        ->get('Magento\Framework\App\ResourceConnection');
        $this->connection = $this->resource->getConnection();
        $this->storeManager = $storeManager;
        $this->coreDataValue = $coreDataValue;
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }

    /**
     * return module enable value
     */

    public function enableModule($storeId)

    {
        $module = $this->scopeConfig
                ->getValue('ced_customnumbers/general/activation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
        return $module;
    }

    /**
     * @param $entityType
     * @param $field
     * @param $storeId
     * @return scope value
    **/

    public function getConfigValue($entityType, $field, $storeId)
    {

        return $this->scopeConfig->getValue(
            'ced_customnumbers/' . $entityType . '/' . $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $entityType
     * @param $field
     * @param $storeId
     * @param $default
     * @return scope value
    **/

    public function getCoreConfigValue($entityType,$field,$storeId,$default)
    {
        $scope = 'stores';
        $scopeId = $storeId;
        $coreCollection = $this->coreDataValue->create()
                    ->addFieldToFilter('scope', $scope)
                    ->addFieldToFilter('scope_id', $scopeId)
                    ->addFieldToFilter('path','ced_customnumbers/'.$entityType.'/'.$field);

        if($coreCollection->count() > 0){
            return $coreCollection->getFirstItem()->getValue();
        }else{
            $this->connection->insert($this->resource->getTableName('core_config_data'), [
                    'scope'=>'stores',
                    'scope_id'=>$storeId,
                    'path'=>'ced_customnumbers/'.$entityType.'/'.$field,
                    'value'=>$default
                ]
            );
            return $default;
        }
    }

    /**
     * @param $entityType
     * @param $field
     * @param $storeId
     * @param $format
     * @return scope value
    **/

    public function getSeparateCounterValue($entityType,$field,$storeId,$format,$defaultCounter,$defaultDate)
    {
        echo $defaultCounter;
        die("__FILE__");
        $scope = 'stores';
        $scopeId = $storeId;
        $coreCollection = $this->coreDataValue->create()
                    ->addFieldToFilter('scope', $scope)
                    ->addFieldToFilter('scope_id', $scopeId)
                    ->addFieldToFilter('path','ced_customnumbers/'.$entityType.'/'.$field);

        $saveFormat = unserialize($coreCollection->getFirstItem()->getValue());

        if(count($saveFormat) > 0 && is_array($saveFormat)){
            if(isset($saveFormat[$format]))
                return $saveFormat[$format];
            else
                return ['incrment_counter'=> $defaultCounter,'date'=> $defaultDate];
        } else {
            $value = serialize([$format => ['incrment_counter'=> $defaultCounter,'date'=>$defaultDate]]);
            $this->connection->insert('core_config_data', [
                    'scope'=>'stores',
                    'scope_id'=>$storeId,
                    'path'=>'ced_customnumbers/'.$entityType.'/'.$field,
                    'value'=> $value
                ]
            );
        return ['incrment_counter'=> $defaultCounter,'date'=> $defaultDate];
        }
    }
}
