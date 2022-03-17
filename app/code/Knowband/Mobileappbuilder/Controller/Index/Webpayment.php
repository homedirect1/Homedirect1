<?php
/**
 * For web-view checkout
 */
namespace Knowband\Mobileappbuilder\Controller\Index;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
class Webpayment extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
//class Webpayment extends \Magento\Checkout\Controller\Onepage
{
    
    /**
     * Checkout page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try{
//            $params = $this->getRequest()->getPost();
//            $customerSession = $this->_objectManager->get("\Magento\Customer\Model\Session");
            //Create the customer session for checkout
//            $helper = $this->_objectManager->get("\Knowband\Mobileappbuilder\Helper\OneSix");
//            if(isset($params['email']) && !empty($params['email'])){
////                $response = $helper->forceLogin($params['email']);      
//            }else{
                $sessionId = $this->getRequest()->getParam('SID');

                $cookieMetadataFactory = $this->_objectManager->get("Magento\Framework\Stdlib\Cookie\CookieMetadataFactory");
                $sessionManager = $this->_objectManager->get("Magento\Framework\Session\SessionManagerInterface");
                $cookieManager = $this->_objectManager->get("Magento\Framework\Stdlib\CookieManagerInterface");

                $metadata = $cookieMetadataFactory
                    ->createPublicCookieMetadata()
                    ->setDuration(86400)
                    ->setPath($sessionManager->getCookiePath())
                    ->setDomain($sessionManager->getCookieDomain());

                $cookieManager->setPublicCookie(
                    'PHPSESSID',
                    $sessionId,
                    $metadata
                );
//            }
               
            
            return $this->resultRedirectFactory->create()->setPath('checkout/index', array('mobileappbuilder_webview' => 1));
        
        } catch (\Exception $ex) {
        }
        
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
