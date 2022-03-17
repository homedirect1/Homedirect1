<?php

namespace Knowband\Mobileappbuilder\Plugin;

class Session extends \Magento\Framework\View\Element\Template
{
 
    public function __construct(
            \Magento\Framework\App\Helper\Context $context
            ) {
        $this->sp_request = $context->getRequest();
        $this->_scopeConfig = $context->getScopeConfig();
    }
    
    public function beforeSetCookieLifetime()
    {
        $mobileappbuilder_webview = $this->sp_request->getParam('mobileappbuilder_webview', '');
        $mobileappbuilder_method = $this->sp_request->getParam('method', '');
        if (strpos($mobileappbuilder_method, 'app') !== FALSE) {
            return [0, 0];
        } else if ($mobileappbuilder_webview && $mobileappbuilder_webview != '') {
            return [0, 0];
        } 
        else {
            $lifetime = $this->_scopeConfig->getValue(\Magento\Framework\Session\Config::XML_PATH_COOKIE_LIFETIME, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            return [$lifetime, \Magento\Framework\Session\Config::COOKIE_LIFETIME_DEFAULT];
        }
    }
    
    
}