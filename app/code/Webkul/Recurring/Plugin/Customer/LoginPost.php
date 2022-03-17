<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Plugin\Customer;

use Magento\Customer\Model\Account\Redirect as AccountRedirect;

class LoginPost
{
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param AccountRedirect $accountRedirect
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        AccountRedirect $accountRedirect,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    ) {
        $this->_request = $context->getRequest();
        $this->_response = $context->getResponse();
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->resultFactory = $context->getResultFactory();
        $this->coreSession   = $coreSession;
        $this->accountRedirect = $accountRedirect;
    }

    /**
     * @param \Magento\Customer\Controller\Account\LoginPost $subject
     * @param \Closure $proceed
     * @return void
     */
    public function aroundExecute(\Magento\Customer\Controller\Account\LoginPost $subject, $proceed)
    {
        $url = $this->coreSession->getReferUrl();
        if ($url) {
            $this->accountRedirect->setRedirectCookie($url);
            $returnValue = $proceed();
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath($url);
            $this->coreSession->unsReferUrl();
            return $resultRedirect;
        }
        return $proceed();
    }
}
