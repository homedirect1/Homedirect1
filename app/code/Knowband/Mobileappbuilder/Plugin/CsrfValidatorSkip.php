<?php
namespace Knowband\Mobileappbuilder\Plugin;
class CsrfValidatorSkip
{   
    public function __construct(
            \Magento\Framework\App\Helper\Context $context,
            \Magento\Framework\App\RequestInterface $request,
            \Magento\Framework\UrlInterface $urlInterface
            ) {
        $this->sp_request = $request;
        $this->kb_urlinterface = $urlInterface;
        $this->_scopeConfig = $context->getScopeConfig();
    }
    
    /**
     * @param \Magento\Framework\App\Request\CsrfValidator $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\ActionInterface $action
     */
    public function aroundValidate(
        $subject,
        \Closure $proceed,
        $request,
        $action
    ) {

        
        if (strpos($this->kb_urlinterface->getCurrentUrl(), 'mobileappbuilder') === true) {
                    return true;
           // Skip CSRF check
        }
        $proceed($request, $action);
    }
}