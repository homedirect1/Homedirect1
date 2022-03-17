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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Model\Api\Affiliate;

use \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;

/**
 * Class WalletInformation
 * @package Ced\Affiliate\Model\Api\Affiliate
 */
class WalletInformation extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var CollectionFactoryInterface
     */
    protected $orderCollection;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * @var \Ced\Affiliate\Model\AffiliateWalletFactory
     */
    protected $affiliateWalletFactory;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * @var \Ced\Affiliate\Model\PaymentMethodsFactory
     */
    protected $paymentMethodsFactory;

    /**
     * WalletInformation constructor.
     * @param CollectionFactoryInterface $orderCollection
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Ced\Affiliate\Model\AffiliateWalletFactory $affiliateWalletFactory
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Ced\Affiliate\Model\PaymentMethodsFactory $paymentMethodsFactory
     */
    public function __construct(
        CollectionFactoryInterface $orderCollection,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Ced\Affiliate\Model\AffiliateWalletFactory $affiliateWalletFactory,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Ced\Affiliate\Model\PaymentMethodsFactory $paymentMethodsFactory
    )
    {
        $this->orderCollection = $orderCollection;
        $this->_orderConfig = $orderConfig;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->affiliateWalletFactory = $affiliateWalletFactory;
        $this->affiliateHelper = $affiliateHelper;
        $this->paymentMethodsFactory = $paymentMethodsFactory;
    }

    /**
     * @param $customerId
     * @return array
     */
    public function getWalletAmount($customerId)
    {
        $affiliateData = [];
        $affiliate = $this->affiliateAccountFactory->create()->load($customerId, 'customer_id');
        $affiliateId = $affiliate->getAffiliateId();

        $amount = $this->affiliateWalletFactory->create()->load($customerId, 'customer_id');
        $affiliateWallet['total_amount'] = $this->affiliateHelper->getFormattedPrice($amount->getCreditAmount());
        $affiliateWallet['used_amount'] = $this->affiliateHelper->getFormattedPrice($amount->getUseAmount());
        $affiliateWallet['remaining_amount'] = $this->affiliateHelper->getFormattedPrice($amount->getRemainingAmount());
        $affiliateWallet['totalamount'] = $amount->getRemainingAmount();

        $affiliateData['balance_history'] = $affiliateWallet;
        $affiliateData['payment_method'] = $this->paymentMethodsFactory->create()->getPaymentMethodsArray($customerId);
        $affiliateData['orders'] = $this->getOrders($customerId);
        return ["data" => $affiliateData];
    }

    /**
     * @param $customerId
     * @return bool
     */
    protected function getOrders($customerId)
    {
        if (!$customerId) {
            return false;
        }
        if (!$this->orders) {
            $this->orders = $this->getOrderCollectionFactory()->create($customerId)->join(
                array('payment' => 'sales_order_payment'),
                'main_table.entity_id=payment.parent_id',
                array('payment_method' => 'payment.method')
            );
            $this->orders->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'status',
                ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
            )->addFieldToFilter('payment.method', array(array('like' => 'storecredit')))->setOrder(
                'created_at',
                'desc'
            );
        }
        return $this->orders;
    }

    /**
     * @return mixed
     */
    private function getOrderCollectionFactory()
    {
        return $this->orderCollection;
    }
}
