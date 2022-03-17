<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul Software Private Limited
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Controller\StripePayment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Sales\Model\Order;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;

class Failure extends Action implements CsrfAwareActionInterface
{
    protected $_publicActions = ['initializerequest'];
    
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @var Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Checkout\Model\Type\Onepage $onePage
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param Session $customerSession
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Checkout\Model\Type\Onepage $onePage,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        Session $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->orderFactory = $orderFactory;
        $this->onePage = $onePage;
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
    
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * handle payment failure
     */
    public function execute()
    {
        $orderId = $this->onePage->getCheckout()->getLastOrderId();
        $order = $this->orderFactory->create()->load($orderId);
        $orderState = Order::STATE_PENDING_PAYMENT;
        $order->setState($orderState)->setStatus(Order::STATE_PENDING_PAYMENT);
        $order->save();
        
        return $this->resultRedirectFactory->create()->setPath('checkout/onepage/failure', ['_current' => true]);
    }
}
