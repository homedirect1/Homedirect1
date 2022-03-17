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

namespace Ced\CsAdvTransaction\Controller\Adminhtml\Pay;

use Magento\Backend\App\Action;

/**
 * Class Save
 * @package Ced\CsAdvTransaction\Controller\Adminhtml\Pay
 */
class Save extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor
{
    /**
     * @var \Ced\CsMarketplace\Model\VpaymentFactory
     */
    protected $vpaymentFactory;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $currency;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Ced\CsAdvTransaction\Model\OrderfeeFactory
     */
    protected $orderfeeFactory;

    /**
     * @var \Ced\CsAdvTransaction\Helper\Data
     */
    protected $advHelper;

    /**
     * @var \Ced\CsMarketplace\Model\VordersFactory
     */
    protected $vordersFactory;

    /**
     * @var \Ced\CsAdvTransaction\Model\RequestFactory
     */
    protected $requestFactory;

    /**
     * Save constructor.
     * @param \Ced\CsMarketplace\Model\VpaymentFactory $vpaymentFactory
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Directory\Model\Currency $currency
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Ced\CsAdvTransaction\Model\OrderfeeFactory $orderfeeFactory
     * @param \Ced\CsAdvTransaction\Helper\Data $advHelper
     * @param \Ced\CsMarketplace\Model\VordersFactory $vordersFactory
     * @param \Ced\CsAdvTransaction\Model\RequestFactory $requestFactory
     * @param Action\Context $context
     */
    public function __construct(
        \Ced\CsMarketplace\Model\VpaymentFactory $vpaymentFactory,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ced\CsAdvTransaction\Model\OrderfeeFactory $orderfeeFactory,
        \Ced\CsAdvTransaction\Helper\Data $advHelper,
        \Ced\CsMarketplace\Model\VordersFactory $vordersFactory,
        \Ced\CsAdvTransaction\Model\RequestFactory $requestFactory,
        Action\Context $context
    )
    {
        $this->vpaymentFactory = $vpaymentFactory;
        $this->directoryHelper = $directoryHelper;
        $this->currency = $currency;
        $this->scopeConfig = $scopeConfig;
        $this->orderfeeFactory = $orderfeeFactory;
        $this->advHelper = $advHelper;
        $this->vordersFactory = $vordersFactory;
        $this->requestFactory = $requestFactory;
        parent::__construct($context);
    }

    /**
     * Customer edit action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            $params = $this->getRequest()->getParams();

            $type = 0;
            $vendorPay = 0;
            $totalShipping = 0;

            $model = $this->vpaymentFactory->create();
            $eligibleOrders = unserialize($data['eligible_orders']);

            $vendorId = $data['vendor_id'];
            $baseCurrencyCode = $this->directoryHelper->getBaseCurrencyCode();
            $allowedCurrencies = $this->currency->getConfigAllowCurrencies();
            $rates = $this->currency->getCurrencyRates($baseCurrencyCode, array_values($allowedCurrencies));
            $data['base_to_global_rate'] = isset($data['currency']) && isset($rates[$data['currency']]) ? $rates[$data['currency']] : 1;
            $data['transaction_id'] = $this->randomString(10);
            $PaytoVendor = 0;
            $description = [];
            $feesarray = [];
            $i = 0;
            $payShip = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_shipping');
            foreach ($eligibleOrders as $orders) {
                $postPaidtoVendor = 0;
                $orIds = [];
                $orIds[] = $orders['order_id'];
                $description[$i]['order_id'] = $orders['order_id'];
                $description[$i]['order_total'] = $orders['order_total'];


                $orderFees = $this->orderfeeFactory->create()->getCollection()
                    ->addFieldToFilter('order_id', $orders['order_id'])
                    ->addFieldToFilter('status', \Ced\CsAdvTransaction\Model\Orderfee::STATE_PENDING)
                    ->addFieldToFilter('vendor_id', $vendorId);
                foreach ($orderFees as $Ofees) {
                    $description[$i][$Ofees['fee_id']] = $Ofees['amount'];
                }

                if ($orders['payment_state'] == 1) {
                    if ($this->advHelper->getOrderPaymentType($orders['order_id']) == "PostPaid") {

                        $description[$i]['order_paymode'] = "PostPaid";
                        $vendorEarn = $orders['vendor_earn'];
                        $postPaiddetails = $this->advHelper->getPostPaidAmount($orders);

                        $postPaidtoVendor = $postPaiddetails['post_paid'];
                        $payShip = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_shipping');
                        $shipamount = 0;

                        if ($payShip) {
                            $shipamount = $postPaiddetails['ship_amount'];
                            if ($postPaiddetails['ship_type'] == 'admin') {
                                $shipamount = 0;
                            }
                            $totalShipping = $totalShipping + $shipamount;
                            $vendorPay = $vendorEarn - $postPaidtoVendor + $shipamount;
                        } else {
                            $vendorPay = $vendorEarn - $postPaidtoVendor;
                        }

                        if ($postPaiddetails['ship_type'] == 'admin' || ($postPaiddetails['ship_type'] == 'vendor' && !$payShip)) {
                            $description[$i]['ship_amount'] = $postPaiddetails['ship_amount'];
                        }
                    } else {
                        $description[$i]['order_paymode'] = "PrePaid";
                        $PaidtoVendor = 0;

                        $vendorEarn = $orders['vendor_earn'];

                        $vendorPay = $vendorEarn - $PaidtoVendor;
                        $shipping = $this->advHelper->getPostPaidAmount($orders);
                        if ($payShip) {

                            $ship = $shipping['ship_amount'];
                            if ($shipping['ship_type'] == 'admin') {
                                $ship = 0;
                            }
                            $totalShipping = $totalShipping + $ship;

                            $vendorPay = $vendorPay + $ship;
                            $description[$i]['ship_amount'] = $shipping['ship_amount'];
                        }
                        if ($shipping['ship_type'] == 'admin' || ($shipping['ship_type'] == 'vendor' && !$payShip)) {
                            $description[$i]['ship_amount'] = $shipping['ship_amount'];
                        }

                    }
                    $description[$i]['commission_fee'] = $orders['shop_commission_fee'];
                } elseif ($orders['payment_state'] == 3) {
                    $shipping = $this->advHelper->getPostPaidAmount($orders);
                    $postPaidtoVendor = 0;
                    $vendorEarn = $orders['vendor_earn'];
                    $vendorPay = $vendorEarn - $postPaidtoVendor;
                    $description[$i]['commission_fee'] = $orders['shop_commission_fee'];

                    if ($shipping['ship_type'] == 'admin' || ($shipping['ship_type'] == 'vendor' && !$payShip)) {
                        $description[$i]['ship_amount'] = $shipping['ship_amount'];
                    }
                }
                if ($orders['payment_state'] == 4) {
                    $PaidtoVendor = 0;
                    $vendorEarn = $orders['vendor_earn'];
                    $vendorPay = $vendorEarn - $PaidtoVendor;
                }

                $serviceTax = $this->advHelper->getServiceTax($orIds, $vendorId);

                $vendorPay = $vendorPay - $serviceTax;

                $description[$i]['vendor_payment'] = $vendorPay;

                $VORDERId = $this->vordersFactory->create()->getCollection()
                    ->addFieldToFilter('order_id', $orders['order_id'])
                    ->addFieldToFilter('vendor_id', $vendorId)->getFirstItem()->getId();

                $this->vordersFactory->create()->load($VORDERId)->setVendorEarn($vendorPay)->save();

                if ($postPaidtoVendor) {
                    $this->vordersFactory->create()->load($VORDERId)->setVendorEarn($postPaidtoVendor - abs($vendorPay))->save();

                }

                $description[$i]['service_tax'] = $serviceTax;
                $PaytoVendor = $PaytoVendor + $vendorPay;

                $i++;
            }

            $description = json_encode($description);
            $value = (float)$data['base_amount'];

            $flag = true;
            if (abs(($value - $PaytoVendor) / $PaytoVendor) < 0.00001) {
                $flag = false;
            }


            if ($flag) {
                $this->messageManager->addErrorMessage('Amount entered should be equal to the sum of all selected order(s)');
                $this->_redirect('csadvtransaction/order/pay/order', array('vendor_id' => $this->getRequest()->getParam('vendor_id'), 'type' => $type));
                return;
            }

            $data['transaction_type'] = 0;
            $data['payment_method'] = isset($data['payment_method']) ? $data['payment_method'] : 0;
            /*Will use it when vendor will pay in different currenncy  */

            list($currentBalance, $currentBaseBalance) = $model->getCurrentBalance($data['vendor_id']);

            $base_net_amount = floatval($data['base_amount']) + floatval($data['base_fee']);
            if ($type == \Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_DEBIT) {
                /* Case of Deduct credit */
                if ($currentBaseBalance > 0) {
                    $newBaseBalance = $currentBaseBalance - $base_net_amount;
                } else {
                    $newBaseBalance = $base_net_amount;
                }
                $base_net_amount = -$base_net_amount;
                if (-$base_net_amount <= 0.00) {
                    $this->messageManager->addErrorMessage("Refund Net Amount can't be less than zero");
                    $this->_redirect('csadvtransaction/pay/new', array('vendor_id' => $this->getRequest()->getParam('vendor_id')));
                    return;
                }
            } else {
                // Case of Add credit
                $newBaseBalance = $currentBaseBalance + $base_net_amount;

                if ($base_net_amount <= 0.00) {
                    $this->messageManager->addErrorMessage("Net Amount can't be less than zero");
                    $this->_redirect('csadvtransaction/pay/new', array('vendor_id' => $this->getRequest()->getParam('vendor_id')));
                    return;
                }
            }
            $data['base_currency'] = $baseCurrencyCode;
            $data['base_net_amount'] = $base_net_amount;
            $data['base_balance'] = $newBaseBalance;
            $data['total_shipping_amount'] = $totalShipping;

            $data['amount'] = $PaytoVendor * $data['base_to_global_rate'];
            $data['balance'] = $this->directoryHelper->currencyConvert($newBaseBalance, $baseCurrencyCode, $data['currency']);
            $data['fee'] = $this->directoryHelper->currencyConvert(floatval($data['base_fee']), $baseCurrencyCode, $data['currency']);
            $data['net_amount'] = $this->directoryHelper->currencyConvert($base_net_amount, $baseCurrencyCode, $data['currency']);

            $data['tax'] = 0.00;
            $data['payment_detail'] = isset($data['payment_detail']) ? $data['payment_detail'] : 'n/a';

            unset($data['eligible_orders']);

            $data['amount_desc'] = $description;


            $model->addData($data);


            $openStatus = $model->getOpenStatus();
            $model->setStatus($openStatus);

            try {

                foreach ($eligibleOrders as $payOrders) {
                    $vorder = $this->vordersFactory->create()->load($payOrders['id']);
                    if ($vorder->getPaymentState() == \Ced\CsMarketplace\Model\Vorders::STATE_REFUND) {
                        $vorder->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUNDED);
                    } elseif ($vorder->getPaymentState() == \Ced\CsMarketplace\Model\Vorders::STATE_CANCELED) {
                        $vorder->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUNDED);
                    } else {
                        $vorder->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_PAID);
                    }

                    $vorder->save();

                    $orderfee = $this->orderfeeFactory->create()->getCollection()
                        ->addFieldToFilter('order_id', $payOrders['order_id'])
                        ->addFieldToFilter('status', \Ced\CsAdvTransaction\Model\Orderfee::STATE_PENDING)
                        ->addFieldToFilter('vendor_id', $vendorId);

                    foreach ($orderfee as $fees) {
                        $vfee = $this->orderfeeFactory->create()->load($fees->getId());
                        $vfee->setStatus(\Ced\CsAdvTransaction\Model\Orderfee::STATE_PAID);
                        $vfee->save();
                    }
                }
                $model->save();

                $Requestmodel = $this->requestFactory->create()->getCollection()
                    ->addFieldToFilter('vendor_id', $vendorId)->addFieldToFilter('status', \Ced\CsAdvTransaction\Model\Request::PENDING)
                    ->getFirstItem();
                if ($Requestmodel->getId()) {
                    $this->requestFactory->create()
                        ->load($Requestmodel->getId())->setStatus(\Ced\CsAdvTransaction\Model\Request::APPROVED)->save();
                }

                $data['eligible_orders'] = serialize($eligibleOrders);
                $data['vpay_id'] = $model->getId();
                $this->advHelper->sendInvoiceToVendor($data);

                $this->messageManager->addSuccessMessage(__('Payment is successfully saved'));

                $this->_redirect('csmarketplace/vpayments/index/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_session->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }

    }

    /**
     * @param $length
     * @return string
     */
    function randomString($length)
    {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }
}
