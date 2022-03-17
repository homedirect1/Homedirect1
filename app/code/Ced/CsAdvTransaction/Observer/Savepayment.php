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
 * @package     Ced_CsAdvTransaction
 * @author       CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class Savepayment
 * @package Ced\CsAdvTransaction\Observer
 */
class Savepayment implements ObserverInterface
{
    /**
     * @var \Ced\CsAdvTransaction\Model\SetVendorOrder
     */
    protected $setVendorOrder;

    /**
     * Savepayment constructor.
     * @param \Ced\CsAdvTransaction\Model\SetVendorOrder $setVendorOrder
     */
    public function __construct(\Ced\CsAdvTransaction\Model\SetVendorOrder $setVendorOrder)
    {
        $this->setVendorOrder = $setVendorOrder;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        $this->setVendorOrder->setVendorOrder($order);

        return $this;
    }

}
