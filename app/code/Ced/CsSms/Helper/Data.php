<?php


namespace Ced\CsSms\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_PATH = 'sms_notification/';
    const XML_PATH_CARRIERS_ROOT = 'sms_gateway';
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfigManager;
    /**
     * @var \Magento\Framework\App\Config\ValueInterface
     */
    protected $_configValueManager;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    protected $_storeId = 0;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->messageManager = $messageManager;
        $this->_objectManager = $objectManager;
        $this->_logger = $logger;
        parent::__construct($context);
        $this->_storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $this->_scopeConfigManager = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->_configValueManager = $this->_objectManager->get('Magento\Framework\App\Config\ValueInterface');
    }

    /**
     * Set a specified store ID value
     *
     * @param int $store
     * @return $this
     */
    public function setStoreId($store){
        $this->_storeId = $store;
        return $this;
    }

    /**
     * @return store
     */
    public function getStore() {
        if ($this->_storeId) $storeId = (int)$this->_storeId;
        else $storeId =  isset($_REQUEST['store'])?(int) $_REQUEST['store']:null;
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * @return status of extension
     */
    public function isExtensionEnabled()
    {
        return $this->_scopeConfigManager->getValue(self::CONFIG_PATH.'enter/enable',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
    }

    /**
     * @param $path
     * @return bool|mixed
     * getting section enable for pariticular store
     */
    public function isSectionEnabled($path){
        if($this->isExtensionEnabled())
            return $this->_scopeConfigManager->getValue(self::CONFIG_PATH.$path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
        else
            return false;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return customer phone number
     */
    public function getTelephoneFromOrder(\Magento\Sales\Model\Order $order)
    {
        $billingAddress = $order->getBillingAddress();
        $number = $billingAddress->getTelephone();

        $country_id = $billingAddress->getCountryId();
        $available_country_codes = $this->getCountryCodes();
        if(!empty($available_country_codes) && isset($available_country_codes[$country_id])) {
            $number = $available_country_codes[$country_id].$number;
        }

        return $number;
    }

    /**
     * @return admin telephone number
     */
    public function getAdminTelephone()
    {
        return $this->_scopeConfigManager->getValue(self::CONFIG_PATH.'orders/receiver',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
    }

    /**
     * @return status CsSms Integration
     */
    public function isOrderStatusNotificationEnabled()
    {
        if($this->isExtensionEnabled())
            return $this->_scopeConfigManager->getValue(self::CONFIG_PATH.'order_status/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
        else
            return false;
    }
    
    /**
     * @return mixed
     */
    public function vendorAddressFields()
    {
        if ($this->isExtensionEnabled())
            return $this->_scopeConfigManager->getValue(self::CONFIG_PATH . 'vendor_registration/enable',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
        else
            return $this->isExtensionEnabled();
    }

    /**
     * @return admin order status
     */
    public function getAdminOrderStatusTelephone()
    {
        if($this->isExtensionEnabled())
            return $this->_scopeConfigManager->getValue(self::CONFIG_PATH.'order_status/receiver',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
        else
            return false;
    }

    /**
     * @return notification at customer registration
     */
    public function getAdminCustomerRegisterationTelephone()
    {
        if($this->isExtensionEnabled())
            return $this->_scopeConfigManager->getValue(self::CONFIG_PATH.'customer_registration/receiver',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
        else
            return false;
    }

    /**
     * @return notification at vendor registration
     */
    public function getAdminVendorRegisterationTelephone()
    {
        if($this->isExtensionEnabled())
            return $this->_scopeConfigManager->getValue(self::CONFIG_PATH.'vendor_registration/receiver',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
        else
            return false;
    }

    /**
     * @return notification at new order placed
     */
    public function getMessage(\Magento\Sales\Model\Order $order)
    {
        $billingAddress = $order->getBillingAddress();
        $codes = array('{{firstname}}','{{middlename}}','{{lastname}}','{{fax}}','{{postal}}','{{city}}','{{email}}','{{order_id}}','{{name}}');
        $accurate = array($billingAddress->getFirstname(),
            $billingAddress->getMiddlename(),
            $billingAddress->getLastname(),
            $billingAddress->getFax(),
            $billingAddress->getPostcode(),
            $billingAddress->getCity(),
            $billingAddress->getEmail(),
            $order->getIncrementId(),
            $billingAddress->getFirstname().' '.$billingAddress->getLastname()
        );

        return str_replace($codes,$accurate,$this->_scopeConfigManager->getValue(self::CONFIG_PATH.'orders/message',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId()));
    }

    /**
     * @param $product
     * @param $vendor
     * @param $orderId
     * @return notification when new product is created
     */
    public function newVendorOrderMsg($product, $vendor, $orderId)
    {
        $codes = array('{{name}}','{{email}}', '{{productname}}','{{sku}}','{{order_id}}');
        $accurate = array($vendor->getName(),
            $vendor->getEmail(),
            $product->getName(),
            $product->getSku(),
            $orderId
        );

        return str_replace($codes,$accurate,$this->_scopeConfigManager->getValue(self::CONFIG_PATH.'vendor_order/message',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId()));
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return notification at order status changed
     */
    public function getOrderStatusChangeMsg(\Magento\Sales\Model\Order $order)
    {
        $status = $this->getStatusName($order->getState());
        $billingAddress = $order->getBillingAddress();
        $codes = array('{{firstname}}','{{middlename}}','{{lastname}}','{{fax}}','{{postal}}','{{city}}','{{email}}','{{order_id}}','{{status}}','{{name}}');
        $accurate = array($billingAddress->getFirstname(),
            $billingAddress->getMiddlename(),
            $billingAddress->getLastname(),
            $billingAddress->getFax(),
            $billingAddress->getPostcode(),
            $billingAddress->getCity(),
            $billingAddress->getEmail(),
            $order->getIncrementId(),
            $status,
            $billingAddress->getFirstname().' '.$billingAddress->getLastname()
        );

        return str_replace($codes,$accurate,$this->_scopeConfigManager->getValue(self::CONFIG_PATH.'order_status/message',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId()));
    }

    /**
     * @param $stateCode
     * @return notifications for product status
     */
    public function getStatusName($stateCode)
    {
        $statuses = $statuses = $this->_objectManager->get('\Magento\Sales\Model\ResourceModel\Order\Status\Collection')
            ->addStateFilter($stateCode)
            ->toOptionHash();
        if(is_array($statuses))
            return $statuses[$stateCode];
        return false;
    }

    /**
     * @param $customer
     * @param $otp
     * @return notification at customer registration
     */
    public function getCustomerRegistrationMsg($customer, $otp)
    {
        $codes = array('{{firstname}}','{{middlename}}','{{lastname}}','{{email}}','{{password}}','{name}}');
        $accurate = array($customer->getFirstname(),
            $customer->getMiddlename(),
            $customer->getLastname(),
            $customer->getEmail(),
            $otp,
            $customer->getFirstname().' '.$customer->getLastname()

        );
        return str_replace($codes,$accurate,$this->_scopeConfigManager->getValue(self::CONFIG_PATH.'customer_registration/message',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId()));
    }

    /**
     * @param $data
     * @param $customer
     * @return return notfication when vendor registered
     */
    public function getVendorRegistrationMsg($data)
    {
        $codes = array('{{firstname}}','{{lastname}}','{{email}}','{{password}}','{{publicname}}','{{shopurl}}','{{name}}');
        $accurate = array($data['firstname'],
            $data['lastname'],
            $data['email'],
            $data['password_confirmation'],
            $data['vendor']['public_name'],
            $data['vendor']['shop_url'],
            $data['firstname'].' '.$data['lastname']
        );

        return str_replace($codes,$accurate,$this->_scopeConfigManager->getValue(self::CONFIG_PATH.'vendor_registration/message',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId()));
    }

    /**
     * @param $vendor
     * @param $status
     * @return vendor status notification
     */
    public function getVendorStatusMsg($vendor, $status)
    {
        $codes = array('{{name}}','{{email}}','{{status}}');
        $accurate = array($vendor->getName(),
            $vendor->getEmail(),
            $status
        );

        return str_replace($codes,$accurate,$this->_scopeConfigManager->getValue(self::CONFIG_PATH.'vendor_status/message',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId()));
    }

    /**
     * @param $vendor
     * @param $product
     * @return notifiaction when new vendor product is created
     */
    public function getVendorNewProductMgs($vendor, $product)
    {
        $codes = array('{{name}}','{{email}}', '{{productname}}','{{sku}}');
        $accurate = array($vendor->getName(),
            $vendor->getEmail(),
            $product->getName(),
            $product->getSku()
        );

        return str_replace($codes,$accurate,$this->_scopeConfigManager->getValue(self::CONFIG_PATH.'vendor_new_product/message',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId()));
    }

    /**
     * @param $vendor
     * @param $product
     * @param $checkStatus
     * @return notifications when vendor product status is change
     */
    public function getVendorProductStatusMsg($vendor, $product, $checkStatus)
    {
        $status='';
        if($checkStatus == '0')
            $status = 'Not Approved';
        elseif ($checkStatus == '1')
            $status = 'Approved';
        elseif ($checkStatus == '2')
            $status = 'Pending';
        elseif ($checkStatus == '3')
            $status = 'Delete';

        $codes = array('{{name}}','{{email}}', '{{productname}}','{{sku}}','{{status}}');
        $accurate = array($vendor->getName(),
            $vendor->getEmail(),
            $product->getName(),
            $product->getSku(),
            $status
        );

        return str_replace($codes,$accurate,$this->_scopeConfigManager->getValue(self::CONFIG_PATH.'vendor_product_status/message',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId()));
    }

    /**
     * @param $payment
     * @param $vendor
     * @return notifications for vendorPayment
     */
    public function newVendorPaymentMsg($payment, $vendor)
    {
        $orderIds = '';
        $transaction = $payment->getTransactionType();
        $transaction_type = '';

        if($transaction == '0')
            $transaction_type = 'Credit';
        elseif ($transaction == '1')
            $transaction_type = 'Debit';

        $amount_desc = json_decode($payment->getAmountDesc());
        foreach ($amount_desc as $key => $value) {
            $orderIds .= $key.', ';
        }
        $codes = array('{{name}}','{{transactionid}}','{{amount}}','{{orderids}}','{{paymentcode}}','{{transactiontype}}');
        $accurate = array($vendor->getName(),
            $payment->getTransactionId(),
            $payment->getBaseAmount(),
            $orderIds,
            $payment->getPaymentCode(),
            $transaction_type
        );
        return str_replace($codes,$accurate,$this->_scopeConfigManager->getValue(self::CONFIG_PATH.'vendor_payment/message',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId()));
    }

    /**
     * @return available sms delivery options
     */
    public function getSmsDeliveryOption(){
        $key = 0;
        $carriersCodes = [];
        foreach ($this->_scopeConfigManager->getValue(
            self::XML_PATH_CARRIERS_ROOT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE) as $carrierCode => $carrier) {
            $smsEnbale = 'sms_gateway/'.$carrierCode.'/enable';
            $configMessaging = 'sms_gateway/'.$carrierCode.'/messaging_priority';
            $configSmsType = 'sms_gateway/'.$carrierCode.'/sms_type';
            $configSmsModel = 'sms_gateway/'.$carrierCode.'/model';
            $priority = $this->_scopeConfigManager->getValue($configMessaging,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $smsType = $this->_scopeConfigManager->getValue($configSmsType,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $smsModel = $this->_scopeConfigManager->getValue($configSmsModel,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $smsEnable = $this->_scopeConfigManager->getValue($smsEnbale,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($smsEnable == 1){
                $carriersCodes[$key]['priority'] = $priority;
                $carriersCodes[$key]['smstype'] = $smsType;
                $carriersCodes[$key]['smsmodel'] = $smsModel;
                $key++;
            }
        }
        return $carriersCodes;
    }

    /**
     * @return country code listed on backend
     */
    public function getCountryCodes() {
        $country_codes = $this->_scopeConfigManager->getValue('sms_notification/enter/country_codes',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
        $country_codes = json_decode($country_codes, true);
        $options = array();
        if(count($country_codes)>0) {
            foreach ($country_codes as $key => $value) {
                if($key=='__empty')continue;
                if($value['country']) {
                    $options[$value['country']] = $value['code'];
                }
            }
        }
        return ($options);
    }

    /**
     * @param $vendorId
     * @return getting vendor contact number in-case of unavailabilty of phone number
     */
    public function getCountryNumber($vendorId){
        $_vendor = $this->_objectManager->create('\Ced\CsMarketplace\Model\Vendor')->load($vendorId);
        $countryId = $_vendor->getCountryId();
        $contactNumber = $_vendor->getContactNumber();
        $countryDetail = json_decode($this->_scopeConfigManager->getValue(self::CONFIG_PATH.'enter/country_codes',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE),true);
        foreach ($countryDetail as $detail){
            if($detail['country'] == $countryId){
                $contactNumber = trim(str_replace(' ','',$contactNumber));
                $contactNumber = trim(str_replace($detail['code'],'',$contactNumber));
                $contactNumber = $detail['code'].''.$contactNumber;
            }
        }
        return $contactNumber;
    }

    /**
     * @param $configpath
     * @return bool|mixed
     */
    public function isSmsSetting($configpath){
        if($this->isExtensionEnabled())
            return $this->_scopeConfigManager->getValue($configpath,\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
        else
            return false;
    }

    /**
     * @return getting messaging extensions status
     */
    public function isSmsExtensionEnableFound(){
        $carriersCodes = [];
        foreach ($this->scopeConfig->getValue(
            self::XML_PATH_CARRIERS_ROOT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE) as $carrierCode => $carrier) {
                $configpath = 'sms_gateway/'.$carrierCode.'/enable';
                $status = $this->_scopeConfigManager->getValue($configpath,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                if($status == 1){
                    $carriersCodes[] = $carrierCode;
                }
        }
        return count($carriersCodes);
    }

    /**
     * @param $to
     * @param deliver sms
     */
    public function sendSms($to, $code, $msg_body, $event) {
        $carriersCodes = $this->getSmsDeliveryOption();
        $count = count($carriersCodes);
        if ($count >= 2){
            usort($carriersCodes, function($a, $b) {
                return $a['priority'] - $b['priority'];
            });
        }
        foreach ($carriersCodes as $data){
            $this->_objectManager->get($data['smsmodel'])->deliverySms($to,$code,$msg_body);
            $response = $this->_objectManager->get($data['smsmodel'])->_get('property');
            if (!empty($response)){
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sendsms.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($response);
                //$this->_logger->info($response);
            }
            else{
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sendsms.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($response);
                break;
            }
        }
    }

}
