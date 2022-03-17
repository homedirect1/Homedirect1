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
 * @package     Ced_CsDeliveryDate
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeliveryDate\Model\Checkout;

use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Model\QuoteAddressValidator;
use Magento\Quote\Model\ShippingAssignmentFactory;
use Magento\Quote\Model\ShippingFactory;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class ShippingInformationManagementPlugin
 * @package Ced\CsDeliveryDate\Model\Checkout
 */
class ShippingInformationManagementPlugin extends \Magento\Checkout\Model\ShippingInformationManagement
{
    /**
     * @var \Ced\CsDeliveryDate\Helper\ConfigData
     */
    protected $configDataHelper;

    /**
     * @var \Ced\CsMarketplace\Model\VproductsFactory
     */
    protected $vproductsFactory;

    /**
     * ShippingInformationManagementPlugin constructor.
     * @param \Ced\CsDeliveryDate\Helper\ConfigData $configDataHelper
     * @param \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param QuoteAddressValidator $addressValidator
     * @param Logger $logger
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
     * @param CartExtensionFactory|null $cartExtensionFactory
     * @param ShippingAssignmentFactory|null $shippingAssignmentFactory
     * @param ShippingFactory|null $shippingFactory
     */
    public function __construct(
        \Ced\CsDeliveryDate\Helper\ConfigData $configDataHelper,
        \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        QuoteAddressValidator $addressValidator,
        Logger $logger,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        CartExtensionFactory $cartExtensionFactory = null,
        ShippingAssignmentFactory $shippingAssignmentFactory = null,
        ShippingFactory $shippingFactory = null
    )
    {
        parent::__construct(
            $paymentMethodManagement,
            $paymentDetailsFactory,
            $cartTotalsRepository,
            $quoteRepository,
            $addressValidator,
            $logger,
            $addressRepository,
            $scopeConfig,
            $totalsCollector,
            $cartExtensionFactory,
            $shippingAssignmentFactory,
            $shippingFactory
        );

        $this->configDataHelper = $configDataHelper;
        $this->vproductsFactory = $vproductsFactory;
    }


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
        $enableModule = ($this->configDataHelper->moduleEnabled() == 1);
        if ($enableModule) {
            $extAttributes = $addressInformation->getExtensionAttributes();
            /*check if parameter exist in extension attribute then save it afteer json decode*/
            $deliveryDate = $extAttributes->getCsdeliveryDate() ? json_decode($extAttributes->getCsdeliveryDate(), true) : '';
            $deliveryComment = $extAttributes->getCsdeliveryComment() ? json_decode($extAttributes->getCsdeliveryComment(), true) : '';
            $timestamp = $extAttributes->getCstimestamp() ? json_decode($extAttributes->getCstimestamp(), true) : '';
            $vendorId = $extAttributes->getCsVendorId() ? json_decode($extAttributes->getCsVendorId(), true) : [];

            $ddData = [];
            /*
             * vendor_id = 0 indicates the shipping method data comes from admin products
             * */
            foreach ($vendorId as $k => $v) {
                $ddData[$k]['vendorId'] = (isset($v)) ? $vendorId[$k] : 0;
                if (isset($deliveryDate[$k])) {
                    $ddData[$k]['deliveryDate'] = $deliveryDate[$k];
                }
                if (isset($deliveryComment[$k])) {
                    $ddData[$k]['deliveryComment'] = $deliveryComment[$k];
                }
                if (isset($timestamp[$k])) {
                    $ddData[$k]['timestamp'] = $timestamp[$k];
                }
            }
            /** model to get vendor_id from product_id
             * @var \Ced\CsMarketplace\Model\ResourceModel\Vproducts\Collection
             */
            $vpId = $this->vproductsFactory->create();

            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->getActive($cartId);
            $product = [];
            foreach ($quote->getAllItems() as $item) {

                $vId = $vpId->getCollection()->addFieldToFilter('product_id', $item->getProductId())->getData();
                $vId = (isset($vId[0]['vendor_id'])) ? $vId[0]['vendor_id'] : 0;

                foreach ($ddData as $k => $v) {
                    $date = isset($v['deliveryDate']) ? $v['deliveryDate'] : '';
                    $comment = isset($v['deliveryComment']) ? $v['deliveryComment'] : '';
                    $timestamp = isset($v['timestamp']) ? $v['timestamp'] : '';
                    $product[] = $item->getProductId();
                    if ($v['vendorId'] == 0 && !in_array($vId, $vendorId)) {

                        $item->setCsDeliverycomment($comment);
                        $item->setCsTimestamp($timestamp);
                        $item->setCsDeliverydate($date);
                        $item->save();

                    } elseif ($vId == $v['vendorId']) {

                        $item->setCsDeliverycomment($comment);
                        $item->setCsTimestamp($timestamp);
                        $item->setCsDeliverydate($date);
                        $item->save();
                    }
                }
            }
            try {

                $this->quoteRepository->save($quote);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new InputException(__('Unable to save shipping information. Please check input data.'));
            }
        }
    }

}