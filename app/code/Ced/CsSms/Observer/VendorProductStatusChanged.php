<?php

namespace Ced\CsSms\Observer;

use Magento\Framework\Event\ObserverInterface;

class VendorProductStatusChanged implements ObserverInterface
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
     * @var \Ced\CsMarketplace\Model\Vendor
     */
    protected $vendor;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * VendorProductStatusChanged constructor.
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
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->getHelper()->isSmsSetting('sms_notification/enter/enable')) {
            if ($this->getHelper()->isSmsExtensionEnableFound() > 0) {
                if ($this->getHelper()->isSectionEnabled('vendor_product_status/enable')) {
                    $status = $observer->getStatus();
                    $product = $observer->getProduct();
                    $vendorId = $product->getVendorId();
                    $_vendor = $this->vendor->load($vendorId);
                    if ($_vendor) {
                        $smsto = $this->getHelper()->getCountryNumber($vendorId);
                        if ($smsto != '' && $smsto != null) {
                            $smsmsg = $this->getHelper()->getVendorProductStatusMsg($_vendor, $product, $status);
                            $codes = $this->getVendorProductStatusVariables($_vendor, $product, $status);
                            try {
                                $this->getHelper()->sendSms($smsto, $codes, $smsmsg, 'vendor_product_status');
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
     * @return \Ced\CsSms\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    public function getVendorProductStatusVariables($vendor, $product, $checkStatus)
    {
        $status='';
        if($checkStatus == '0')
            $status = 'Not Approved';
        elseif ($checkStatus == '1')
            $status = 'Approved';
        elseif ($checkStatus == '2')
            $status = 'Pending';
        elseif ($checkStatus == '3')
            $status = 'Delete';;

        $codes = [
            'name' => $vendor->getName(),
            'email' => $vendor->getEmail(),
            'productname' => $product->getName(),
            'sku' => $product->getSku(),
            'status' => $status
        ];
        return $codes;
    }
}
