<?php

namespace Ced\CsSms\Observer;

use Magento\Framework\Event\ObserverInterface;

class VendorNewProductCreation implements ObserverInterface
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
     * VendorNewProductCreation constructor.
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
                if ($this->getHelper()->isSectionEnabled('vendor_new_product/enable')) {
                    $product = $observer->getEvent()->getProduct();
                    $vendor_id = $observer->getEvent()->getVendorId();

                    $vendor_customer = $this->vendor->load($vendor_id);
                    if ($vendor_customer && $vendor_customer->getId()) {
                        $smsto = $this->getHelper()->getCountryNumber($vendor_id);
                        if ($smsto != '' && $smsto != null) {
                            $smsmsg = $this->getHelper()->getVendorNewProductMgs($vendor_customer, $product);
                            $code = $this->getVendorNewProductVariables($vendor_customer, $product);
                            try {
                                $this->getHelper()->sendSms($smsto, $code,$smsmsg,'vendor_new_product');
                            } catch (\Magento\Framework\Exception $e) {
                                $this->messageManager->addError(__('Something went wrong ' . $e->getMessage()));
                            }
                        }
                    }
                }

                /*codes for sending products creation messages to admin*/
                if (($this->getHelper()->isSectionEnabled('vendor_new_product/enable')) /*&&
                    ($this->getHelper()->getAdminVendorProductCreation())*/
                ) {
                    //$smsto = $this->getHelper()->getAdminVendorProductCreation();
                    $smsto = $this->getHelper()->getCountryNumber($vendor_id);
                    $smsmsg = __('New product has been created in your store');
                    try {
                        $this->getHelper()->sendSms($smsto,[], $smsmsg,'vendor_new_product_admin_notify');

                    } catch (\Magento\Framework\Exception $e) {
                        $this->messageManager->addError(__('Something went wrong ' . $e->getMessage()));
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

    public function getVendorNewProductVariables($vendor, $product)
    {
        $codes = [
            'name' => $vendor->getName(),
            'email' => $vendor->getEmail(),
             'productname' =>$product->getName(),
             'sku' => $product->getSku()
        ];
        return $codes;
    }
}
