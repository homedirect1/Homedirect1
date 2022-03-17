<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_GroupBuying
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GroupBuying\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\SessionFactory;

class CustomDiscount implements ObserverInterface
{

    private SessionFactory $checkoutSession;

    /**
     * Constructor
     *
     * @param SessionFactory $checkoutSession
     */
    public function __construct(
        SessionFactory $checkoutSession
    )
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * TODO
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $quoteItem = $observer->getEvent()->getQuoteItem();
        $checkoutSession = $this->checkoutSession->create();
        $writer = new \Zend\Log\Writer\Stream(BP.'/var/log/groupbuy.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($checkoutSession->getIsGroupBuy());
        if($checkoutSession->getIsGroupBuy() === true){
            $logger->info("success !!!!");
            $checkoutSession->unsIsGroupBuy();
            $logger->info($checkoutSession->getIsGroupBuy());
            $array = array_reverse($quoteItem->getProduct()->getTierPrice());
            $groupBuyPrice = array_pop($array)["price"];
            $quoteItem->setOriginalCustomPrice($groupBuyPrice);
        }
    }//end execute()


}//end class
