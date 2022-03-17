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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class Discount
 * @package Ced\Affiliate\Observer
 */
Class Discount implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * Discount constructor.
     * @param \Magento\Checkout\Model\Session $session
     */
    public function __construct(
        \Magento\Checkout\Model\Session $session
    )
    {
        $this->session = $session;
    }

    /**
     *Product Assignment Tab
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $discount = $this->session->getCustomdiscount();
        $orderData = $observer->getEvent()->getOrder();
        $orderData->setCustomdiscount($discount);
        $this->session->unsCustomdiscount();
    }
}    

