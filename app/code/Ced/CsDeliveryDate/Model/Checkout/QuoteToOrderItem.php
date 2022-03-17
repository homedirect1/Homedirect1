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
 * @package     Ced_CsDeliveryDate
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsDeliveryDate\Model\Checkout;
/*
 * used to save data in Quote To OrderItem table
 * */
class QuoteToOrderItem
{
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ){
        $orderItem = $proceed($item, $additional);
        $orderItem->setCsDeliverydate($item->getCsDeliverydate());
        $orderItem->setCsDeliverycomment($item->getCsDeliverycomment());
        $orderItem->setCsTimestamp($item->getCsTimestamp());
        return $orderItem;
    }
}