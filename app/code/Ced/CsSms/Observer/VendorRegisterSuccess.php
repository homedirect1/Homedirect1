<?php


namespace Ced\CsSms\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class VendorRegisterSuccess implements ObserverInterface
{
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
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if($this->getHelper()->isSectionEnabled('vendor_registration/enable'))
        {

            if($this->_request->getPost('is_vendor')== 1)
            {
                $data = $this->_request->getPost();
                $telephone = $this->_request->getPost('telephone');
                if (empty($telephone)){
                    $telephone = $data['vendor']['mobile'];
                }

                $country_id = $data['vendor']['country_id'];
                $available_country_codes = $this->getHelper()->getCountryCodes();
                if(!empty($available_country_codes)  && isset($country_id)) {
                    if(isset($available_country_codes[$country_id]))
                        $telephone = $available_country_codes[$country_id].$telephone;
                }
               
                try{
                        $smsto = $telephone;
                        $smsmsg = $this->getHelper()->getVendorRegistrationMsg($data);
                        $code = $this->getVendorRegistrationVariables($data);
                        $this->getHelper()->sendSms($smsto, $code,$smsmsg,'vendor_registration');

                }catch (\Magento\Framework\Exception $e){
                    $this->messageManager->addError(__('Something went wrong '.$e->getMessage()));
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

    public function getHelper()
    {
        return $this->helper;
    }

    public function getVendorRegistrationVariables($data){
        $codes = [
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password' => $data['password_confirmation'],
            'publicname' => $data['vendor']['public_name'],
            'shopurl' => $data['vendor']['shop_url'],
            'name' => $data['firstname'].' '.$data['lastname']

        ];
        return $codes;

    }
}