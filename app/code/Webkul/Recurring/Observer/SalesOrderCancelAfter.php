<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_Recurring
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\Recurring\Observer;

use Magento\Framework\Event\ObserverInterface;
use Webkul\Recurring\Model\Subscriptions;

class SalesOrderCancelAfter implements ObserverInterface
{
    /**
     * @var subscriptions
     */
    private $subscriptions;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @param Subscriptions $subscriptions
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        Subscriptions $subscriptions,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->jsonHelper      = $jsonHelper;
        $this->subscriptions    = $subscriptions;
    }

    /**
     * Observer action for Sales order cancel after.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderId = $observer->getOrder()->getId();
        $subscriptionsCol = $this->subscriptions->getCollection();
        $subscriptionsCol->addFieldToFilter('order_id', $orderId);
        foreach ($subscriptionsCol as $model) {
            $this->setStatus($model, $model->getId());
        }
    }

    /**
     * Updates the status of the subscription
     *
     * @param object $model
     * @param integer $id
     * @return void
     */
    private function setStatus($model, $id)
    {
        $model->setStatus(false);
        $model->setId($id);
        $model->save();
    }
}
