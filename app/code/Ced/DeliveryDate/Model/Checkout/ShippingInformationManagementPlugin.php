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
 * @package     Ced_DeliveryDate
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\DeliveryDate\Model\Checkout;

class ShippingInformationManagementPlugin extends \Magento\Checkout\Model\ShippingInformationManagement
{

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforesaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    )
    {
        $extAttributes = $addressInformation->getExtensionAttributes();

        if (isset($extAttributes)) {
            $deliveryDate = $extAttributes->getDeliveryDate() ? $extAttributes->getDeliveryDate() : '';
            $deliveryComment = $extAttributes->getDeliveryComment() ? $extAttributes->getDeliveryComment() : '';
            $timestamp = $extAttributes->getTimestamp() ? $extAttributes->getTimestamp() : '';

            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->getActive($cartId);

            $quote->setData('cedDeliveryDate', $deliveryDate);
            $quote->setData('cedDeliveryComment', $deliveryComment);
            $quote->setData('cedTimestamp', $timestamp);

            try {
                $this->quoteRepository->save($quote);

            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
    }

}