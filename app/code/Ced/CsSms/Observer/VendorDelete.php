<?php

namespace Ced\CsSms\Observer;

use Magento\Framework\Event\ObserverInterface;

class VendorDelete implements ObserverInterface
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
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * VendorDelete constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Ced\CsSms\Helper\Data $helper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\CsSms\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->_objectManager = $objectManager;
        $this->helper = $helper;
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
                if ($this->getHelper()->isSectionEnabled('vendor_status/acoount_delete')) {
                    $vendor = $observer->getEvent()->getVendor();
                    if ($vendor && $vendor->getId()) {
                        $smsto = $this->getHelper()->getCountryNumber($vendor->getId());
                        if ($smsto != '' && $smsto != null) {
                            $smsmsg = __('Hi' . ' ' . $vendor->getName() . ' ' . ', Your Vendor Account has been deleted.');
                            try {
                                $this->getHelper()->sendSms($smsto,['name' => $vendor->getName()], $smsmsg,'vendor_account_delete');
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
}
