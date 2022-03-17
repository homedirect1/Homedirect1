<?php

/**
 * Created by PhpStorm.
 * User: cedcoss
 * Date: 13/11/18
 * Time: 5:41 PM
 */

namespace Ced\Rewardsystem\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CancelOrder implements ObserverInterface
{
    public $serialize;
    public $pointCollectionFactory;

    /**
     * @var \Ced\Rewardsystem\Helper\Data
     */
    public $cedHelper;

    public function __construct(
        \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint\CollectionFactory $pointCollectionFactory,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize,
        \Ced\Rewardsystem\Helper\Data $cedHelper
    ) {
        $this->serialize = $serialize;
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
            $order = $observer->getEvent()->getOrder();

            $orderId = $order->getId();
            if ($orderId) {
                $pointModel =  $this->pointCollectionFactory->create()->addFieldToFilter('order_id', $orderId)->getFirstItem();
            }
            $refundOnCancelOrder = $this->cedHelper->getStoreConfig('reward/setting/refund_on_cancel', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            if ($order->hasShipments() || $order->hasInvoices()) {
                $pointReversed = 0;
                $totalAmountToCancel = 0;
                $baseTotalAmountToCancel = 0;
                foreach ($order->getItems() as $item) {
                    if ($item->getQtyCanceled()) {
                        $priceofOneQty = ($item->getRowTotal() - $item->getDiscountAmount()) / $item->getQtyOrdered();
                        $basePriceofOneQty = ($item->getBaseRowTotal() - $item->getBaseDiscountAmount()) / $item->getQtyOrdered();
                        $amountToCancel = round($priceofOneQty) * $item->getQtyCanceled();
                        $baseAmountToCancel = round($basePriceofOneQty) * $item->getQtyCanceled();
                        $totalAmountToCancel += $amountToCancel;
                        $baseTotalAmountToCancel += $baseAmountToCancel;
                    }
                    if ($item->getProductType() == 'bundle' || $item->getProductType() == 'configurable') {
                        continue;
                    }
                    if ($item->getQtyCanceled()) {
                        if ($item->getCedRpoint()) {
                            $pointReversed += intval($item->getQtyCanceled()) * $item->getCedRpoint();
                        }
                    }
                }

                if ($pointReversed > 0) {
                    if ($orderId) {
                        if ($pointModel->getId()) {
                            $point = $pointModel->getPoint();
                            $netPoint = $point - $pointReversed;
                            if ($netPoint < 0) {
                                $netPoint = 0;
                            }
                            $pointModel->setPoint($netPoint);
                            $pointModel->setStatus($order->getStatus());
                            $pointModel->save();
                        }
                    }
                }
                if ($refundOnCancelOrder == '1') {
                    $baseRewardAmount = $order->getRewardsystemBaseAmount();
                    $baseRewardInvoiced =  $order->getBaseRewardsystemAmountInvoiced();
                    $rewardAmount = $order->getRewardsystemDiscount();
                    $rewardInvoiced =  $order->getRewardsystemAmountInvoiced();

                    $rewardBaseRefunded = $order->getBaseRewardsystemAmountRefunded();
                    $rewardRefunded = $order->getRewardsystemAmountRefunded();

                    $baseAmountToRefund = $baseRewardAmount - $baseRewardInvoiced;
                    $amountToRefund = $rewardAmount - $rewardInvoiced;
                    if ($baseAmountToRefund > $baseTotalAmountToCancel) {
                        $baseAmountToRefund = $baseTotalAmountToCancel;
                    }
                    if ($amountToRefund > $totalAmountToCancel) {
                        $amountToRefund = $totalAmountToCancel;
                    }
                    $order->setBaseRewardsystemAmountRefunded($rewardBaseRefunded + $baseAmountToRefund);
                    $order->setRewardsystemAmountRefunded($rewardRefunded + $amountToRefund);

                    $rewardBalance = $rewardAmount - $rewardRefunded;
                    $rewardBalance = round($rewardBalance);
                    if ($pointModel->getId()) {
                        $pointUsedInOrder = $pointModel->getPointUsed();
                        if ($pointUsedInOrder) {
                            $pointValue = $rewardBalance / $pointUsedInOrder;
                            $pointtoReturn = $amountToRefund / $pointValue;
                            $pointModel->setPointUsed($pointUsedInOrder - $pointtoReturn);
                            $pointModel->save();
                        }
                    }
                }
            } else {
                if ($orderId) {
                    if ($pointModel->getId()) {
                        if ($refundOnCancelOrder == '1') {
                            $pointModel->setPointUsed(0);
                        }
                        $pointModel->setStatus($order->getStatus());
                        $pointModel->save();
                    }
                }
            }

            /*
                 * no redemption process as the display manages non cancelled and cancelled orders
                 * redemption process = set redeemed point in order to handle partial approval or cancellation
                 * if expired points was used and expiration date has passed then no redemption
                //get item details for points
                $item_details = $this->serialize->unserialize($model->getItemDetails());
                $item_details = is_array($item_details) && !empty($item_details) ? array_column( $item_details, 'point', 'id') : [];

                foreach( $order->getAllItems() as $key => $item ) {
                    if ( !empty($item->getData()) )
                        $order_items[] = $item->getData();
                }

                //get count of cancelled items
                $cancelled_items = array_sum( array_column($order_items, 'qty_canceled') );
                //get total qty
                $total_qty_ordered = $order->getData('total_qty_ordered');

                if( $cancelled_items == $total_qty_ordered ){
                    $price_status = 'cancelled';
                     $model->setReceivedPoint( $model->getPointUsed );
                } */

            /* // required when partial shipment and partial cancellation is allowed
                if( !$cancelled_items ){
                    foreach( $order_items as $key => $item ) {
                        if( !empty($item->getData()) && $item->getData('qty_shipped') == $item->getData('qty_ordered') && array_key_exists( $item->getData('product_id'), $item_details )){
                            $point_val += $item_details[$item->getData('product_id')];
                        }
                    }
                }*/
            // }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }
}
