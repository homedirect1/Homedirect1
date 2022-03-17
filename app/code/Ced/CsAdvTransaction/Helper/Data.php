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
 * @author     CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Helper;

/**
 * Class Data
 * @package Ced\CsAdvTransaction\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var int
     */
    protected $_storeId = 0;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Ced\CsMarketplace\Model\VendorFactory
     */
    protected $vendorFactory;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $state;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Ced\CsMarketplace\Model\VordersFactory
     */
    protected $vordersFactory;

    /**
     * @var \Ced\CsAdvTransaction\Model\OrderfeeFactory
     */
    protected $orderfeeFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * Data constructor.
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param \Magento\Framework\Translate\Inline\StateInterface $state
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Ced\CsMarketplace\Model\VordersFactory $vordersFactory
     * @param \Ced\CsAdvTransaction\Model\OrderfeeFactory $orderfeeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Magento\Framework\Translate\Inline\StateInterface $state,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Ced\CsMarketplace\Model\VordersFactory $vordersFactory,
        \Ced\CsAdvTransaction\Model\OrderfeeFactory $orderfeeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Unserialize\Unserialize $unserialize
    )
    {
        $this->orderFactory = $orderFactory;
        $this->scopeConfig = $context->getScopeConfig();
        $this->vendorFactory = $vendorFactory;
        $this->state = $state;
        $this->transportBuilder = $transportBuilder;
        $this->vordersFactory = $vordersFactory;
        $this->orderfeeFactory = $orderfeeFactory;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->dateTime = $dateTime;
        $this->unserialize = $unserialize;
        parent::__construct($context);
    }

    /**
     * @param $orderId
     * @return \Magento\Framework\Phrase
     */
    public function getOrderPaymentType($orderId)
    {
        $order = $this->orderFactory->create()->loadByIncrementId($orderId);
        $orderId = $order->getId();
        $payment_method = [];
        if($order->getPayment()){
            $payment_method = $order->getPayment()->getMethodInstance()->getCode();
        }
        $prepaids = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/postpaid_paymethod');
        $prepaids = explode(',', $prepaids);
        if (in_array($payment_method, $prepaids)) {
            return __('PostPaid');
        } else {
            return __('PrePaid');
        }
    }

    /**
     * @param $data
     */
    public function sendInvoiceToVendor($data)
    {

        $vendor = $this->vendorFactory->create()->load($data['vendor_id']);
        $vendorName = $vendor->getName();
        $vendorEmail = $vendor->getEmail();

        $senderEmail = $this->scopeConfig->getValue('trans_email/ident_general/email');

        $data['vendor_name'] = $vendorName;
        if (!empty($senderEmail)) {

            $senderName = "Admin";
            $data['id'] = $data;
            $this->state->suspend();
            $templateVars = $data;

            try {


                $mail = $senderEmail;
                $error = false;
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $Vsender = [
                    'name' => $senderName,
                    'email' => $senderEmail,
                ];

                $transport = $this->transportBuilder
                    ->setTemplateIdentifier('ced_csadvtransaction_vendor_pay_invoice')// this code we have mentioned in the email_templates.xml
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ]
                    )
                    ->setTemplateVars($templateVars)
                    ->setFrom($Vsender)
                    ->addTo($vendorEmail)
                    ->getTransport();

                $transport->sendMessage();;
                $this->state->resume();
                return;


            } catch (\Exception $e) {

                return;
            }
        }


    }

    /**
     * @param $order
     * @return array
     */
    public function getPostPaidAmount($order)
    {
        $vid = $order['vendor_id'];
        if ($order['code'] != NULL) {
            if ($this->getOrderPaymentType($order['order_id']) == __('PostPaid')) {
                $postPaidAmount = $order['order_total'] + $order['shipping_amount'];
            } else {
                $postPaidAmount = 0;
            }
            $shipAmount = $order['shipping_amount'];
            $shipType = "vendor";
        } else {
            $origOrder = $this->orderFactory->create()->load($order['order_id'], 'increment_id');
            $shipAmount = $origOrder->getBaseShippingAmount();

            $id = $origOrder->getId();
            $vendorOrders = $this->vordersFactory->create()->getCollection()
                ->addFieldToFilter('vendor_id', ['nin' => $vid])
                ->addFieldToFilter('order_id', $order['order_id'])->getData();

            $shipping = 0;
            $othervid = [];
            if (count($vendorOrders)) {
                foreach ($vendorOrders as $vorder) {
                    if ($vorder['code'] != NULL) {
                        $othervid[] = $vorder['vendor_id'];
                        $shipping = $shipping + $vorder['shipping_amount'];

                    }
                }
            }
            $items = $this->orderFactory->create()->load($id)->getAllItems();
            $vids = [];
            foreach ($items as $item) {
                if ($item->getProductType() == "virtual") {
                    continue;
                }
                $vids[] = $item->getVendorId();
            }
            $vids = array_unique($vids);

            $vids = array_diff($vids, $othervid);
            $count = count($vids);


            if ($count) {
                $shipAmount = ($shipAmount - $shipping) / $count;
            } else {
                $shipAmount = 0;
            }

            if ($this->getOrderPaymentType($order['order_id']) == __('PostPaid')) {
                $postPaidAmount = $order['order_total'] + $shipAmount;
            } else {
                $postPaidAmount = 0;
            }


            $shipType = "admin";
        }
        return ['post_paid' => $postPaidAmount, 'ship_amount' => $shipAmount, 'ship_type' => $shipType];
    }


    /**
     * @param $orIds
     * @param $vid
     * @return float|int|mixed
     */
    public function getServiceTax($orIds, $vid)
    {

        $serviceTax = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/vendor_taxes');
        $serviceTax = $this->unserialize->unserialize($serviceTax);
        //$serviceTax = json_decode($serviceTax, true);


        $taxes = 0;
        $fees = 0;
        if (is_array($serviceTax)) {
            foreach ($serviceTax as $key => $tax) {
                if ($tax['enable']) {
                    $taxes = $taxes + $tax['amount'];
                }
            }
            $orderFees = $this->orderfeeFactory->create()->getCollection()
                ->addFieldToFilter('order_id', ['in' => $orIds])
                ->addFieldToFilter('status', 0)
                ->addFieldToFilter('vendor_id', $vid)->getData();

            foreach ($orderFees as $Ofees) {
                $fees = $fees + $Ofees['amount'];
            }
            $commissionFee = 0;
            $commission = 0;
            $shipping = 0;
            $shippingFee = 0;
            $payShip = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_shipping');
            for ($i = 0; $i < count($orIds); $i++) {
                $vendororder = $this->vordersFactory->create()->getCollection()
                    ->addFieldToFilter('order_id', $orIds[$i])
                    ->addFieldToFilter('vendor_id', $vid)->getFirstItem();

                if ($vendororder->getPaymentState() != 4) {
                    $commissionFee = $vendororder->getShopCommissionFee();

                    $postPaiddetails = $this->getPostPaidAmount($vendororder->getData());
                    if ($postPaiddetails['ship_type'] == 'admin' || ($postPaiddetails['ship_type'] == 'vendor' && !$payShip)) {
                        $shippingFee = $postPaiddetails['ship_amount'];
                    }
                } else {
                    $commissionFee = 0;
                    $shippingFee = 0;
                }
                $shipping = $shipping + $shippingFee;
                $commission = $commission + $commissionFee;
            }
            $fees = $fees + $commission + $shipping;
            $serviceTax = $fees * $taxes / 100;
        } else {
            $serviceTax = 0;
        }


        return $serviceTax;

    }


    /**
     * @param $orIds
     * @param $vid
     * @return float|int|mixed
     */
    public function getServiceTaxforOrder($orIds, $vid)
    {

        $serviceTax = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/vendor_taxes');        
        $serviceTax = $this->unserialize->unserialize($serviceTax);
        //$serviceTax = json_decode($serviceTax, true);


        $taxes = 0;
        $fees = 0;
        if (is_array($serviceTax)) {
            foreach ($serviceTax as $key => $tax) {
                if ($tax['enable']) {
                    $taxes = $taxes + $tax['amount'];
                }
            }
            $orderFees = $this->orderfeeFactory->create()->getCollection()->addFieldToFilter('order_id', ['in' => $orIds])->addFieldToFilter('vendor_id', $vid)->getData();

            foreach ($orderFees as $Ofees) {
                $fees = $fees + $Ofees['amount'];
            }
            $commissionFee = 0;
            $commission = 0;
            $shipping = 0;
            $shippingFee = 0;
            $payShip = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_shipping');
            for ($i = 0; $i < count($orIds); $i++) {
                $vendororder = $this->vordersFactory->create()->getCollection()
                    ->addFieldToFilter('order_id', $orIds[$i])
                    ->addFieldToFilter('vendor_id', $vid)->getFirstItem();

                if ($vendororder->getPaymentState() != 4) {
                    $commissionFee = $vendororder->getShopCommissionFee();

                    $postPaiddetails = $this->getPostPaidAmount($vendororder->getData());
                    if ($postPaiddetails['ship_type'] == 'admin' || ($postPaiddetails['ship_type'] == 'vendor' && !$payShip)) {
                        $shippingFee = $postPaiddetails['ship_amount'];
                    }
                } else {
                    $commissionFee = 0;
                    $shippingFee = 0;
                }
                $shipping = $shipping + $shippingFee;
                $commission = $commission + $commissionFee;
            }
            $fees = $fees + $commission + $shipping;
            $serviceTax = $fees * $taxes / 100;
        } else {
            $serviceTax = 0;
        }


        return $serviceTax;

    }

    /**
     * @param $vid
     * @param $vorderId
     * @return float|int|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function vendorPay($vid, $vorderId)
    {
        $orders = $this->vordersFactory->create()->load($vorderId)->getData();
        $currencyCode = $this->storeManager->getStore(null)->getBaseCurrencyCode();
        $PaytoVendor = 0;
        $vendorPay = 0;
        $orIds = [];
        $orIds[] = $orders['order_id'];
        if ($orders['payment_state'] == 1) {

            if ($this->getOrderPaymentType($orders['order_id']) == "PostPaid") {

                $postPaiddetails = $this->getPostPaidAmount($orders);

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

                    $shipping = $this->getPostPaidAmount($orders);
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

        $PaytoVendor = $PaytoVendor + $vendorPay;

        if ($orders['payment_state'] == 3) {
            return $PaytoVendor;
        }


        $serviceTax = $this->getServiceTax($orIds, $vid);
        $PaytoVendor = $PaytoVendor - $serviceTax;

        return $PaytoVendor;
    }


    /**
     * @return mixed
     */
    public function getVendorId()
    {
        $vendorId = $this->customerSession->getVendorId();
        return $vendorId;
    }

    /**
     * @return float|int|mixed
     */
    public function getPendingPayAmount()
    {
        $eligibleOrders = $this->getVendorEligibleOrders()->getData();
        $vid = $this->getVendorId();
        $PaytoVendor = 0;
        $vendorPay = 0;
        try {
            foreach ($eligibleOrders as $orders) {
                $orIds = [];
                $orIds[] = $orders['order_id'];
                if ($orders['payment_state'] == 1) {
                    if ($this->getOrderPaymentType($orders['order_id']) == "PostPaid") {

                        $postPaiddetails = $this->getPostPaidAmount($orders);
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

                            $shipping = $this->getPostPaidAmount($orders);
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
                $serviceTax = $this->getServiceTax($orIds, $vid);
                $PaytoVendor = $PaytoVendor - $serviceTax;
                $PaytoVendor = $PaytoVendor + $vendorPay;

            }
        } catch (\Exception $e) {
            die($e);
        }
        return $PaytoVendor;
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
        $vorders = $this->vordersFactory->create()->getCollection()
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

        $orders = $this->vordersFactory->create()->getCollection()
            ->addFieldToFilter('vendor_id', $vid)
            ->addFieldToFilter('order_id', ['in' => $orderIds]);
        return $orders;
    }
}
