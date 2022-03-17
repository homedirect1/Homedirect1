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
namespace Webkul\Recurring\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Webkul\Recurring\Helper\Stripe as StripeHelper;

/**
 * Webkul MpStripe PreDispatchConfigSaveObserver Observer.
 */
class PreDispatchConfigSaveObserver implements ObserverInterface
{
    /**
     * @var ManagerInterface
     */
    private $_messageManager;

    /**
     * @var StripeHelper
     */
    private $stripeHelper;

    protected $storeManager;

    /**
     * @param ManagerInterface $messageManager
     * @param StripeHelper $stripeHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     */
    public function __construct(
        ManagerInterface $messageManager,
        StripeHelper $stripeHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
    ) {
        $this->_messageManager = $messageManager;
        $this->stripeHelper = $stripeHelper;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
        try {
            $observerRequestData = $observer['request'];
            $params = $observerRequestData->getParams();
            
            if ($params['section'] == 'payment') {
                $currentDebugMode = $params['groups']['recurringstripe']['fields']['sandbox']['value'];
                $previousDebugMode = $this->getConfig('payment/recurringstripe/debug');
                if (($previousDebugMode != '') && ($previousDebugMode != $currentDebugMode)) {
                    $webhookId = $this->getConfig('payment/recurringstripe/webhook_id');
                    if ($webhookId != '') {

                        $webhookEndpoint = \Stripe\WebhookEndpoint::retrieve(
                            $webhookId
                        );
                        $webhookEndpoint->delete();
                    }
                    $this->configWriter->save('payment/recurringstripe/webhook_id', '');
                }
            }
        } catch (\Exception $e) {
            $this->_messageManager->addError($e->getMessage());
        }
    }

    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
