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
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Framework\App\Request\InvalidRequestException;

/**
 * Webkul Recurring Abstract Controller.
 */
abstract class WebhookAbstract extends Action implements \Magento\Framework\App\CsrfAwareActionInterface
{
    /**
     * @var SubscriptionsFactory
     */
    protected $subscription;
    
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderModel;
    
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
     * @var \Magento\Framework\DB\Transaction
     */
    protected $transaction;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $fileDriver;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    protected $invoiceSender;

    /**
     * @var \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface
     */
    protected $transactionBuilder;

    /**
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Webkul\Recurring\Model\SubscriptionsFactory $subscription
     * @param \Webkul\Recurring\Helper\Stripe $stripeHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Webkul\Recurring\Model\Cron $cron
     * @param \Webkul\Recurring\Helper\Order $orderHelper
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param \Magento\Sales\Model\OrderFactory $orderModel
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param InvoiceSender $invoiceSender
     * @param Transaction\BuilderInterface $transactionBuilder
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Webkul\Recurring\Model\SubscriptionsFactory $subscription,
        \Webkul\Recurring\Helper\Stripe $stripeHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Webkul\Recurring\Model\Cron $cron,
        \Webkul\Recurring\Helper\Order $orderHelper,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Model\OrderFactory $orderModel,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        InvoiceSender $invoiceSender,
        Transaction\BuilderInterface $transactionBuilder
    ) {
        $this->orderModel             = $orderModel;
        $this->orderHelper            = $orderHelper;
        $this->invoiceService         = $invoiceService;
        $this->transaction            = $transaction;
        $this->cron                   = $cron;
        $this->stripeHelper           = $stripeHelper;
        $this->subscription           = $subscription;
        $this->jsonHelper             = $jsonHelper;
        $this->fileDriver             = $fileDriver;
        $this->invoiceSender          = $invoiceSender;
        $this->transactionBuilder     = $transactionBuilder;
        parent::__construct($context);
    }

    /**
     * Write custom Logs
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
