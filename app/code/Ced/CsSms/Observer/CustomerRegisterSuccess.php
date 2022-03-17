<?php

namespace Ced\CsSms\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerRegisterSuccess implements ObserverInterface
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Ced\CsSms\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * CustomerRegisterSuccess constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Ced\CsSms\Helper\Data $helper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\RequestInterface $request,
        \Ced\CsSms\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->_request = $request;
        $this->_objectManager = $objectManager;
        $this->helper = $helper;
        $this->messageManager = $messageManager;
    }


    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

    
        if($this->getHelper()->isSmsSetting('sms_notification/enter/enable')) {
         
            if($this->getHelper()->isSmsExtensionEnableFound() > 0) {
               
                if($this->getHelper()->isSectionEnabled('customer_registration/enable'))
                {
                   
                    if($customer = $observer->getEvent()->getCustomer())
                    {
                        
                        $telephone = $this->_request->getPost('telephone');
                        if (empty($telephone)){
                            $telephone = $this->_request->getPost('mobile');
                        }
                       
                        $pass = $this->_request->getPost('password_confirmation');
                        $country_id = $this->_request->getPost('country_id');
                        $available_country_codes = $this->getHelper()->getCountryCodes();
                        if(!empty($available_country_codes) && isset($country_id)) {
                            if(isset($available_country_codes[$country_id]))
                                $telephone = $available_country_codes[$country_id].$telephone;
                        }
                        try {
                            $is_vendor = $this->_request->getParam('is_vendor');
                           
                            if ($customer && $customer->getId() && $telephone!="" && !isset($is_vendor)) {
                                $smsto = $telephone;
                                $msg = $this->getHelper()->getCustomerRegistrationMsg($customer, $pass);
                                $smsmsg = str_replace('{', '', $msg);
                                $code = $this->getCustomerVariables($customer, $pass);
                                $this->getHelper()->sendSms($smsto, $code,$smsmsg,"customer_registration");
                            }
                        } catch (\Magento\Framework\Exception $e) {
                            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sendsms.log');
                            $logger = new \Zend\Log\Logger();
                            $logger->addWriter($writer);
                            $logger->info($e->getMessage());
                        }
                    }
                }
              
                /*codes for admin messaging*/
                if(($this->getHelper()->isSectionEnabled('customer_registration/admin_notify')) &&
                    ($this->getHelper()->getAdminCustomerRegisterationTelephone())  && (!isset($is_vendor))) {
                    $smsto = $this->getHelper()->getAdminCustomerRegisterationTelephone();
                    $smsmsg = __('New customer has been registered in your store');
                    $this->getHelper()->sendSms($smsto,[],$smsmsg,'admin_notify');
                }
               
                $this->vendorRegisterSuccess($observer);
            }
        }
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function vendorRegisterSuccess(\Magento\Framework\Event\Observer $observer)
    {

         
        
        if($this->getHelper()->isSectionEnabled('vendor_registration/enable'))
        {

            
            if($this->_request->getPost('is_vendor')== 1)
            {
                 
                $telephone = $this->_request->getPost('telephone');
               
                if (empty($telephone)){
                    $telephone = $this->_request->getPost('mobile');
                   
                }
                $country_id = $this->_request->getPost('country_id');
               
                $available_country_codes = $this->getHelper()->getCountryCodes();
                if(!empty($available_country_codes)  && isset($country_id)) {
                    if(isset($available_country_codes[$country_id]))
                        $telephone = $available_country_codes[$country_id].$telephone;
                }
                $customerData = $observer->getCustomer();
                try{
                    if($customerData && $customerData->getId()  && $telephone!="" ) {
                     
                        $smsto = $telephone;
                        $smsmsg = $this->getHelper()->getVendorRegistrationMsg($_POST, $customerData);
                        $code = $this->getVendorRegistrationVariables($_POST,$customerData);
                        $this->getHelper()->sendSms($smsto, $code,$smsmsg,'vendor_registration');
                    }
                }catch (\Magento\Framework\Exception $e){
                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sendsms.log');
                    $logger = new \Zend\Log\Logger();
                    $logger->addWriter($writer);
                    $logger->info($e->getMessage());
                }
            }
        }
        /*codes for admin messaging*/
        /*codes for admin on vendor register*/
        if(($this->getHelper()->isSectionEnabled('vendor_registration/notify')) and ($this->_request->getPost('is_vendor')== 1))
        {
            $smsto = $this->getHelper()->getAdminVendorRegisterationTelephone();
            $smsmsg = __('New vendor has been registered in your store.');
            $this->getHelper()->sendSms($smsto, [],$smsmsg,'vendor_registration_notify_admin');
        }
    }

    /**
     * @return \Ced\CsSms\Helper\Data Instance
     */
    public function getHelper()
    {
        return $this->helper;
    }

    public  function getCustomerVariables($customer,$otp){
        $codes = [
             'firstname' => $customer->getFirstname(),
             'middlename' => $customer->getMiddlename(),
             'lastname' => $customer->getLastname(),
             'email' => $customer->getEmail(),
              'otp' => $otp,
             'name' => $customer->getFirstname().' '.$customer->getLastname(),
        ];
        return $codes;
    }

    public function getVendorRegistrationVariables($data, $customer){
        $codes = [
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'email' => $customer->getEmail(),
            'password' => $data['password_confirmation'],
            'publicname' => $data['vendor']['public_name'],
            'shopurl' => $data['vendor']['shop_url']

        ];
        return $codes;

    }

}
