<?php

namespace Ced\CsSms\Observer;

use Magento\Framework\Event\ObserverInterface;

class NewVendorPaymentSaved implements ObserverInterface
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Ced\CsSms\Helper\Data
     */
    protected $helper;

    /**
     * @var \Ced\CsMarketplace\Model\Vendor Instance
     */
    protected $vendor;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * NewVendorPaymentSaved constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Ced\CsSms\Helper\Data $helper
     * @param \Ced\CsMarketplace\Model\Vendor $vendor
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\CsSms\Helper\Data $helper,
        \Ced\CsMarketplace\Model\Vendor $vendor,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->_objectManager = $objectManager;
        $this->helper = $helper;
        $this->vendor = $vendor;
        $this->messageManager = $messageManager;
    }


    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->getHelper()->isSmsSetting('sms_notification/enter/enable')) {
            if($this->getHelper()->isSmsExtensionEnableFound() > 0) {
                if ($this->getHelper()->isSectionEnabled('vendor_payment/enable')) {
                    $payment = $observer->getEvent()->getVpayment();
                    $vendor = $this->vendor->load($payment->getVendorId());
                    if ($vendor && $vendor->getId()) {
                        $smsto = $this->getHelper()->getCountryNumber($vendor->getId());
                        if ($smsto != '') {
                            $smsmsg = $this->getHelper()->newVendorPaymentMsg($payment, $vendor);
                            $code = $this->newVendorPaymentVariables($payment, $vendor);
                            try {
                                $this->getHelper()->sendSms($smsto, $code, $smsmsg,'vendor_payment');
                            } catch (\Magento\Framework\Exception $e) {
                                $this->messageManager->addError(__('Something went wrong ' . $e->getMessage()));
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @return \Ced\CsSms\Helper\Data Instance
     */
    public function getHelper()
    {
        return $this->helper;
    }


    public function newVendorPaymentVariables($payment, $vendor)
    {
        $orderIds = '';
        $transaction = $payment->getTransactionType();
        $transaction_type = '';

        if($transaction == '0')
            $transaction_type = 'Credit';
        elseif ($transaction == '1')
            $transaction_type = 'Debit';

        $amount_desc = json_decode($payment->getAmountDesc());
        foreach ($amount_desc as $key => $value) {
            $orderIds .= $key.', ';
        }

        $codes = [
            'name' => $vendor->getName(),
            'transactionid' => $payment->getTransactionId(),
            'amount' => $payment->getBaseAmount(),
            'orderids' => $orderIds,
            'paymentcode' => $payment->getPaymentCode(),
            'transactiontype' => $transaction_type
        ];

        return $codes;

    }
}
