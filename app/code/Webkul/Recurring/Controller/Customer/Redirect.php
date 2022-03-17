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
namespace Webkul\Recurring\Controller\Customer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;

/**
 * Webkul Recurring Redirect Customer Controller.
 */
class Redirect extends Action
{
    /**
     * @var AccountRedirect
     */
    private $accountRedirect;

    /**
     * @var ResultFactory
     */
    private $resultRedirect;

    /**
     * @var UrlInterface
     */
    private $urlInterface;

    /**
     * @var SessionManagerInterface
     */
    private $coreSession;

    /**
     *
     * @param Context $context
     * @param \Magento\Framework\Controller\ResultFactory $result
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     * @param AccountRedirect $accountRedirect
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\ResultFactory $result,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        AccountRedirect $accountRedirect
    ) {
        $this->accountRedirect          = $accountRedirect;
        $this->resultRedirect           = $result;
        $this->coreSession              = $coreSession;
        $this->urlInterface             = $urlInterface;
        parent::__construct($context);
    }

    /**
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $url = $this->getRequest()->getparam('url');
        $this->accountRedirect->setRedirectCookie($url);
        $this->coreSession->setReferUrl($url);
        $resultRedirect = $this->resultRedirect->create(
            \Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT
        );
        $resultRedirect->setUrl(
            $this->urlInterface->getUrl("customer/account/login")
        );
        return $resultRedirect;
    }
}
