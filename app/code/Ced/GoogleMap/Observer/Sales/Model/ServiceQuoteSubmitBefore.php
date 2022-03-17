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
 * @package     Ced_GoogleMap
 * @author 	    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */


namespace Ced\GoogleMap\Observer\Sales\Model;

use Magento\Framework\Event\Observer;

class ServiceQuoteSubmitBefore implements \Magento\Framework\Event\ObserverInterface
{


    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/shipping.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(__FILE__);

        try {
            if ($quote->getBillingAddress()) {
                $order->getBillingAddress()->setLongitude($quote->getBillingAddress()->getLongitude());
                $order->getBillingAddress()->setLatitude($quote->getBillingAddress()->getLatitude());
            }

            if (!$quote->isVirtual()) {
                $order->getShippingAddress()->setLongitude($quote->getShippingAddress()->getLongitude());
                $order->getShippingAddress()->setLatitude($quote->getShippingAddress()->getLatitude());
            }
        } catch (\Exception $e) {
            $logger->info($e->getMessage());
        }

        return $this;
    }
}
