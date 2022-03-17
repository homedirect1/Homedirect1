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
namespace Webkul\Recurring\Controller\Paypal;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory as ResultFactory;
use Magento\Sales\Model\Order as OrderModel;
use Webkul\Recurring\Model\Term  as Term;
use Webkul\Recurring\Model\Mapping  as Mapping;
use Webkul\Recurring\Model\Subscriptions  as Subscriptions;
use Magento\Framework\Stdlib\DateTime\DateTime  as Date;
use Magento\Quote\Api\CartManagementInterface  as CartManagement;
use Magento\Quote\Api\CartRepositoryInterface  as CartRepository;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;
use Magento\Catalog\Model\ProductFactory as ProductFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface as StockRegistry;
use Magento\Checkout\Model\CartFactory as CartFactory;
use Magento\Quote\Model\QuoteRepository;
use Magento\Checkout\Model\Cart as CheckoutCart;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Webkul\Recurring\Model\SubscriptionType;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Framework\App\Request\InvalidRequestException;

/**
 * Webkul Recurring Landing page Index Controller.
 */
abstract class PaypalAbstract extends Action implements \Magento\Framework\App\CsrfAwareActionInterface
{
    const  SANDBOX          = "payment/recurringpaypal/sandbox";
    const  USERNAME         = "payment/recurringpaypal/api_username";
    const  PASSWORD         = "payment/recurringpaypal/api_password";
    const  SIGNATURE        = "payment/recurringpaypal/api_signature";
    const  URL              = "https://api-3t.";
    const  URL_COMPLETE     = "paypal.com/nvp";
    const  CANCEL_URL       = 'recurring/paypal/cancel';
    const  RETURN_URL       = 'recurring/paypal/returnAction';
    const  NOTIFICATION_URL = 'recurring/paypal/notify';
    /**
     * @var PageFactory
     */
    protected $helper;

    /**
     * @var subscriptions
     */
    protected $subscriptions;

    /**
     * @var Date
     */
    protected $date;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $coreSession;
    
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultRedirect;

    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $transaction;

    /**
     * @var \Webkul\Recurring\Model\Cron
     */
    protected $cron;

    /**
     * @var \Webkul\Recurring\Helper\Order
     */
    protected $orderHelper;
    
    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $invoiceService;
    
    /**
     * @var SubscriptionType
     */
    protected $plans;

    /**
     * @var Term
     */
    protected $term;
    
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface
     */
    protected $transactionBuilder;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    protected $invoiceSender;

    /**
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param OrderModel $orderModel
     * @param Date $date
     * @param Subscriptions $subscriptions
     * @param SubscriptionType $plans
     * @param Term $term
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\UrlInterface $urlBulder
     * @param \Magento\Framework\Controller\ResultFactory $result
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param \Webkul\Recurring\Helper\Paypal $helper
     * @param \Webkul\Recurring\Helper\Order $orderHelper
     * @param \Webkul\Recurring\Model\Cron $cron
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param CheckoutCart $checkoutCart
     * @param QuoteRepository $quoteRepository
     * @param Transaction\BuilderInterface $transactionBuilder
     * @param InvoiceSender $invoiceSender
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        Context $context,
        PageFactory $resultPageFactory,
        OrderModel $orderModel,
        Date $date,
        Subscriptions $subscriptions,
        SubscriptionType $plans,
        Term $term,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\UrlInterface $urlBulder,
        \Magento\Framework\Controller\ResultFactory $result,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        \Webkul\Recurring\Helper\Paypal $helper,
        \Webkul\Recurring\Helper\Order $orderHelper,
        \Webkul\Recurring\Model\Cron $cron,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        CheckoutCart $checkoutCart,
        QuoteRepository $quoteRepository,
        Transaction\BuilderInterface $transactionBuilder,
        InvoiceSender $invoiceSender
    ) {
        $this->term                     = $term;
        $this->plans                    = $plans;
        $this->coreSession              = $coreSession;
        $this->cron                     = $cron;
        $this->orderHelper              = $orderHelper;
        $this->checkoutSession          = $checkoutSession;
        $this->curl                     = $curl;
        $this->urlBulder                = $urlBulder;
        $this->logger                   = $logger;
        $this->resultPageFactory        = $resultPageFactory;
        $this->date                     = $date;
        $this->subscriptions            = $subscriptions;
        $this->orderModel               = $orderModel;
        $this->transaction              = $transaction;
        $this->invoiceService           = $invoiceService;
        $this->resultRedirect           = $result;
        $this->helper                   = $helper;
        $this->checkoutCart             = $checkoutCart;
        $this->quoteRepository          = $quoteRepository;
        $this->jsonHelper               = $jsonHelper;
        $this->transactionBuilder       = $transactionBuilder;
        $this->invoiceSender            = $invoiceSender;
        parent::__construct($context);
    }

    /**
     * Express paypal url
     *
     * @param boolean $isSandBox
     * @return string
     */
    protected function getExpressUrl($isSandBox)
    {
        return "https://www.".(($isSandBox) ? "sandbox." : "")."paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=";
    }

    /**
     * Express paypal Action
     *
     * @param boolean $isSandBox
     * @return string
     */
    protected function getActionUrl($isSandBox)
    {
        return "https://www.".(($isSandBox) ? "sandbox." : "")."paypal.com/cgi-bin/webscr";
    }

    /**
     * Write custom logs
     *
     * @param string $message
     * @return void
     */
    protected function printLog($message)
    {
        $this->cron->printLog($message);
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
            return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
