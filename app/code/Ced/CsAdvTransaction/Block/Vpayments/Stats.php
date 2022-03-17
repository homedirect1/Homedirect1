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
 * @package     Ced_CsAdvTransaction
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Block\Vpayments;

use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Stats
 * @package Ced\CsAdvTransaction\Block\Vpayments
 */
class Stats extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{
    /**
     * @var \Ced\CsAdvTransaction\Helper\Data
     */
    protected $advHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vorders\CollectionFactory
     */
    protected $vordersCollection;

    /**
     * @var \Ced\CsAdvTransaction\Model\ResourceModel\Request\CollectionFactory
     */
    protected $requestCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    public $priceCurrency;

    /**
     * @var \Ced\CsMarketplace\Model\Session
     */
    public $customerSession;

    /**
     * Stats constructor.
     * @param \Ced\CsAdvTransaction\Helper\Data $advHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vorders\CollectionFactory $vordersCollection
     * @param \Ced\CsAdvTransaction\Model\ResourceModel\Request\CollectionFactory $requestCollection
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param Context $context
     * @param \Ced\CsMarketplace\Model\Session $customerSession
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        \Ced\CsAdvTransaction\Helper\Data $advHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Ced\CsMarketplace\Model\ResourceModel\Vorders\CollectionFactory $vordersCollection,
        \Ced\CsAdvTransaction\Model\ResourceModel\Request\CollectionFactory $requestCollection,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Ced\CsMarketplace\Helper\Payment $paymentHelper,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        Context $context,
        \Ced\CsMarketplace\Model\Session $customerSession,
        UrlFactory $urlFactory
    )
    {
        $this->advHelper = $advHelper;
        $this->scopeConfig = $context->getScopeConfig();
        $this->dateTime = $dateTime;
        $this->vordersCollection = $vordersCollection;
        $this->requestCollection = $requestCollection;
        $this->storeManager = $context->getStoreManager();
        $this->priceCurrency = $priceCurrency;
        $this->customerSession = $customerSession;
        $this->paymentHelper = $paymentHelper;

        parent::__construct($vendorFactory, $customerFactory, $context, $customerSession, $urlFactory);

        $this->setPendingAmount(0.00);
        $this->setPendingTransfers(0);
        $this->setPaidAmount(0.00);
        $this->setCanceledAmount(0.00);
        $this->setRefundableAmount(0.00);
        $this->setRefundedAmount(0.00);
        $this->setEarningAmount(0.00);

        if ($this->getVendor() && $this->getVendor()->getId()) {
            $productsCollection = array();
            $collection = $this->paymentHelper->_getTransactionsStats($this->getVendor());

            $pendingAmount = $this->getPendingPayAmount();

            if (count($collection) > 0) {
                foreach ($collection as $stats) {
                    switch ($stats->getPaymentState()) {
                        case \Ced\CsMarketplace\Model\Vorders::STATE_OPEN :
                            $this->setPendingAmount($pendingAmount);
                            $this->setPendingTransfers($stats->getCount() ? $stats->getCount() : 0);
                            break;
                        case \Ced\CsMarketplace\Model\Vorders::STATE_PAID :
                            $this->setPaidAmount($stats->getNetAmount());
                            break;
                        case \Ced\CsMarketplace\Model\Vorders::STATE_CANCELED :
                            $this->setCanceledAmount($stats->getNetAmount());
                            break;
                        case \Ced\CsMarketplace\Model\Vorders::STATE_REFUND :
                            $this->setRefundableAmount($stats->getNetAmount());
                            break;
                        case \Ced\CsMarketplace\Model\Vorders::STATE_REFUNDED :
                            $this->setRefundedAmount($stats->getNetAmount());
                            break;
                    }
                }
            }
            $this->setEarningAmount($this->getVendor()->getAssociatedPayments()->getFirstItem()->getBalance());
        }
    }

    /**
     * @return float|int|mixed
     */
    public function getPendingPayAmount()
    {
        $eligibleOrders = $this->getVendorEligibleOrders()->getData();
        $vid = $this->getVendorId();
        $PaytoVendor = 0;
        try {
            foreach ($eligibleOrders as $orders) {
                $orIds = [];
                $orIds[] = $orders['order_id'];
                if ($orders['payment_state'] == 1) {
                    if ($this->advHelper->getOrderPaymentType($orders['order_id']) == "PostPaid") {

                        $postPaiddetails = $this->advHelper->getPostPaidAmount($orders);
                        $postPaidtoVendor = $postPaiddetails['post_paid'];
                        $vendorEarn = $orders['vendor_earn'];
                        $payShip = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_shipping');
                        $shipamount = 0;
                        if ($payShip) {
                            $shipamount = $postPaiddetails['ship_amount'];
                            if ($postPaiddetails['ship_type'] == 'admin') {
                                $shipamount = 0;
                            }
                            $vendorPay = $vendorEarn - $postPaidtoVendor + $shipamount;
                        } else {
                            $shipamount = $postPaiddetails['ship_amount'];
                            $vendorPay = $vendorEarn - $postPaidtoVendor;
                        }
                    } else {

                        $PaidtoVendor = 0;

                        $vendorEarn = $orders['vendor_earn'];
                        $payShip = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_shipping');
                        $vendorPay = $vendorEarn - $PaidtoVendor;
                        if ($payShip) {

                            $shipping = $this->advHelper->getPostPaidAmount($orders);
                            $ship = $shipping['ship_amount'];
                            if ($shipping['ship_type'] == 'admin') {
                                $ship = 0;
                            }
                            $vendorPay = $vendorPay + $ship;
                        }
                    }
                } elseif ($orders['payment_state'] == 3) {
                    $postPaidtoVendor = 0;
                    $vendorEarn = $orders['vendor_earn'];
                    $vendorPay = $vendorEarn - $postPaidtoVendor;
                }
                if ($orders['payment_state'] == 4) {
                    $PaidtoVendor = 0;
                    $vendorEarn = $orders['vendor_earn'];
                    $vendorPay = $vendorEarn - $PaidtoVendor;
                }
                $serviceTax = $this->advHelper->getServiceTax($orIds, $vid);
                $PaytoVendor = $PaytoVendor - $serviceTax;
                $PaytoVendor = $PaytoVendor + $vendorPay;

            }
        } catch (\Exception $e) {
            die($e);
        }
        return $PaytoVendor;
    }

    /**
     * @return int
     */
    public function getVendorId()
    {
        $vendorId = $this->session->getVendorId();
        return $vendorId;
    }

    /**
     * @return mixed
     */
    public function getVendorEligibleOrders()
    {

        $rmaDate = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/refund_policy');
        $paycycle = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_cycle');
        $completeCycle = $rmaDate + $paycycle;

        $date = $this->dateTime->gmtDate();
        $date = explode(' ', $date);

        $vid = $this->getVendorId();
        $vorders = $this->vordersCollection->create()
            ->addFieldToFilter('payment_state', ['nin' => [2, 5]])
            ->addFieldToFilter('order_payment_state', ['nin' => 1])
            ->addFieldToFilter('vendor_id', $vid)
            ->addFieldToFilter('vendor_earn', ['neq' => 0]);
        $orderIds = [];
        foreach ($vorders as $k => $v) {
            if (!$v->canInvoice() && !$v->canShip()) {

                $days = $completeCycle;
                $afterdate = strtotime("+" . $days . " days", strtotime($v->getCreatedAt()));
                $afterdate = date("Y-m-d", $afterdate);
                if ($date[0] >= $afterdate) {
                    $orderIds[] = $v->getOrderId();
                }
            }
        }

        $orders = $this->vordersCollection->create()
            ->addFieldToFilter('vendor_id', $vid)
            ->addFieldToFilter('order_id', ['in' => $orderIds]);
        return $orders;


    }

    /**
     * @return mixed
     */
    public function getVRequestedAmount()
    {
        $vid = $this->getVendorId();
        $Rmodel = $this->requestCollection->create()
            ->addFieldToFilter('vendor_id', $vid)->addFieldToFilter('status', \Ced\CsAdvTransaction\Model\Request::PENDING)
            ->getFirstItem();

        return $Rmodel->getData('amount');

    }

    /**
     * @param $price
     * @param bool $includeContainer
     * @param int $precision
     * @param null $scope
     * @param $currency
     * @return float
     */
    public function formatCurrency(
        $price,
        $includeContainer = false,
        $precision = 2,
        $scope = null,
        $currency
    ){
        return $this->priceCurrency->format(
            $price,
            $includeContainer,
            $precision,
            $scope,
            $currency
        );
    }
}
