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
namespace Webkul\Recurring\Model\Stripe;

use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\Order\Payment;
use Webkul\Recurring\Model\SubscriptionsFactory  as Subscriptions;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\Order\Payment\Transaction as PaymentTransaction;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Session\SessionManager;
use Magento\Quote\Model\QuoteRepository;
use Webkul\Recurring\Model\SubscriptionTypeFactory as SubscriptionType;
use Webkul\Recurring\Model\TermFactory as Term;

class PaymentMethod extends AbstractMethod
{
    const CODE = 'recurringstripe';

    /**
     * @var \Webkul\Recurring\Helper\Data
     */
    private $helper;

    /**
     * @var bool
     */
    protected $_isGateway = true;

    /**
     * @var SubscriptionType
     */
    protected $plans;

    /**
     * @var Term
     */
    protected $term;

    /**
     * Availability option.
     *
     * @var bool
     */
    protected $_isInitializeNeeded = false;

    /**
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Availability option.
     *
     * @var bool
     */
    protected $_canRefund = false;

    /**
     * Availability option.
     *
     * @var bool
     */
    protected $_canRefundInvoicePartial = false;

    /**
     * Availability option.
     *
     * @var bool
     */
    protected $_canUseInternal = false;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Transaction\BuilderInterface
     */
    protected $transactionBuilder;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Sales\Model\Order\Payment\Transaction\Repository
     */
    private $transactionRepository;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * Order Id
     *
     * @var string
     */
    private $orderId = '';

    /**
     * Transaction Id
     *
     * @var string
     */
    private $transactionId = '';

    /**
     * Order Currency Code
     *
     * @var string
     */
    private $orderCurrencyCode = '';

    /**
     * Base Currency Code
     *
     * @var string
     */
    private $baseCurrencyCode = '';

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var Subscriptions
     */
    protected $subscriptions;

    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $coreSession;
    
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Webkul\Recurring\Helper\Data $helper
     * @param \Webkul\Recurring\Helper\Paypal $paypalHelper
     * @param \Webkul\Recurring\Helper\Stripe $stripeHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param Payment\Transaction\BuilderInterface $transactionBuilder
     * @param RequestInterface $request
     * @param QuoteRepository $quoteRepository
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Session\SessionManager $coreSession
     * @param Subscriptions $subscriptions
     * @param SubscriptionType $plans
     * @param Term $term
     * @param \Magento\Sales\Model\Order\Payment\Transaction\Repository $transactionRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Webkul\Recurring\Helper\Data $helper,
        \Webkul\Recurring\Helper\Paypal $paypalHelper,
        \Webkul\Recurring\Helper\Stripe $stripeHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        Payment\Transaction\BuilderInterface $transactionBuilder,
        RequestInterface $request,
        QuoteRepository $quoteRepository,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Session\SessionManager $coreSession,
        Subscriptions $subscriptions,
        SubscriptionType $plans,
        Term $term,
        \Magento\Sales\Model\Order\Payment\Transaction\Repository $transactionRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );
        $this->term                     = $term;
        $this->plans                    = $plans;
        $this->quoteRepository          = $quoteRepository;
        $this->urlBuilder               = $urlBuilder;
        $this->subscriptions            = $subscriptions;
        $this->jsonHelper               = $jsonHelper;
        $this->coreSession              = $coreSession;
        $this->transactionBuilder       = $transactionBuilder;
        $this->request                  = $request;
        $this->helper                   = $helper;
        $this->paypalHelper             = $paypalHelper;
        $this->stripeHelper             = $stripeHelper;
        $this->transactionRepository    = $transactionRepository;
        $this->orderRepository          = $orderRepository;
        /*
         * set api key for payment  >> sandbox api key or live api key
         */
        if ($this->getDebugFlag()) {
            \Stripe\Stripe::setApiKey(
                $this->stripeHelper->getConfigValue('api_secret_key')
            );
        } else {
            \Stripe\Stripe::setApiKey(
                $this->stripeHelper->getConfigValue('api_secret_key')
            );
        }
        // \Stripe\Stripe::setAppInfo(
        //     "Webkul Recurring Payments & Subscription for Magento 2",
        //     "3.0.1",
        //     "https://store.webkul.com/magento2-recurring-subscription.html",
        //     "pp_partner_FLJSvfbQDaJTyY"
        // );
        // \Stripe\Stripe::setApiVersion("2019-12-03");
    }

    /**
     * Authorizes specified amount.
     *
     * @param InfoInterface $payment
     * @param float         $amount
     *
     * @return $this
     *
     * @throws LocalizedException
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        return $this;
    }

    /**
     * Captures specified amount.
     *
     * @param InfoInterface $payment
     * @param float         $amount
     *
     * @return $this
     *
     * @throws LocalizedException
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $order = $payment->getOrder();
        $amount  = $order->getGrandTotal();
        parent::capture($payment, $amount);
    }

    /**
     * Do not validate payment form using server methods.
     *
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * Assign corresponding data.
     *
     * @param \Magento\Framework\DataObject|mixed $data
     *
     * @return $this
     *
     * @throws LocalizedException
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);
        return $this;
    }

    /**
     * Define if debugging is enabled.
     *
     * @return bool
     *
     * @api
     */
    public function getDebugFlag()
    {
        if ($this->getConfigData('sandbox')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * getCardData get the unique fingureprint from the card object
     *
     * @param [String] $token
     * @return String
     */
    public function getCardData($token = null)
    {
        try {
            $tokenData = \Stripe\Token::retrieve($token);
            return $tokenData;
        } catch (\Exception $e) {
            throw new LocalizedException(
                __(
                    'There was an error capturing the transaction: %1',
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * Get config payment action url
     * Used to universalize payment actions when processing payment place
     *
     * @return string
     * @api
     */
    public function getConfigPaymentAction()
    {
        $sType = $this->getInfoInstance()->getAdditionalInformation('stype');
        if ($sType == 'bitcoin' && $this->getConfigData('payment_action') == 'authorize') {
            return self::ACTION_AUTHORIZE_CAPTURE;
        } else {
            return $this->getConfigData('payment_action');
            // parent::getConfigPaymentAction(); this will not call overrided authorize and capture
        }
    }
}
