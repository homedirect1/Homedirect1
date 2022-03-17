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

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Saveorderpoint
 * @package Ced\Rewardsystem\Observer
 */
class Saveorderpoint implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    public $serialize;

    /**
     * @var \Ced\Rewardsystem\Helper\Data
     */
    public $cedHelper;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Ced\Rewardsystem\Model\RegisuserpointFactory
     */
    protected $regisuserpointFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer
     */
    protected $_customerResource;

    /**
     * Saveorderpoint constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Serialize\Serializer\Serialize $serialize
     * @param \Ced\Rewardsystem\Helper\Data $cedHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Ced\Rewardsystem\Model\RegisuserpointFactory $regisuserpointFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer $customerResource
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize,
        \Ced\Rewardsystem\Helper\Data $cedHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Ced\Rewardsystem\Model\RegisuserpointFactory $regisuserpointFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\ResourceModel\Customer $customerResource
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_customerResource = $customerResource;
        $this->scopeConfig = $scopeConfig;
        $this->_checkoutSession = $checkoutSession;
        $this->serialize = $serialize;
        $this->cedHelper = $cedHelper;
        $this->productFactory = $productFactory;
        $this->regisuserpointFactory = $regisuserpointFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * custom event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            $usedpoint = $this->calculate($this->_checkoutSession->getDiscountAmountUsed());
            $totalPointGained = $this->_checkoutSession->getFinalConditionpoint();

            $order->getId();
            $orderDetail = $order;
            $saveOrderDetail = $order->getData();

            $rewardCalculateBy = $this->cedHelper->getStoreConfig('reward/purchase/reward_calculate_by', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            //get all items
            foreach ($order->getAllItems() as $item) {
                if ($item->getProductType() == Configurable::TYPE_CODE) {
                    continue;
                }
                $initializePoint = $this->cedHelper->getStoreConfig('reward/purchase/initial_product_point', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                if ($initializePoint) {
                    $pointApplyBy = $this->cedHelper->getStoreConfig('reward/purchase/initial_point_option', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    $point = $this->cedHelper->getStoreConfig('reward/purchase/initial_point_value', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    if ($pointApplyBy == 'fixed') {
                        $sameProductPoint = $point;
                    } elseif ($pointApplyBy == 'percentage') {
                        $sameProductPoint = floor(($item->getPrice() * $point) / 100);
                    }
                } else {
                    $sameProductPoint = 0;
                }

                $productCollection = $this->productFactory->create();
                $collection = $productCollection->load($item->getProductId());
                $point_val = !empty($collection->getCedRpoint()) ? $collection->getCedRpoint() : $sameProductPoint;
                $product_details[] = ['id' => $item->getProductId(), 'point' => $point_val];

                if ($item->getProductType() == 'bundle') {
                    continue;
                }
                if ($rewardCalculateBy == 'catalog') {
                    $point = $collection->getCedRpoint();
                    if ($point !== '0') {
                        if ($point) {
                            $item->setCedRpoint($point);
                        } elseif ($sameProductPoint) {
                            $item->setCedRpoint($sameProductPoint);
                        }
                    } else {
                        $item->setCedRpoint(0);
                    }
                }
            }
            //item details
            $item_serialize_detail = $this->serialize->serialize($product_details);

            $store = $this->scopeConfig;
            $referEnable = $store->getValue('reward/referral/referral_enable');
            if ($referEnable) {
                $firstpurchase = $store->getValue('reward/setting/first_ref_purchase');
                $anypurchase = $store->getValue('reward/setting/ref_purchase');
            } else {
                $firstpurchase = 0;
                $anypurchase = 0;
            }

            if ($saveOrderDetail['customer_id']) {
                $this->_checkoutSession->unsDiscountAmount();
                $this->_checkoutSession->unsUsedRPoints();
                $this->_checkoutSession->unsDiscountAmountUsed();

                if ($totalPointGained || $usedpoint) {
                    $model = $this->regisuserpointFactory->create();
                    $model->setCustomerId($saveOrderDetail['customer_id']);
                    $model->setPoint((int)$totalPointGained);
                    $model->setTitle('Received Rewardpoint for Ordered successfully');
                    $model->setCreatingDate($order->getCreatedAt());
                    $model->setStatus($saveOrderDetail['status']);
                    $model->setPointUsed((int)$usedpoint);
                    $model->setOrderId($order->getId());
                    $model->setItemDetails($item_serialize_detail);
                    $model->save();
                    $this->_checkoutSession->unsFinalConditionpoint();
                }
                if ($firstpurchase || $anypurchase) {
                    $_orders = $this->orderCollectionFactory->create()
                        ->addFieldToFilter('customer_id', $saveOrderDetail['customer_id'])->getData();
                    $parentId = $this->regisuserpointFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('is_register', 1)
                        ->addFieldToFilter('customer_id', $saveOrderDetail['customer_id'])
                        ->getFirstItem()
                        ->getParentCustomer();

                    if ($orderDetail->getStatus() == "complete") {
                        if ($parentId) {
                            $customerModel = $this->_customerFactory->create();
                            $this->_customerResource->load($customerModel, $parentId);
                            if (count($_orders) == 1) {
                                $rcmodel = $this->regisuserpointFactory->create();
                                if ($customerModel->getId()) {
                                    $rcmodel->setTitle('Your referral ' . $customerModel->getEmail() . ' has made the first purchase.');
                                } else {
                                    $rcmodel->setTitle('Received Rewardpoint due to referral purchase');
                                }
                                $rcmodel->setCreatingDate($order->getCreatedAt());
                                $rcmodel->setUpdatedAt($order->getCreatedAt());
                                $rcmodel->setStatus('complete');
                                $rcmodel->setPoint($firstpurchase);
                                $rcmodel->setReceivedPoint($firstpurchase);
                                $rcmodel->setCustomerId($parentId);
                                $rcmodel->save();
                            } else {
                                $rcmodel = $this->regisuserpointFactory->create();
                                if ($customerModel->getId()) {
                                    $rcmodel->setTitle('Your referral ' . $customerModel->getEmail() . ' has made a purchase.');
                                } else {
                                    $rcmodel->setTitle('Received Rewardpoint due to referral purchase');
                                }
                                $rcmodel->setCreatingDate($order->getCreatedAt());
                                $rcmodel->setUpdatedAt($order->getCreatedAt());
                                $rcmodel->setStatus('complete');
                                $rcmodel->setPoint($anypurchase);
                                $rcmodel->setReceivedPoint($anypurchase);
                                $rcmodel->setCustomerId($parentId);
                                $rcmodel->save();
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * @param $value
     * @return false|float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function calculate($value)
    {
        $originalBasePrice = $this->convertPriceRate($value);
        $pointPriceRate = $this->scopeConfig->getValue('reward/setting/point_value', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $definePoint = $this->scopeConfig->getValue('reward/setting/point', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (empty((int)$pointPriceRate)) {
            $usedpoint = 0;
        } else {
            $usedpoint = (1 / $pointPriceRate) * $originalBasePrice;
        }
        return floor($usedpoint);
    }

    /**
     * @param int $amount
     * @param null $store
     * @param null $currency
     * @return float|int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function convertPriceRate($amount = 0, $store = null, $currency = null)
    {
        $getCurrentCurrencyRate = $this->storeManager->getStore()->getCurrentCurrencyRate();
        $value = $amount / $getCurrentCurrencyRate;
        return $value;
    }
}
