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
namespace Ced\Customnumbers\Model\Plugin;

use Magento\SalesSequence\Model\Sequence;
use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\Framework\DB\Sequence\SequenceInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\SalesSequence\Model\ResourceModel\Meta as Meta;
/**
* 
*/
class SequencePlugin
{
    protected $timezone;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    protected $readConnection;
    /**
     * @var \Magento\Sales\Model\OrderRepository
     */

    protected $orderRepository;

    /**
     * @var \Magento\Backend\Model\Session\Quote $quoteSession
     */

    protected $quoteSession;
    
    /**
     * @var \Magento\Framework\App\Request\Http
     */

     protected $httpRequest;

     /**
     * @var \Magento\Framework\App\ResourceConnection
     */

     protected $resource;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */

    protected $objectManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */

    protected $coreDate;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */

    protected $storeManager;

    /**
     * @var \Ced\Customnumbers\Helper\Data
     */

    protected $helper;

    /**
     * @param Magento\SalesSequence\Model\ResourceModel\Meta
     * @param Magento\Framework\App\ResourceConnection
     * @param Magento\Sales\Model\Order
     * @param Magento\Backend\Model\Session\Quote
     * @param Magento\Store\Model\StoreManagerInterface
     * @param Magento\Framework\ObjectManagerInterface
     * @param Magento\Framework\Stdlib\DateTime\DateTime
     * @param Ced\Customnumbers\Helper\Data
     */

    public function __construct(
            Meta $meta,
            AppResource $appResource,
            \Magento\Sales\Model\Order $orderRepository,
            \Magento\Backend\Model\Session\Quote $quoteSession,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Framework\ObjectManagerInterface $objectManager,
            \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
            \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
            \Ced\Customnumbers\Helper\Data $helper
        )
    {
        $this->orderRepository = $orderRepository;
        $this->quoteSession = $quoteSession;
        $this->storeManager = $storeManager;
        $this->meta = $meta;
        $this->objectManager = $objectManager;
        $this->httpRequest = $this->objectManager->get('Magento\Framework\App\Request\Http');
        $this->resource = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
        $this->connection = $this->resource->getConnection();
        $this->coreDate = $coreDate;
        $this->helper = $helper;
        $this->timezone = $timezone;
    }

    /**
     *   get increment ID for orders/invoices/shipment/credit memos
     *
     * @param Sequence $subject
     * @param \Closure $proceed
     * @return mixed
     */

    public function aroundGetNextValue(Sequence $subject, \Closure $proceed)
    {
        
        $entityType = $subject->entityType;

        if($entityType == 'invoice' || $entityType == 'shipment'|| $entityType == 'creditmemo'){
            $storeId = $this->orderRepository->load($this->httpRequest->getParam('order_id'))
                    ->getStoreId();      
        } else {
            $storeId = $this->quoteSession->getQuote()->getStoreId();
        }

        if($storeId == null){
            $storeId = 0;
        }
        if(!$this->helper->enableModule($storeId)){
            $returnValue = $proceed();
            return $returnValue;
        }
        
        try {
                $customId = $this->handleNewIncrementId($entityType, $storeId,$proceed);

                if($customId){
                    $this->incrmentIdExisting($customId,$entityType,$proceed,$storeId);
                }
                if(empty($customId )|| !$customId){
                    $returnValue = $proceed();
                    return $returnValue;
                }
                return $customId;

            } catch (\Exception $e) {
                echo $e->getMessage();
                $returnValue = $proceed();
                return $returnValue;
            }
    }

    /**
     *   when increment id exists
     *
     * @param $entityType
     * @param $customId
     * @param $storeId
     * @param $proceed
     * @return incremented value
     */

    protected function incrmentIdExisting($customId,$entityType,$proceed,$storeId)
    {
        $readConnection = $this->resource->getConnection('core_read');
        $tableName = $this->resource->getTableName('sales_'.$entityType);
        $rawQuery = sprintf( "SELECT entity_id FROM %s WHERE increment_id = '%s' AND store_id = '%s'",
                    $tableName,$customId,$storeId 
                );
        $row = $readConnection->rawQuery($rawQuery);
        return $this->handleNewIncrementId($entityType,$storeId,$proceed);
    }
    /**
     *   get increment ID for orders/invoices/shipment/credit memos
     *
     * @param $entityType
     * @param $storeId
     * @return incremented value
     */

    protected function handleNewIncrementId($entityType,$storeId,$proceed)
    {
        $replacement = [];
        $newFormatedValue ='';
        $prefix ='';

        $activate = $this->helper->getConfigValue($entityType, 'activation', $storeId);

        //when module is enabled

        if($activate){

            $defaultCounter = $this->helper->getConfigValue($entityType, 'default', $storeId);
            $prefix = $this->helper->getConfigValue($entityType, 'prefix', $storeId);

            //when default counter is used then get the last inserted id
            if($defaultCounter){
                $proceed_default = $proceed();

            }

            $readConnection = $this->resource->getConnection('core_read');
            $tablename = $this->resource->getTableName('sequence_'.$entityType.'_'.$storeId);
            $query = 'SELECT MAX(sequence_value) FROM ' . $tablename;
            $defaultValue = $readConnection->fetchOne($query);

            $padChar = 0;
            $padLength = intval($this->helper->getConfigValue($entityType, 'padding', $storeId));
            $counter = intval($this->helper->getConfigValue($entityType, 'counter', $storeId));
            $increment = intval($this->helper->getConfigValue($entityType, 'increment', $storeId));
            $reset = $this->helper->getConfigValue($entityType, 'reset', $storeId);
            $currentDate = $this->timezone->date()->format('Y-m-d'); //get cusrrent date
            $sameAsOrder = $this->helper->getConfigValue($entityType, 'same_value_order', $storeId);
            $format = $this->helper->getConfigValue($entityType, 'format', $storeId);
            if($format){
                $replaceDateValue = $this->replaceDateValue($replacement);
                $replaceRandValue = $this->replaceRandValue($replacement,$format);
                $replaceStoreValue = $this->replaceStoreValue($replacement,$storeId);
                $replaceResult = array_merge($replaceDateValue,$replaceRandValue,$replaceStoreValue);
                $newFormatedValue = str_replace(array_keys($replaceResult),
                                    array_values($replaceResult),
                                    $format
                                );
            }
            //incrment value 
            
            if($increment < 0 || $increment =='') {
                $increment = 1;
            }

            // when the default magento increment is used then return default increment id 

            if($defaultCounter){

                $newIncrementValue = $defaultValue;
                return str_pad((string)$newIncrementValue, 8, 0, STR_PAD_LEFT); 

            } else {
                    //when entity type is invoice,shipment,creditmemo

                    if($entityType == 'invoice' || $entityType == 'shipment'|| $entityType == 'creditmemo'){  

                    //when invoice,shipment and credit memo has same order settings then we just use order id
                        if($sameAsOrder){
                            $prefix .= $this->orderRepository->load($this->httpRequest->getParam('order_id'))
                                    ->getIncrementId();
                            return $prefix;
                        }
                    }
                    // when increment counter is used for separtae setting

                    $lastFormatDetails = $this->getIncrementCounterValue($reset,$entityType,$storeId,$format,$currentDate,$counter,$increment);

                    if(is_array($lastFormatDetails)){
                        $incrementCounter = $lastFormatDetails['incrment_counter'];
                     }else{         // when increment counter is used for others reset settings
                        $incrementCounter = $lastFormatDetails;
                     }

                    if($reset == 'separate'){
                        $lastResetDate = $lastFormatDetails['date'];
                    } else{
                        $lastResetDate = $this->helper->getCoreConfigValue($entityType,'last_reset_date',
                                    $storeId,$this->timezone->date()->format('Y-m-d'));
                    }
                
                    // when reset setting is given as daily,monthly,yealry
                    $flag = true;

                    if($reset!=''){
                        if($reset =='y-m-d'){
                            if(!$this->compareDay($lastResetDate)) {
                                $newIncrement = $counter;
                                $flag = false;
                            }
                        }elseif ($reset =='y-m') {
                           if(!$this->compareMonth($lastResetDate)) {
                                $newIncrement = $counter;
                                $flag = false;
                            }
                        }elseif ($reset =='y') {
                            if(!$this->compareYear($lastResetDate)) {
                                $newIncrement = $counter;
                                $flag = false;
                            }
                        }
                    }
                    if($flag) {
                        $newIncrement = $increment + $incrementCounter;
                    }
                
                    // update the last reset date

                    $path = 'ced_customnumbers/'.$entityType.'/'.'last_reset_date';
                    $where = $this->connection->quoteInto('path = ?',$path);
                    $where .= $this->connection->quoteInto(" AND scope_id = ? ", $storeId);
                    try {
                            $this->connection->update($this->resource->getTableName('core_config_data'),[
                                    'value'=>$this->timezone->date()->format('Y-m-d')
                                ],$where);
                    } catch(\Exception $e) {
                        echo $e->getMessage();
                    }
                    //update the increment counter
                    
                    if($reset == 'separate'){
                       $savedFormat = unserialize($this->helper->getCoreConfigValue($entityType,'last_saved_format',$storeId,0));
                        $savedFormat[$format] = ['incrment_counter'=> $newIncrement,'date'=>$this->timezone->date()->format('Y-m-d')];
                        $path = 'ced_customnumbers/'.$entityType.'/'.'last_saved_format';
                        $value = serialize($savedFormat);

                    }else{
                        $path = 'ced_customnumbers/'.$entityType.'/'.'increment_counter';
                        $value = $newIncrement;
                    }
                    $where = $this->connection->quoteInto('path = ?',$path);
                    $where .= $this->connection->quoteInto(" AND scope_id = ? ", $storeId);
                
                    // update the increment value
                    try {
                            $this->connection->update($this->resource->getTableName('core_config_data'),[
                                        'value'=>$value
                                    ],$where);
                        } catch(\Exception $e) {
                            echo $e->getMessage();
                        }
            } //else close whend default counter is no 

            // append the pad length

            if($padLength > 0){
                $newIncrement = str_pad((string)$newIncrement, $padLength, $padChar, STR_PAD_LEFT);
            }

            $newIncrementValue = $prefix.$newFormatedValue.$newIncrement; 
            return $newIncrementValue;
        }
    }

    /**
     * get incremented counter value from core-config-data table for orders/invoices/shipment/credit memos
     *
     * @param $reset
     * @param $entityType
     * @param $storeId
     * @param $format
     * @param $currentDate
     * @return array in case of separte counter else return string 
     */

    protected function getIncrementCounterValue($reset,$entityType,$storeId,$format,$currentDate,$counter,$increment)
    {
        if($reset == 'separate'){
            if($counter){
                $defaultCounter = $counter-$increment;
                if($defaultCounter < 0 || $defaultCounter==''){
                    $defaultCounter = 1;
                }
            } else {
                $defaultCounter = 0;
            }
            
            return $this->helper->getSeparateCounterValue($entityType,'last_saved_format',$storeId,$format,$defaultCounter,$currentDate);
        } else {
            return $this->helper->getCoreConfigValue($entityType,'increment_counter',$storeId,0); 
        }
    }

    /**
     *
     * @param $replacement as an array
     * @return array for data/time used in format settings 
     */

    protected function replaceDateValue($replacement)
    {

        $replacement["{{var y}}"] = $this->timezone->date()->format('y'); //A two digit representation of a year
        $replacement["{{var Y}}"] = $this->timezone->date()->format('Y'); //A four digit representation of a year
        $replacement["{{var d}}"] = $this->timezone->date()->format('d'); //The day of the month (from 01 to 31)
        $replacement["{{var D}}"] = $this->timezone->date()->format('D'); //A textual representation of a day (three letters)
        $replacement["{{var n}}"] = sprintf("%02d", $this->timezone->date()->format('n')); //A numeric representation of a month, with leading zeros (01 to 12)
        $replacement["{{var a}}"] = $this->timezone->date()->format('a'); //Lowercase am or pm
        $replacement["{{var A}}"] = $this->timezone->date()->format('A'); //Uppercase AM or PM
        $replacement["{{var h}}"] = $this->timezone->date()->format('h'); //12-hour format of an hour (01 to 12)
        // $replacement["{{var H}}"] = sprintf("%02d", $this->timezone->date()->format('G')); //24-hour format of an hour (00 to 23)
        $replacement["{{var H}}"] = sprintf("%02d", $this->timezone->date()->format('G'));
        // $replacement["{{var H}}"] = $this->timezone->date()->format('H'); //24-hour format of an hour (00 to 23)
        $replacement["{{var i}}"] = $this->timezone->date()->format('i'); //Minutes with leading zeros (00 to 59)
        $replacement["{{var s}}"] = $this->coreDate->gmtDate('s'); //Seconds, with leading zeros (00 to 59)
        $replacement["{{var l}}"] = $this->timezone->date()->format('l'); //A full textual representation of a day Sunday
        $replacement["{{var F}}"] = $this->timezone->date()->format('F'); //A full textual representation of a month, such as January
        return $replacement;

    }

    /**
     *
     * @param $replacement as an array
     * @return array for random numbers used in format
     */

    protected function replaceRandValue($replacement,$format)
    {
        $random = $this->getRandomFormat('rand',$format,'setRandomNumber');
        $alphanumeric = $this->getRandomFormat('alphanum',$format,'setAlphaNumericRand');
        
        return array_merge($random,$alphanumeric,$replacement);
    }

    protected function getRandomFormat($find,$format,$callback)
    {
        $start_index = 0;
        $randomArray = [];
            do {

                $randomFlag = false;
                $rand_index = strpos($format,$find,$start_index);
                if($rand_index !== false) {
                    
                    $randomFlag = true;
                    $start = strpos($format," ",$rand_index)+1;

                    $length = strpos($format,"}}",$rand_index)-$start;
                    $rand_length = substr($format, $start, $length);
                    $randomArray['{{'.$find.' '.$rand_length.'}}'] = $this->$callback($rand_length);

                    $start_index = $start;
                }

            } while ($randomFlag);
    return $randomArray;
    }

    public function setRandomNumber($length)
    {
        return rand(pow(10, $length-1), pow(10, $length)-1);
    }


    public function setAlphaNumericRand($alpanum)
    {
        $string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $storeRandom = '';
        $userDefineLen = $alpanum;
        $length = strlen($string);
        for($i = 0;$i < $userDefineLen;$i++)
        {
            $generateRandomValue = rand(0,$length-1);
            $storeRandom.= $string{$generateRandomValue};
        }
        return $storeRandom;
    }
    /**
     *
     * @param $replacement as an array
     * @return array for store settings used in format
     */

    protected function replaceStoreValue($replacement,$storeId)
    {
        $current = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $base = $this->storeManager->getStore()->getBaseCurrencyCode();
        $default = $this->storeManager->getStore()->getDefaultCurrencyCode();

        $replacement["{{id}}"] = $storeId;
        $replacement["{{curr}}"] = $current;
        $replacement["{{base}}"] = $base;
        $replacement["{{def}}"] = $default;

        return $replacement;
    }

    /**
     *
     * @param $lastResetDate
     * @return boolean
     */

    public function compareDay($lastResetDate)
    {
        $inputDate = $lastResetDate;

        $current = $this->timezone->date()->format('Y-m-d');
        if (date('Y-m-d', strtotime($inputDate)) === date('Y-m-d', strtotime($current))){
            return true;
        }else{
            return false;
        }
    }

    /**
     *
     * @param $lastResetDate
     * @return boolean
     */

    public function compareMonth($lastResetDate)
    {
        $inputDate = $lastResetDate;
        $current = $this->timezone->date()->format('Y-m-d');
        if (date('Y-m', strtotime($inputDate)) === date('Y-m', strtotime($current))) {
            return true;
        }else{
            return false;
        }
    }

    /**
     *
     * @param $lastResetDate
     * @return boolean
     */

    public function compareYear($lastResetDate)
    {
        $inputDate = $lastResetDate;
        $current = $this->timezone->date()->format('Y-m-d');
        if (date('Y', strtotime($lastResetDate)) === date('Y', strtotime($current))){
            return true;
        }else{
            return false;
        }
    }
}
