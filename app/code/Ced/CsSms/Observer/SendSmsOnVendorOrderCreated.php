<?php

namespace Ced\CsSms\Observer;

use Magento\Framework\Event\ObserverInterface;

class SendSmsOnVendorOrderCreated implements ObserverInterface
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
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \Ced\CsMarketplace\Model\Vendor
     */
    protected $vendor;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * SendSmsOnVendorOrderCreated constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Ced\CsSms\Helper\Data $helper
     * @param \Magento\Sales\Model\Order $order
     * @param \Ced\CsMarketplace\Model\Vendor $vendor
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\CsSms\Helper\Data $helper,
        \Magento\Sales\Model\Order $order,
        \Ced\CsMarketplace\Model\Vendor $vendor,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->_objectManager = $objectManager;
        $this->helper = $helper;
        $this->order = $order;
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
                if ($this->getHelper()->isSectionEnabled('vendor_order/enable')) {
                    $orders = $observer->getEvent()->getOrderIds();
                    $order = $this->order->load($orders['0']);
                    if ($order instanceof \Magento\Sales\Model\Order) {
                        $ordered_items = $order->getAllVisibleItems();
                        foreach ($ordered_items as $item) {
                            $vendor_id = $item->getVendorId();
                            if ($vendor_id) {
                                $vendor_customer = $this->vendor->load($vendor_id);
                                $smsto = $this->getHelper()->getCountryNumber($vendor_id);
                                if ($smsto != '' && $smsto != null) {
                                    $smsmsg = $this->getHelper()->newVendorOrderMsg($item, $vendor_customer, $orders['0']);
                                    $code = $this->newVendorOrderVariables($item, $vendor_customer, $orders['0']);
                                    try {
                                        $this->getHelper()->sendSms($smsto, $code, $smsmsg,'vendor_order');
                                    } catch (\Magento\Framework\Exception $e) {
                                        $this->messageManager->addError(__('Something went wrong ' . $e->getMessage()));
                                    }
                                }
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

    public function newVendorOrderVariables($product, $vendor, $orderId)
    {
        $codes = [
            'name' => $vendor->getName(),
            'email' => $vendor->getEmail(),
            'productname' => $product->getName(),
            'sku' =>  $product->getSku(),
            '$orderId' => $orderId
        ];

        return $codes;
    }
}
