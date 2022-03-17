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
 * @author     CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Rewardsystem\Controller\Rewardpoint;

/**
 * Class UpdateSubtotal
 * @package Ced\Rewardsystem\Controller\Rewardpoint
 */
class UpdateSubtotal extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * UpdateSubtotal constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_checkoutSession = $checkoutSession;
        $this->scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
    }

    /**
     * Load the page defined in view/frontend/layout/samplenewpage_index_index.xml
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {

            $data = $this->getRequest()->getParams();
            $usedPoint = 0;
            if (isset($data['enterpoint'])) {
                $usedPoint = $data['enterpoint'];
            }

            $discountAmount = $this->calculateDiscount($usedPoint);

            $gandTotal = $this->_checkoutSession->getQuote()->getBaseSubtotal();
            if ($discountAmount > $gandTotal) {
                $discountAmount = $gandTotal;
            }

            $this->_checkoutSession->setDiscountAmount($discountAmount);
            $this->_checkoutSession->setUsedRPoints($usedPoint);
            $this->_checkoutSession->getQuote()->collectTotals()->save();
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData($discountAmount);
            return $resultJson;
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('checkout/cart/');
            $data = $this->getRequest()->getParams();
            if ($data) {
                if (isset($data['defaultpoint']) && isset($data['enterpoint'])) {
                    $data['defaultpoint'] = intval($data['defaultpoint']);
                    $data['enterpoint'] = intval($data['enterpoint']);
                    if ($data['defaultpoint'] < $data['enterpoint']) {
                        $this->messageManager->addErrorMessage(__('Enter point must be less than or equal to Total point'));
                        return $resultRedirect;
                    }
                    $usedPoint = 0;
                    if (isset($data['remove'])) {
                        $usedPoint = 0;
                    }
                    if (isset($data['apply'])) {
                        if (isset($data['enterpoint'])) {
                            $usedPoint = $data['enterpoint'];
                        }
                    }

                    $discountAmount = $this->calculateDiscount($usedPoint);

                    $gandTotal = $this->_checkoutSession->getQuote()->getBaseSubtotal();
                    if ($discountAmount > $gandTotal) {
                        $discountAmount = $gandTotal;
                    }

                    $this->_checkoutSession->setDiscountAmount($discountAmount);
                    $this->_checkoutSession->setUsedRPoints($usedPoint);
                    $this->_checkoutSession->getQuote()->collectTotals()->save();
                }
            }
            return $resultRedirect;
        }
    }

    /**
     * @param $data
     * @return float|int
     */
    public function calculateDiscount($data)
    {
        $pointPriceRate = $this->scopeConfig->getValue('reward/setting/point_value', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $definePoint = $this->scopeConfig->getValue('reward/setting/point', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $definePoint = !empty((int)$definePoint) ? (int)$definePoint : 1;

        $calculate = $pointPriceRate / $definePoint;
        $discount = $data * $calculate;
        $discount = $this->convertPriceRate($discount);
        return $discount;
    }

    /**
     * @param int $amount
     * @param null $store
     * @param null $currency
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function convertPriceRate($amount = 0, $store = null, $currency = null)
    {
        $currentPriceCurrency = $this->priceCurrency;
        $storeManager = $this->storeManager;
        if ($store == null) {
            $store = $storeManager->getStore()->getStoreId();
            $priceRate = $currentPriceCurrency->convert($amount, $store, $currency);
            return $priceRate;
        }
    }
}
