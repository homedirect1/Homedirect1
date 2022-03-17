<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSla\Controller\Vorders;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;


/**
 * Class MassConfirm
 * @package Ced\CsSla\Controller\Vorders
 */
class MassConfirm extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    protected $invoiceSender;
    const EMAIL_TEMPLATE = 'ced_csmarketplace/vsla/send_confirm_email_template';
    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $invoiceService;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Ced\CsMarketplace\Model\VordersFactory
     */
    protected $vordersFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $state;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $transaction;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $emailSender;

    /**
     * MassConfirm constructor.
     * @param \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Ced\CsMarketplace\Model\VordersFactory $vordersFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\Translate\Inline\StateInterface $state
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Mail\Template\TransportBuilder $emailSender
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlFactory $urlFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper
     * @param \Ced\CsMarketplace\Helper\Acl $aclHelper
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendor
     */
    public function __construct(
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ced\CsMarketplace\Model\VordersFactory $vordersFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Translate\Inline\StateInterface $state,
        \Magento\Framework\DB\Transaction $transaction,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Mail\Template\TransportBuilder $emailSender,
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Session $customerSession,
        UrlFactory $urlFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        \Ced\CsMarketplace\Helper\Acl $aclHelper,
        \Ced\CsMarketplace\Model\VendorFactory $vendor,
        StoreManagerInterface $storeManager
    )
    {
        parent::__construct(
            $context,
            $resultPageFactory,
            $customerSession,
            $urlFactory,
            $registry,
            $jsonFactory,
            $csmarketplaceHelper,
            $aclHelper,
            $vendor
        );

        $this->invoiceSender = $invoiceSender;
        $this->invoiceService = $invoiceService;
        $this->scopeConfig = $scopeConfig;
        $this->vordersFactory = $vordersFactory;
        $this->orderFactory = $orderFactory;
        $this->state = $state;
        $this->transaction = $transaction;
        $this->logger = $logger;
        $this->emailSender = $emailSender;
        $this->storeManager = $storeManager;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $error = [];
        $success = [];
        $vendorDatas = $this->session->getVendor();

        $params = $this->getRequest()->getParams();
        if (isset($params['id'])) {
            $confirmIds = explode(',', $params['id']);
        } else {
            $confirmIds = [$params['order_id']];
        }
        $alconfirmids = [];
        if (isset($confirmIds)) {
            foreach ($confirmIds as $id) {
                $isInvoiceCreated = false;

                $vorderModel = $this->vordersFactory->create()->load($id);

                $invoiceEnable = $this->scopeConfig->getValue('ced_csmarketplace/vsla/generate_invoice');

                if ($vorderModel->getOrderStatus() == \Ced\CsSla\Helper\Data::STATE_CONFIRMED) {
                    $alconfirmids[] = $id;
                    continue;
                }
                if ($vorderModel->getOrderStatus() != \Ced\CsSla\Helper\Data::STATE_CANCELLED) {

                    if ($vorderModel->getOrderPaymentState() == \Ced\CsMarketplace\Model\Vorders::STATE_OPEN || $vorderModel->getOrderPaymentState() == \Ced\CsMarketplace\Model\Vorders::STATE_PAID) {
                        if ($invoiceEnable) {
                            if ($this->scopeConfig->getValue('ced_vorders/general/vorders_caninvoice') && $vorderModel->getOrderPaymentState() == \Ced\CsMarketplace\Model\Vorders::STATE_OPEN) {

                                $this->createInvoice($id);
                                $isInvoiceCreated = true;

                                if ($isInvoiceCreated) {
                                    $vorderModel = $this->vordersFactory->create()->load($id);
                                    $vorderModel->setOrderStatus(\Ced\CsSla\Helper\Data::STATE_CONFIRMED);
                                    $vorderModel->setOrderPaymentState(2);
                                    $vorderModel->save();
                                    $success[] = $id;
                                } else {
                                    $error[] = $id;
                                }

                            } else {

                                $vorderModel = $this->vordersFactory->create()->load($id);
                                $vorderModel->setOrderStatus(\Ced\CsSla\Helper\Data::STATE_CONFIRMED);
                                $vorderModel->save();
                            }
                        } else {
                            $vorderModel = $this->vordersFactory->create()->load($id);
                            $vorderModel->setOrderStatus(\Ced\CsSla\Helper\Data::STATE_CONFIRMED);
                            $vorderModel->save();

                        }

                        /**
                         * send confirm email
                         */
                        try {
                            $customerData = $this->orderFactory->create()->load($vorderModel->getOrderId(), 'increment_id');
                            $email = $customerData->getCustomerEmail();
                            $name = $customerData->getCustomerFirstname();
                            $description = 'your order with orderId ' . $vorderModel->getOrderId() . ' which is associated to ' . $vendorDatas['name'] . ' is confirmed by Vendor';

                            $senderEmail = $this->csmarketplaceHelper->getStoreConfig('trans_email/ident_support/email', $storeId = null);
                            $senderName = $this->csmarketplaceHelper->getStoreConfig('trans_email/ident_support/name', $storeId = null);
                            $this->state->suspend();

                            $error = false;
                            $Vsender = [
                                'name' => $senderName,
                                'email' => $senderEmail,
                            ];

                            $storeId = $this->getStoreId();
                            /* email template */
                            $template = $this->scopeConfig->getValue(
                                self::EMAIL_TEMPLATE,
                                ScopeInterface::SCOPE_STORE,
                                $storeId
                            );
                            $transport = $this->emailSender
                                ->setTemplateIdentifier($template)// this code we have mentioned in the email_templates.xml
                                ->setTemplateOptions(
                                    [
                                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                                        'store' => \Magento\Store\Model\Store::DISTRO_STORE_ID,
                                    ]
                                )
                                ->setTemplateVars(['answer' => $description, 'name' => $name])
                                ->setFrom($Vsender)
                                ->addTo($email)
                                ->getTransport();

                            $transport->sendMessage();
                            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                            $this->state->resume();

                        } catch (\Exception $e) {

                            $this->messageManager->addErrorMessage(
                                __($e->getMessage())
                            );
                            $this->_redirect('*/*/');
                        }
                    } else {
                        $error[] = $id;
                    }

                }
                if ($vorderModel->getOrderStatus() == \Ced\CsSla\Helper\Data::STATE_CANCELLED) {
                    $error[] = $id;
                }

            }
        }

        if (empty($error)) {
            if (count($confirmIds) > count($alconfirmids)) {
                $this->messageManager->addSuccessMessage('Orders confirmed Successfully...');
            }

            if (!empty($alconfirmids)) {
                $this->messageManager->addErrorMessage('Orders with ids ' . implode(',', $alconfirmids) . ' Skipped...');
            }
            return $this->_redirect('csorder/*/index');

        } elseif (!empty($error)) {

            $this->messageManager->addErrorMessage('Orders with order ids ' . implode(',', $error) . ' can not be confirmed.');

            return $this->_redirect('csorder/*/index');

        }
    }

    /**
     * @param $id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createInvoice($id)
    {

        $error = [];
        if (isset($id)) {
            $vorderId = $id;
            $vorder = $this->vordersFactory->create()->load($vorderId);
            $orderId = $vorder->getOrder()->getId();

            $order = $this->orderFactory->create()->load($orderId);
            $invoiceItems = [];
            $items = $order->getAllItems();
            foreach ($items as $item) {
                if ($item->getVendorId() == $this->session->getVendorId()) {
                    $invoiceItems[$item->getItemId()] = round($item->getQtyOrdered());
                }
            }

            if (!$order->getId()) {
                $error['exist'][] = $id;
            }

            if (!$order->canInvoice()) {
                $error['invoice_not_created'] = $id;
            }

            $invoice = $this->invoiceService->prepareInvoice($order, $invoiceItems);

            if (!$invoice) {
                $error['invoice_not_save'] = $id;
            }

            if (!$invoice->getTotalQty()) {
                $error['invoice_without_product'] = $id;
            }

            $invoice->register();
            $invoice->getOrder()->setIsInProcess(true);

            $transactionSave = $this->transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );

            $transactionSave->save();

            try {
                $this->invoiceSender->send($invoice);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addErrorMessage(__('We can\'t send the invoice email right now.'));
            }
        }
        return;
    }
    /*
     * get Current store id
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
 
    /*
     * get Current store Info
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }
}
