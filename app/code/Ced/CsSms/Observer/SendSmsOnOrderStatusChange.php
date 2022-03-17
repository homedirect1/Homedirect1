<?php

namespace Ced\CsSms\Observer;

use Magento\Framework\Event\ObserverInterface;

class SendSmsOnOrderStatusChange implements ObserverInterface
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
     * @var \Ced\CsMarketplace\Model\VproductsFactory
     */
    protected $vendorProductsFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * SendSmsOnOrderStatusChange constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Ced\CsSms\Helper\Data $helper
     * @param \Ced\CsMarketplace\Model\VproductsFactory $vendorProductsFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\CsSms\Helper\Data $helper,
        \Ced\CsMarketplace\Model\VproductsFactory $vendorProductsFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->_objectManager = $objectManager;
        $this->helper = $helper;
        $this->vendorProductsFactory = $vendorProductsFactory;
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
                $order = $observer->getOrder();
                if ($this->getHelper()->isSectionEnabled('order_status/enable')) {
                    if ($order instanceof \Magento\Sales\Model\Order) {
                        if ($order->getState() !== $order->getOrigData('state') && $order->getState() != 'new') {

                            $smsto = $this->getHelper()->getTelephoneFromOrder($order);
                            $smsmsg = $this->getHelper()->getOrderStatusChangeMsg($order);
                            $code = $this->getOrderStatusChangeVariables($order);
                            $this->getHelper()->sendSms($smsto, $code,$smsmsg, 'order_status');
                        }
                    }
                }
                /*codes for sending messages to admin*/
                if ($this->getHelper()->isSectionEnabled('order_status/notify') and $this->getHelper()->getAdminOrderStatusTelephone()) {
                    if ($order instanceof \Magento\Sales\Model\Order) {
                        if ($order->getState() !== $order->getOrigData('state') && $order->getState() != 'new') {
                            $state = $this->getHelper()->getStatusName($order->getState());
                            $smsto = $this->getHelper()->getAdminOrderStatusTelephone();
                            $orderIncrementId = $order->getIncrementId();
                            $smsmsg = __('Order status of' . ' ' . $orderIncrementId . ' ' . 'has been changed to ' . $state);
                            $code = [
                                'orderincrementid' => $orderIncrementId,
                                'state' => $state
                            ];
                            try {
                                $this->getHelper()->sendSms($smsto, $code,$smsmsg,'order_status_admin_notify');
                            } catch (\Magento\Framework\Exception $e) {
                                $this->messageManager->addError(__('Something went wrong ' . $e->getMessage()));
                            }
                        }
                    }
                }
                /*codes for sending vendor product status messages*/
                if ($this->getHelper()->isSectionEnabled('order_status/vendornotify') and $this->getHelper()->getAdminOrderStatusTelephone()) {
                    if ($order instanceof \Magento\Sales\Model\Order) {
                        if ($order->getState() !== $order->getOrigData('state') && $order->getState() != 'new') {
                            $orderIncrementId = $order->getIncrementId();
                            $items = $order->getAllVisibleItems();
                            $productIds = array();
                            foreach ($items as $item) {
                                $productIds[] = $item->getProductId();
                            }
                            if (!empty($productIds)) {
                                foreach ($productIds as $ids) {
                                    $vendorId = $this->vendorProductsFactory->create()->getVendorIdByProduct($ids);
                                    if (!empty($vendorId)) {
                                        $smsto = $this->getHelper()->getCountryNumber($vendorId);
                                        if ($smsto != '' && $smsto != null) {
                                            $smsmsg = __('Order status of' . ' ' . $orderIncrementId . ' ' . 'has been changed to ' . $state);
                                            $code = [
                                                'orderincrementid' => $orderIncrementId,
                                                'state' => $state
                                            ];
                                            try {
                                                $this->getHelper()->sendSms($smsto, $code,$smsmsg,'order_status_vendor_notify');
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
        }
    }

    /**
     * @return \Ced\CsSms\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    public function getOrderStatusChangeVariables(\Magento\Sales\Model\Order $order)
    {
        $status = $this->getStatusName($order->getState());
        $billingAddress = $order->getBillingAddress();
        $code = [
            'firstname' => $billingAddress->getFirstname(),
            'middlename' =>  $billingAddress->getMiddlename(),
            'lastname' => $billingAddress->getLastname(),
            'fax' => $billingAddress->getFax(),
            'postal' => $billingAddress->getPostcode(),
            'city' => $billingAddress->getCity(),
            'email' => $billingAddress->getEmail(),
            'order_id' => $order->getIncrementId(),
            'status' => $status,
            'name' => $billingAddress->getFirstname().' '.$billingAddress->getLastname()
        ];

        return $code;

    }

    public function getStatusName($stateCode)
    {
        $statuses = $statuses = $this->_objectManager->get('\Magento\Sales\Model\ResourceModel\Order\Status\Collection')
            ->addStateFilter($stateCode)
            ->toOptionHash();
        if(is_array($statuses))
            return $statuses[$stateCode];
        return false;
    }
}
