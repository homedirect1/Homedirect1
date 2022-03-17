<?php
namespace Knowband\Mobileappbuilder\Observer;
 
use Magento\Framework\Event\ObserverInterface;

class OrderSaveAfter implements ObserverInterface
{    
    
    public function __construct(
            \Magento\Framework\App\Helper\Context $context,
            \Knowband\Mobileappbuilder\Helper\Data $sp_helper,
            \Knowband\Mobileappbuilder\Model\OrderStatus $sp_orderModel
            ) {
        $this->sp_helper = $sp_helper;
        $this->sp_orderModel = $sp_orderModel;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $push_notification_settings = $this->sp_helper->getSettings('push_notification_settings');
            if (isset($push_notification_settings['firebase_server_key']) && trim($push_notification_settings['firebase_server_key'] != '')) {
                $order = $observer->getOrder();
                $order_id = $order->getId();
                $email = $order->getCustomerEmail();
                $status = $order->getStatus();
                if ($order_details_id = $this->sp_helper->getOrderStatusIdByOrderId($order_id)) {
                    $order_detail_model = $this->sp_orderModel->load($order_details_id);
                    if ($order_detail_model->getData('order_status') != $status) {
                        $order_detail_model->setData('order_status', $status);
                        $order_detail_model->setData('date_upd', $this->sp_helper->getDate());
                        $order_detail_model->save();
                        if (isset($push_notification_settings['order_status_change_enable']) && $push_notification_settings['order_status_change_enable'] == '1') {
                            $this->sp_helper->sendNotificationRequest('order_status_change', $email, $order_id, $status);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
        }
    }   
}
