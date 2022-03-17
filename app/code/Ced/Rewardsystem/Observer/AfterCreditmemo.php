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
 * @package     Ced_Rewardsystem
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Rewardsystem\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AfterCreditmemo implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    public $serialize;

    /**
     * @var \Ced\Rewardsystem\ResourceModel\Regisuserpoint\CollectionFactory
     */
    public $pointCollectionFactory;

    /**
     * @var \Ced\Rewardsystem\Helper\Data
     */
    public $cedHelper;

    public function __construct(
        \Ced\Rewardsystem\Model\RegisuserpointFactory $point,
        \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint $pointResource,
        \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint\CollectionFactory $pointCollectionFactory,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize,
        \Ced\Rewardsystem\Helper\Data $cedHelper
    ) {
        $this->serialize = $serialize;
        $this->point = $point;
        $this->pointResource = $pointResource;
        $this->pointCollectionFactory = $pointCollectionFactory;
        $this->cedHelper = $cedHelper;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        try {
            $collection = $observer->getEvent()->getCreditmemo();
            $order = $observer->getEvent()->getCreditmemo()->getOrder();
            $orderId = $order->getId();

            $rewardsystemBaseAmount = $order->getRewardsystemBaseAmount();
            $rewardsystemDiscount = $order->getRewardsystemDiscount();

            $rewardBaseRefunded = $order->getBaseRewardsystemAmountRefunded();
            $rewardRefunded = $order->getRewardsystemAmountRefunded();

            $baseRewardBalance = $rewardsystemBaseAmount - $rewardBaseRefunded;
            $baseRewardBalance = round($baseRewardBalance);
            $rewardBalance = $rewardsystemDiscount - $rewardRefunded;
            $rewardBalance = round($rewardBalance);
            $baseAmounttoReturn = $collection->getBaseDiscountAmount();
            $amounttoReturn = $collection->getDiscountAmount();

            if ($orderId) {
                $pointModel = $this->point->create()->load($orderId, 'order_id');
                if ($pointModel->getId()) {
                    $pointUsedInOrder = $pointModel->getPointUsed();
                    if ($pointUsedInOrder) {
                        $pointValue = $rewardBalance / $pointUsedInOrder;
                        $pointtoReturn = $amounttoReturn / $pointValue;
                        $pointModel->setPointUsed($pointUsedInOrder - $pointtoReturn);
                        $this->pointResource->save($pointModel);
                    }
                }
            }

            $order->setBaseRewardsystemAmountRefunded($baseAmounttoReturn);
            $order->setRewardsystemAmountRefunded($amounttoReturn);

            $pointReversed = 0;
            foreach ($order->getAllItems() as $item) {
                if ($item->getProductType() == 'bundle' || $item->getProductType() == 'configurable') {
                    continue;
                }
                foreach ($collection->getAllItems() as $value) {
                    if ($item->getItemId() == $value['order_item_id']) {
                        $creditmemoQty = $value['qty'];
                        if ($item->getCedRpoint()) {
                            $pointReversed += $creditmemoQty * $item->getCedRpoint();
                        }
                        break;
                    }
                }
            }

            if ($pointReversed > 0) {
                if ($orderId) {
                    $pointModel = $this->point->create()->load($orderId, 'order_id');
                    if ($pointModel->getId()) {
                        $point = $pointModel->getPoint();
                        $netPoint = $point - $pointReversed;
                        if ($netPoint < 0) {
                            $netPoint = 0;
                        }
                        $pointModel->setPoint($netPoint);
                        $this->pointResource->save($pointModel);
                    }
                }
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }
}
