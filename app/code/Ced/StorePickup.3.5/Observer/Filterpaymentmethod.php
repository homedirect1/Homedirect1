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
 * @category  Ced
 * @package   Ced_StorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\StorePickup\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Filterpaymentmethod
 * @package Ced\StorePickup\Observer
 */
class Filterpaymentmethod implements ObserverInterface
{
    const XML_PATH_ALLOWED_PAYMENT_METHODS = "carriers/storepickupshipping/allowed_payment_methods";

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeconfig;
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * Filterpaymentmethod constructor.
     * @param Session $checkoutSession
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Session $checkoutSession,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeconfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $method = $observer->getEvent()->getMethodInstance();
        $result = $observer->getEvent()->getResult();
        $quote = $this->checkoutSession->getQuote();
        $shippingMethod = $this->getShippingMethod($quote);
        $isStorePickup = strpos($shippingMethod, 'storepickupshipping_storepickupshipping');

        $showPaymentMethods = $this->scopeconfig->getValue(self::XML_PATH_ALLOWED_PAYMENT_METHODS);

        $showPaymentMethods = explode(',', $showPaymentMethods);
        if (!empty($showPaymentMethods) && $isStorePickup) {
            if (!in_array($method->getCode(), $showPaymentMethods)) {
                $result->setData('is_available', false);
            } else {
                $result->setData('is_available', true);
            }
        }
    }

    /**
     * @param $quote
     * @return mixed
     */
    private function getShippingMethod($quote)
    {
        if ($quote) {
            return $quote->getShippingAddress()->getShippingMethod();
        }
        return '';
    }
}
