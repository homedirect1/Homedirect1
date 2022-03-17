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
 * Class Amount
 * @package Ced\Affiliate\Observer
 */
Class Amount implements ObserverInterface
{
    /**
     * @var \Ced\Affiliate\Model\AffiliateWalletFactory
     */
    protected $affiliateWalletFactory;

    /**
     * Amount constructor.
     * @param \Ced\Affiliate\Model\AffiliateWalletFactory $affiliateWalletFactory
     */
    public function __construct(
        \Ced\Affiliate\Model\AffiliateWalletFactory $affiliateWalletFactory
    )
    {
        $this->affiliateWalletFactory = $affiliateWalletFactory;
    }

    /**
     *Product Assignment Tab
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order->getPayment()->getMethodInstance()->getCode() == "storecredit") {
            if (!$order->getCustomerIsGuest()) {
                $model = $this->affiliateWalletFactory->create()->load($order->getCustomerId(), 'customer_id');
                if (!empty($model->getData())) {
                    $model->setUsedAmount($model->getUsedAmount() + $order->getGrandTotal());
                    $model->setRemainingAmount($model->getRemainingAmount() - $order->getGrandTotal());
                    $model->save();
                }
            }
        }
        return $this;
    }
}    

