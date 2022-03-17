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
namespace Webkul\Recurring\Model;

use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\Order\Payment;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\Order\Payment\Transaction as PaymentTransaction;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Session\SessionManager;

class PaymentMethod extends AbstractMethod
{
    const CODE = 'recurringpaypal';

    /**
     * @var \Webkul\Recurring\Helper\Data
     */
    private $helper;

    /**
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Availability option.
     *
     * @var bool
     */
    protected $_isInitializeNeeded = true;

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
    protected $_urlBuilder;

    /**
     * @var Transaction\BuilderInterface
     */
    protected $_transactionBuilder;

    /**
     * @var RequestInterface
     */
    protected $_request;

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
     * @param \Magento\Framework\Model\Context                                    $context
     * @param \Magento\Framework\Registry                                         $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory                   $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory                        $customAttributeFactory
     * @param \Magento\Payment\Helper\Data                                        $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface                  $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger                                $logger
     * @param \Magento\Framework\UrlInterface                                     $urlBuilder
     * @param Transaction\BuilderInterface                                        $transactionBuilder
     * @param RequestInterface                                                    $request
     * @param \Magento\Sales\Model\Order\Payment\Repository                       $transactionRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface                         $orderRepository
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource             $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb                       $resourceCollection
     * @param array                                                               $data
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
        \Magento\Framework\UrlInterface $urlBuilder,
        Payment\Transaction\BuilderInterface $transactionBuilder,
        RequestInterface $request,
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
        $this->_urlBuilder = $urlBuilder;
        $this->_transactionBuilder = $transactionBuilder;
        $this->_request = $request;
        $this->helper = $helper;
        $this->transactionRepository = $transactionRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Checkout redirect URL getter for onepage checkout (hardcode).
     *
     * @see \Magento\Checkout\Controller\Onepage::savePaymentAction()
     * @see Quote\Payment::getCheckoutRedirectUrl()
     *
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        try {
            return $this->_urlBuilder->getUrl(
                'recurring/paypal/index'
            );
        } catch (\Exception $e) {
            $this->helper->logDataInLogger("Model_PaymentMethod getCheckoutRedirectUrl : ".$e->getMessage());
        }
    }
}
