<?php
namespace Knowband\Mobileappbuilder\Observer;
 
use Magento\Framework\Event\ObserverInterface;

class CheckMobileappbuilderWebview implements ObserverInterface
{    
    
    public function __construct(
            \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager, 
            \Magento\Framework\App\Helper\Context $context,
            \Knowband\Mobileappbuilder\Helper\Data $sp_helper,
            \Knowband\Mobileappbuilder\Model\OrderStatus $sp_orderModel
            ) {
        $this->sp_request = $context->getRequest();
        $this->sp_helper = $sp_helper;
        $this->ar_cookieManager = $cookieManager;
        $this->sp_orderModel = $sp_orderModel;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $mobileappbuilder_webview = $this->sp_request->getParam('mobileappbuilder_webview', '');
            
            $payment_cookie = $this->ar_cookieManager->getCookie('kb_mobileapp_payment_webview');
            
            
            if (($mobileappbuilder_webview && $mobileappbuilder_webview != '') || (isset($payment_cookie) && $payment_cookie == 1)) {
                $layout = $observer->getLayout();
//                $layout->getUpdate()->addUpdate('
//                    <body>  
//                    <remove name="header" />   
//                    <referenceBlock name="header.container" remove="true" />       
//                    <remove name="breadcrumbs"  remove="true" />
//                    <referenceBlock name="top.links" remove="true"/>           
//                    <referenceBlock name="page.top" remove="true"/>           
//                    <referenceBlock name="footer-container" remove="true" />    
//                    <referenceBlock name="logo" remove="true" />    
//                    
//                   </body>');
//                $layout->getUpdate()->load();
                $layout->generateXml();
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }   
}
