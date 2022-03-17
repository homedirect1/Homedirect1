<?php

namespace Knowband\Mobileappbuilder\Controller\Index;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
class Api extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
//class Api extends \Magento\Framework\App\Action\Action
{
    protected $sp_resultRawFactory;
    protected $sp_request;
    protected $sp_helper;
    protected $sp_scopeConfig;
    protected $inlineTranslation;
    protected $sp_transportBuilder;
    protected $sp_emailFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Knowband\Mobileappbuilder\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultRawFactory,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\View\LayoutFactory $viewLayoutFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
        parent::__construct($context);
        $this->sp_request = $request;
        $this->sp_scopeConfig = $scopeConfig;
        $this->sp_storeManager = $storeManager;
        $this->sp_helper = $helper;
        $this->sp_resultRawFactory = $resultRawFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->sp_transportBuilder = $transportBuilder;
        $this->_viewLayoutFactory = $viewLayoutFactory;
        $this->_eventManager = $context->getEventManager();
    }

    public function execute() {

        $configData = $this->sp_helper->getSavedSettings('knowband/mobileappbuilder/settings');
        if (isset($configData['general_settings']['enable']) && $configData['general_settings']['enable'] == '1') {
            $api_verion = $this->getRequest()->getParam('version', '1.7');
            $isRequestdApiAvailable = $this->checkRequestedApi($api_verion);
            if (!$isRequestdApiAvailable) {
                $response = $this->setRequestedApiNotAvailableResponse();
                $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                $resultJson->setData($response);
                return $resultJson;
            }else{
                $helper_file_name = $isRequestdApiAvailable;
            }
        } else {
            $response = $this->setIntallModuleResponse();
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($response);
            return $resultJson;
        }
        $method = (string) $this->getRequest()->getParam('method');
        
        $currency = (string) $this->getRequest()->getParam('id_currency');
        if ($currency) {
            $this->sp_storeManager->getStore()->setCurrentCurrencyCode($currency);
        }
        $email = (string) $this->getRequest()->getPost('email');
        if ($email && $method != 'appLogin' && $method != 'appSocialLogin' && $method != 'createSocialCustomer' && $method != 'appRegisterUser' && $method != 'appLogout'  && $method != 'appGuestRegistration') {
            $helper = $this->_objectManager->get("\Knowband\Mobileappbuilder\Helper\\".$helper_file_name);
            $response = $helper->forceLogin($email);
        }
        if($method == 'paymentmethods'){
            $this->paymentmethods();
        }elseif($method == 'success'){
             $this->success();
        }else{
            $helper = $this->_objectManager->get("\Knowband\Mobileappbuilder\Helper\\".$helper_file_name);
            $response = $helper->$method();
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($response);
            return $resultJson;
        }
    }
    
    private function setIntallModuleResponse()
    {
        $response["status"] = "failure";
        $response["message"] = __("Knowband Mobile App Builder is not enabled on this store.");
        $response["install_module"] = __("Knowband Mobile App Builder is not enabled on this store.");
        $response["session_data"] = "";
        $response["version"] = $this->sp_helper->getVersion();
        return $response;
    }
    
    private function checkRequestedApi($api_verion) {
        $module_helper_dir = BP . '/app/code/Knowband/Mobileappbuilder/Helper/';
        $helper_file_name = $this->sp_helper->getHelperName($api_verion);
        if ($helper_file_name) {
            $module_helper_dir = $module_helper_dir . $helper_file_name . '.php';
            if (file_exists($module_helper_dir)) {
                $helper_file_name = $helper_file_name;
                return $helper_file_name;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    public function setRequestedApiNotAvailableResponse()
    {
        $response["status"] = "failure";
        $response["message"] = __("Requested API not available");
        $response["session_data"] = "";
        $response["version"] = $this->sp_helper->getVersion();
        return $response;
    }
    
    public function setMethodNotAvailableResponse()
    {
        $response["status"] = "failure";
        $response["message"] = __("Requested Api method not available");
        $response["session_data"] = "";
        $response["version"] = $this->sp_helper->getVersion();
        return $response;
    }
    
    public function paymentmethods()
    {
        $blockSuccessRequestUpload = $this->_viewLayoutFactory->create()->createBlock('Knowband\Mobileappbuilder\Block\WebPayment');
        $output = $blockSuccessRequestUpload->toHtml();
        $this->getResponse()->appendBody($output);
    }
    
    public function success()
    {
        $session = $this->_objectManager->get('Magento\Checkout\Model\Type\Onepage')->getCheckout();
        $lastOrderId = $session->getLastOrderId();

        $this->_eventManager->dispatch('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
        $checkoutSessionQuote = $this->_objectManager->get('\Magento\Checkout\Model\Session')->getQuote();
        $checkoutSessionQuote->setIsActive(0)->save();
        $checkoutSessionQuote->delete();
//        $checkoutSessionQuote->clearQuote();
        $this->getResponse()->setBody(
            "Order Placed."
        );
    }
	
	   public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

}
