<?php
/**
 * Webkul Software.
 *
 * @category   Webkul
 * @package    Webkul_Recurring
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Controller\Adminhtml\System;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class GenerateWebHook extends \Magento\Backend\App\Action
{
    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Url $urlHelper
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Webkul\Recurring\Helper\Stripe $stripeHelper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Url $urlHelper,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Webkul\Recurring\Helper\Stripe $stripeHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $messageManager;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->configWriter = $configWriter;
        $this->storeManager = $storeManager;
        $this->urlHelper = $urlHelper;
        $this->stripeHelper = $stripeHelper;
        parent::__construct($context);
    }
    
    /**
     *  To create webhooks on Stripe
     *
     * @return json data
     */
    public function execute()
    {
        $resultJson = $this->jsonResultFactory->create();
        $webHookId = $this->scopeConfig->getValue(
            'payment/recurringstripe/webhook_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $secretkey = $this->scopeConfig->getValue(
            'payment/recurringstripe/api_secret_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$webHookId || !$secretkey) {
            \Stripe\Stripe::setApiKey($this->stripeHelper->getConfigValue('api_secret_key'));
            
            // \Stripe\Stripe::setAppInfo(
            //     "Webkul Recurring Payments & Subscription for Magento 2",
            //     "3.0.1",
            //     "https://store.webkul.com/magento2-recurring-subscription.html",
            //     "pp_partner_FLJSvfbQDaJTyY"
            // );
            // \Stripe\Stripe::setApiVersion("2019-12-03");
            $storeId = $this->storeManager->getDefaultStoreView()->getStoreId();

            $webHookResponse = \Stripe\WebhookEndpoint::create([
                "url" => $this->urlHelper->getBaseUrl().'recurring/subscription/webhook',
                "enabled_events" => [
                    "checkout.session.completed",
                    "invoice.payment_succeeded"
                ]
            ]);
            if ($webHookResponse['id']) {
                $this->configWriter->save(
                    'payment/recurringstripe/webhook_id',
                    $webHookResponse['id'],
                    $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    $scopeId = \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                $response['error'] = 0;
                $message = __('WebHooks Generated Successfully');
                $this->messageManager->addSuccess($message);
            } else {
                $response['error'] = 1;
                $message = __('Invalid Request Check Credentials');
                $this->messageManager->addError($message);
            }
            
            return $resultJson->setData($response);
        } elseif ($webHookId) {
            $response['error'] = 1;
            $message = __('WebHooks Already Generated');
            $this->messageManager->addSuccess($message);
            return $resultJson->setData($response);
        } else {
            $response['error'] = 1;
            $message = __('Invalid Request Check Credentials');
            $this->messageManager->addError($message);
            return $resultJson->setData($response);
        }
    }
}
