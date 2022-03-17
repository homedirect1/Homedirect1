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
 * @package   Ced_CsAdvTransaction
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Block\Order\View;

/**
 * Class Totals
 * @package Ced\CsAdvTransaction\Block\Order\View
 */
class Totals extends \Ced\CsMarketplace\Block\Vorders\View\Totals
{
    /**
     * @var
     */
    public $storeManager;

    /**
     * @var \Magento\Framework\Locale\Currency
     */
    public $currency;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    public $priceCurrency;

    /**
     * @var \Ced\CsAdvTransaction\Helper\Data
     */
    public $advHelper;

    /**
     * @var \Ced\CsAdvTransaction\Model\OrderfeeFactory
     */
    public $orderfeeFactory;

    /**
     * @var \Ced\CsOrder\Helper\Data
     */
    public $csorderHelper;

    /**
     * Totals constructor.
     * @param \Magento\Framework\Locale\Currency $currency
     * @param \Ced\CsAdvTransaction\Helper\Data $advHelper
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Ced\CsAdvTransaction\Model\OrderfeeFactory $orderfeeFactory
     * @param \Ced\CsOrder\Helper\Data $csorderHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Ced\CsMarketplace\Model\VordersFactory $vordersFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Locale\Currency $currency,
        \Ced\CsAdvTransaction\Helper\Data $advHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Ced\CsAdvTransaction\Model\OrderfeeFactory $orderfeeFactory,
        \Ced\CsOrder\Helper\Data $csorderHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Ced\CsMarketplace\Model\VordersFactory $vordersFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->currency = $currency;
        $this->advHelper = $advHelper;
        $this->priceCurrency = $priceCurrency;
        $this->orderfeeFactory = $orderfeeFactory;
        $this->csorderHelper = $csorderHelper;

        parent::__construct($context, $registry, $vordersFactory, $storeManager);
    }
}
