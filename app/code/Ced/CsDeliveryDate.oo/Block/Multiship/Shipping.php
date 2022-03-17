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
 * @category  Ced
 * @package   Ced_CsDeliveryDate
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeliveryDate\Block\Multiship;


/**
 * Class Shipping
 * @package Ced\CsDeliveryDate\Block\Multiship
 */
class Shipping extends \Magento\Multishipping\Block\Checkout\Shipping
{
    /**
     * @var \Ced\CsMultiShipping\Helper\Data
     */
    protected $multishippingHelper;

    /**
     * @var \Ced\CsMarketplace\Model\VendorFactory
     */
    protected $vendorFactory;

    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $marketplaceHelper;

    /**
     * Shipping constructor.
     * @param \Ced\CsMultiShipping\Helper\Data $multishippingHelper
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param \Ced\CsMarketplace\Helper\Data $marketplaceHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Filter\DataObject\GridFactory $filterGridFactory
     * @param \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Ced\CsMultiShipping\Helper\Data $multishippingHelper,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Ced\CsMarketplace\Helper\Data $marketplaceHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Filter\DataObject\GridFactory $filterGridFactory,
        \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    )
    {
        parent::__construct($context, $filterGridFactory, $multishipping, $taxHelper, $priceCurrency, $data);
        $this->multishippingHelper = $multishippingHelper;
        $this->vendorFactory = $vendorFactory;
        $this->marketplaceHelper = $marketplaceHelper;
    }


    /**
     * @return \Magento\Multishipping\Block\Checkout\Shipping
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _prepareLayout()
    {
        if (!$this->multishippingHelper->isEnabled()) {
            $this->setTemplate('Magento_Multishipping::checkout/shipping.phtml');
        }
        return parent::_prepareLayout();
    }

    /**
     * @param $address
     * @return array
     */
    public function getSelectedMethod($address)
    {
        $selectedMethod = str_replace("vendor_rates_", '', $address->getShippingMethod());
        $selectedMethods = explode(\Ced\CsMultiShipping\Model\Shipping::METHOD_SEPARATOR, $selectedMethod);
        return $selectedMethods;
    }

    /**
     * @param Address $address
     * @return mixed
     */
    public function getShippingRates($address)
    {
        if (!$this->multishippingHelper->isEnabled()) {
            return parent::getShippingRates($address);
        }
        $groups = $address->getGroupedAllShippingRates();

        $rates = array();
        foreach ($groups as $code => $_rates) {
            if ($code == 'vendor_rates') {
                foreach ($_rates as $rate) {
                    if (!$rate->isDeleted()) {
                        if (!isset($rates[$rate->getCarrier()])) {
                            $rates[$rate->getCarrier()] = array();
                        }
                        $rates[$rate->getCarrier()][] = $rate;
                    }
                }
            }
        }
        return $rates;
    }

    /**
     * @param $address
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRatesByVendor($address)
    {

        $addrs_mthd = $address->getGroupedAllShippingRates();
        $groups = array();

        foreach ($addrs_mthd as $code => $rateCollection) {
            foreach ($rateCollection as $rate) {
                if ($rate->isDeleted()) {
                    continue;
                }
                if ($rate->getCarrier() == 'vendor_rates') {
                    continue;
                }

                $tmp = explode(\Ced\CsMultiShipping\Model\Shipping::SEPARATOR, $rate->getCode());

                $vendorId = isset($tmp[1]) ? $tmp[1] : "admin";
                $vendor = $this->vendorFactory->create();
                if ($vendorId && $vendorId != "admin") {
                    $vendor = $vendor->load($vendorId);
                }

                if (!isset($groups[$vendorId])) {
                    $groups[$vendorId] = array();
                }

                $groups[$vendorId]['title'] = $vendor->getId() ? $vendor->getPublicName() : $this->marketplaceHelper->getStore()->getWebsite()->getName();

                if (!isset($groups[$vendorId]['rates'])) {
                    $groups[$vendorId]['rates'] = array();
                }
                $groups[$vendorId]['rates'][] = $rate;
            }
        }
        return $groups;
    }


}
