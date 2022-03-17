<?php

namespace Ced\CsSms\Observer;

use Magento\Framework\Event\ObserverInterface;

class SendSmsOnOrderCreated implements ObserverInterface
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Ced\CsSms\Helper\Data Instance
     */
    protected $helper;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \Ced\CsMarketplace\Model\Vendor Instance
     */
    protected $vendor;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * SendSmsOnOrderCreated constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Ced\CsSms\Helper\Data $helper
     * @param \Magento\Sales\Model\Order $order
     * @param \Ced\CsMarketplace\Model\Vendor $vendor
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Ced\CsSms\Helper\Data $helper,
        \Magento\Sales\Model\Order $order,
        \Ced\CsMarketplace\Model\Vendor $vendor,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->orderRepository = $orderRepository;
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
                $orders = $observer->getEvent()->getOrderIds();
                if ($this->getHelper()->isSectionEnabled('orders/enable')) {
                    $order = $this->order->load($orders['0']);
                    if ($order instanceof \Magento\Sales\Model\Order) {

                        $smsto = $this->getHelper()->getTelephoneFromOrder($order);
                        $smsmsg = $this->getHelper()->getMessage($order);
                        $code = $this->getVariables($order);
                        try {
                            $this->getHelper()->sendSms($smsto, $code, $smsmsg,'orders');
                        } catch (\Magento\Framework\Exception $e) {
                            $this->messageManager->addError(__('Something went wrong ' . $e->getMessage()));
                        }
                    }
                }
                /*codes for sending messages to admin*/
                if (($this->getHelper()->isSectionEnabled('orders/notify')) && ($this->getHelper()->getAdminTelephone())) {
                    $order = $this->order->load($orders['0']);
                    if ($order instanceof \Magento\Sales\Model\Order) {
                        $orderIncrementId = $order->getIncrementId();
                        $smsto = $this->getHelper()->getAdminTelephone();
                        $smsmsg = __('A new order has been placed:' . '' . $orderIncrementId);
                        $code = [
                            'orderincrementid' => $orderIncrementId
                        ];
                        try {
                            $this->getHelper()->sendSms($smsto,$code,$smsmsg,'notify');
                        } catch (\Magento\Framework\Exception $e) {
                            $this->messageManager->addError(__('Something went wrong ' . $e->getMessage()));
                        }
                    }
                }
                $this->vendorOrderCreated($observer);
            }
        }
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
   public function vendorOrderCreated(\Magento\Framework\Event\Observer $observer)
    {
        if($this->getHelper()->isSectionEnabled('vendor_order/enable')) {
            $orders = $observer->getEvent()->getOrderIds();
            $order = $this->order->load($orders['0']);
            if ($order instanceof \Magento\Sales\Model\Order) {
                $ordered_items = $order->getAllVisibleItems();
                foreach ($ordered_items as $item)
                {
                    $vendor_id = $item->getVendorId();
                    if($vendor_id) {
                        $vendor_customer = $this->vendor->load($vendor_id);
                        $smsto = $this->getHelper()->getCountryNumber($vendor_id);
                        if($smsto != '' && $smsto != null) {
                            $smsmsg = $this->getHelper()->newVendorOrderMsg($item,$vendor_customer,$orders['0']);
                            try {
                                $code = $this->newVendorOrdervar($item,$vendor_customer,$orders['0']);
                                $this->getHelper()->sendSms($smsto, $code,$smsmsg,'vendor_order');
                            } catch (\Magento\Framework\Exception $e) {
                                $this->messageManager->addError(__('Something went wrong '.$e->getMessage()));
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

    public function getVariables(\Magento\Sales\Model\Order $order)
    {
        $billingAddress = $order->getBillingAddress();
        $codes = [
            'firstname' => $billingAddress->getFirstname(),
            'middlename' => $billingAddress->getMiddlename(),
             'lastname' => $billingAddress->getLastname(),
             'fax' => $billingAddress->getFax(),
             'postal' =>$billingAddress->getPostcode(),
             'city' =>$billingAddress->getCity(),
             'email' => $billingAddress->getEmail(),
             'order_id' =>$order->getIncrementId(),
             'name' =>$billingAddress->getFirstname().' '.$billingAddress->getLastname()
        ];

        return $codes;
    }
    public function newVendorOrdervar($product, $vendor, $orderId)
    {
        $codes = [
            'name' => $vendor->getName(),
            'email' =>$vendor->getEmail(),
        'productname' =>$product->getName(),
        'sku' =>$product->getSku(),
        'order_id' => $orderId

        ];

        return  $codes;
    }
}
