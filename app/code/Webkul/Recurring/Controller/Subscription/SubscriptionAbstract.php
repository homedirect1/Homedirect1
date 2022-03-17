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
namespace Webkul\Recurring\Controller\Subscription;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;

/**
 * Webkul Recurring Abstract Controller.
 */
abstract class SubscriptionAbstract extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;

    /**
     * @var sessionManager
     */
    protected $sessionManager;

    /**
     * @var \Webkul\Recurring\Helper\Stripe
     */
    protected $stripeHelper;

    /**
     * @var Subscription
     */
    protected $subscription;
    
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     *
     * @param Context $context
     * @param Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Webkul\Recurring\Model\Subscriptions $subscription
     * @param \Webkul\Recurring\Helper\Paypal $paypalHelper
     * @param \Webkul\Recurring\Helper\Stripe $stripeHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Session\SessionManager $sessionManager
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Webkul\Recurring\Model\Subscriptions $subscription,
        \Webkul\Recurring\Helper\Paypal $paypalHelper,
        \Webkul\Recurring\Helper\Stripe $stripeHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Session\SessionManager $sessionManager
    ) {
        $this->customerSession      = $customerSession;
        $this->resultPageFactory    = $resultPageFactory;
        $this->customerUrl          = $customerUrl;
        $this->paypalHelper         = $paypalHelper;
        $this->stripeHelper         = $stripeHelper;
        $this->coreRegistry         = $coreRegistry;
        $this->subscription         = $subscription;
        $this->jsonHelper           = $jsonHelper;
        $this->sessionManager       = $sessionManager;
        parent::__construct($context);
    }

     /**
      * Check customer authentication.
      *
      * @param RequestInterface $request
      * @return \Magento\Framework\App\ResponseInterface
      */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->customerUrl->getLoginUrl();

        if (!$this->customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }
}
