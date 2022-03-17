<?php
namespace Knowband\Mobileappbuilder\Observer;
 
use Magento\Framework\Event\ObserverInterface;

class SendOrderCreatedNotification implements ObserverInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;
 
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Knowband\Mobileappbuilder\Helper\Data $sp_helper,
        \Knowband\Mobileappbuilder\Model\OrderStatus $sp_orderModel,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->_objectManager = $objectManager;
        $this->sp_helper = $sp_helper;
        $this->sp_orderModel = $sp_orderModel;
        $this->orderRepository = $orderRepository;
    }
 
    /**
     * customer register event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $push_notification_settings = $this->sp_helper->getSettings('push_notification_settings');

        $event = $observer->getEvent();
        $data = $event->getOrderIds();
        

        if (isset($data) && isset($data[0])) {
            $order = $this->orderRepository->get($data[0]);

            $order_id = $order->getId();
            $status = $order->getStatus();
            $email = $order->getCustomerEmail();

            $data = array(
                'order_id' => $order_id,
                'order_status' => $status,
                'date_add' => $this->sp_helper->getDate(),
                'date_upd' => $this->sp_helper->getDate()
            );
            $order_detail_model = $this->sp_orderModel->setData($data);
            $order_detail_model->save();
            if (isset($push_notification_settings['firebase_server_key']) && trim($push_notification_settings['firebase_server_key'] != '')) {
                if (isset($push_notification_settings['order_create_enable']) && $push_notification_settings['order_create_enable'] == '1') {
                    $this->sp_helper->sendNotificationRequest('order_create', $email, $order->getId());
                }
            }
        }
    }
}
