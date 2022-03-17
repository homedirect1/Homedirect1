<?php

namespace Ced\CsSms\Observer;

use Magento\Framework\Event\ObserverInterface;

class VendorStatusChanged implements ObserverInterface
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
     * VendorStatusChanged constructor.
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
                $vendor = $observer->getEvent()->getVendor();
                if ($this->getHelper()->isSectionEnabled('vendor_status/enable') && $vendor) {
                    $_vendor = $this->vendor->load($vendor->getId());
                    $vendorData = $_vendor->getData();
                    if (!empty($vendorData)) {
                        if (array_key_exists('status', $vendorData)) {
                            $status = $vendorData['status'];
                        }
                    } else {
                        $status = $vendor->getStatus();
                    }
                    $smsto = $this->getHelper()->getCountryNumber($vendor->getId());
                    if ($smsto != '' && $smsto != null) {
                        $smsmsg = $this->getHelper()->getVendorStatusMsg($_vendor, $status);
                        $codes = $this->getVendorStatusVariables($_vendor, $status);
                        try {
                            $this->getHelper()->sendSms($smsto, $codes,$smsmsg,'vendor_status');
                        } catch (\Magento\Framework\Exception $e) {
                            $this->messageManager->addError(__('Something went wrong ' . $e->getMessage()));
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

    public function getVendorStatusVariables($vendor, $status)
    {
        $code = [
            'name' => $vendor->getName(),
            'email' => $vendor->getEmail(),
            'status' => $status
        ];

        return  $code;


    }
}
