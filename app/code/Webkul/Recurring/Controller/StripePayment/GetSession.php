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
use Magento\Framework\Controller\ResultFactory;
use Webkul\Recurring\Model\SubscriptionsFactory  as Subscriptions;
use Webkul\Recurring\Model\SubscriptionTypeFactory as SubscriptionType;
use Webkul\Recurring\Model\TermFactory as Term;
use Magento\Framework\Session\SessionManager;
use Magento\Quote\Model\QuoteRepository;

class GetSession extends Action
{

    protected $checkoutSession;

    protected $_jsonResultFactory;

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
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param \Magento\Checkout\Model\Type\Onepage $onePage
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Webkul\Recurring\Helper\Data $helper
     * @param \Webkul\Recurring\Helper\Paypal $paypalHelper
     * @param \Webkul\Recurring\Helper\Stripe $stripeHelper
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Checkout\Model\Type\Onepage $onePage,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Webkul\Recurring\Helper\Data $helper,
        \Webkul\Recurring\Helper\Paypal $paypalHelper,
        \Webkul\Recurring\Helper\Stripe $stripeHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        QuoteRepository $quoteRepository,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Session\SessionManager $coreSession,
        Subscriptions $subscriptions,
        SubscriptionType $plans,
        Term $term
    ) {
        $this->_jsonResultFactory = $jsonResultFactory;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->orderFactory = $orderFactory;
        $this->helper = $helper;
        $this->paypalHelper = $paypalHelper;
        $this->stripeHelper = $stripeHelper;
        $this->onePage = $onePage;
        $this->term = $term;
        $this->plans = $plans;
        $this->quoteRepository = $quoteRepository;
        $this->subscriptions = $subscriptions;
        $this->jsonHelper = $jsonHelper;
        $this->coreSession = $coreSession;
        parent::__construct($context);
    }

    /**
     * create stripe data for checkout page
     */
    public function execute()
    {
        try {
            $this->helper->logDataInLogger("GetSession");

            $smallcurrencyarray = [
                "bif", "clp", "djf", "gnf", "jpy", "kmf", "krw", "mga", "pyg", "rwf","vnd", "vuv", "xaf", "xof", "xpf"
            ];
            $orderId = $this->onePage->getCheckout()->getLastOrderId();
            $resultJson = $this->_jsonResultFactory->create();
            $resultJson->setHeader('Cache-Control', 'max-age=0, must-revalidate, no-cache, no-store', true);
            $resultJson->setHeader('Pragma', 'no-cache', true);

            \Stripe\Stripe::setApiKey($this->stripeHelper->getConfigValue("api_secret_key"));
            // \Stripe\Stripe::setAppInfo(
            //     "Webkul Recurring Payments & Subscription for Magento 2",
            //     "3.0.1",
            //     "https://store.webkul.com/magento2-recurring-subscription.html",
            //     "pp_partner_FLJSvfbQDaJTyY"
            // );
            // \Stripe\Stripe::setApiVersion("2019-12-03");
            
            $order = $this->orderFactory->create()->load($orderId);
            $amount  = $order->getGrandTotal();
            $smallcurrencyarray = [
                "bif", "clp", "djf", "gnf", "jpy", "kmf", "krw", "mga", "pyg", "rwf","vnd", "vuv", "xaf", "xof", "xpf"
            ];

            /** @var \Magento\Quote\Model\Quote  */
            $quote                   = $this->quoteRepository->get($order->getQuoteId());
            $cartData                = $quote->getAllItems();
            $planId                  = $interval = $intervalCount = "";
            $initialFee              = 0.0;
            $subscriptionProductName = '';
            $startDate               = date("m/d/Y");
            foreach ($cartData as $item) {
                if ($additionalOptionsQuote = $item->getOptionByCode('custom_additional_options')) {
                    $subscriptionProductName = $item->getName();
                    $allOptions = $this->jsonHelper->jsonDecode(
                        $additionalOptionsQuote->getValue()
                    );
                    foreach ($allOptions as $key => $option) {
                        if ($option['label'] == 'Plan Id') {
                            $planId = $option['value'];
                        }
                        if ($option['label'] == 'Initial Fee') {
                            $initialFee = $initialFee + $option['value'];
                        }
                        if ($option['label'] == 'Start Date') {
                            $startDate = $option['value'];
                        }
                    }
                }
            }

            if ($planId) {
                $result         = $this->getFrequency($planId);
                if ($result['frequency'] != 0) {
                    $intervalCount  = $result['frequency'];
                    $interval       = strtolower($result['peroid']);
                }
            }
            $stripeAmount = $amount - $initialFee;
            $currencyStripe = strtolower($order->getStore()->getCurrentCurrencyCode());
            $stripeAmount = (in_array($currencyStripe, $smallcurrencyarray)) ? round($stripeAmount) : $stripeAmount * 100;
            $initialFee   = (in_array($currencyStripe, $smallcurrencyarray)) ? round($initialFee) : $initialFee * 100;

            $product = \Stripe\Product::create(
                [
                    "name" => $subscriptionProductName,
                    "type" => "service"
                ]
            );
            $this->helper->logDataInLogger(json_encode($product));
            $initialProduct = \Stripe\Product::create(
                [
                    "name" => "InitialFee-".$subscriptionProductName
                ]
            );
            $this->helper->logDataInLogger(json_encode($initialProduct));

            $price = \Stripe\Price::create(
                [
                    'product'       => $product["id"],
                    'unit_amount'   => $stripeAmount,
                    'currency'      => $currencyStripe,
                    'recurring'     => [
                        'interval'    => $interval,
                        'interval_count' => $intervalCount
                    ],
                    'nickname'      => $order->getIncrementId(),
                ]
            );
            $this->helper->logDataInLogger(json_encode($price));
            $initialPrice = \Stripe\Price::create(
                [
                    'product'       => $initialProduct["id"],
                    'unit_amount'   => $initialFee,
                    'currency'      => $currencyStripe,
                    'nickname'      => $order->getIncrementId(),
                ]
            );
            $this->helper->logDataInLogger(json_encode($initialPrice));

            $lineItems = [];
            $lineItems[] = [
                'price' => $price["id"],
                'quantity' => 1,
            ];
            $lineItems[] = [
                'price' => $initialPrice["id"],
                'quantity' => 1,
            ];
            $response = \Stripe\Checkout\Session::create([
                "payment_method_types" => ["card"],
                "line_items" => $lineItems,
                'mode' => 'subscription',
                "success_url" => $this->storeManager->getStore()->getUrl('recurring/stripepayment/success'),
                "cancel_url" => $this->storeManager->getStore()->getUrl('stripe/stripepayment/failure'),
                "client_reference_id" => $orderId,
                "customer_email" => $order->getCustomerEmail()
            ]);
            $this->helper->logDataInLogger(json_encode($response));

            return $resultJson->setData($response);
        } catch (\Exception $e) {
            $this->helper->logDataInLogger("StripePayment_GetSession execute : ".$e->getMessage());
            return false;
        }
    }

    /**
     * This function return the duration of the plan
     *
     * @param integer $planId
     * @return integer
     */
    private function getFrequency($planId)
    {
        //paypalHelper is used for code reuseability
        $typeId         = $this->plans->create()->load($planId)->getType();
        $termDuration   = $this->term->create()->load($typeId)->getDuration();
        $result         = $this->paypalHelper->calculateDuration($termDuration);
        return $result;
    }
}
