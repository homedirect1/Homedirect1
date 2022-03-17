<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Model;

use Magento\Sales\Model\Order as OrderModel;
use Webkul\Recurring\Model\SubscriptionsFactory  as SubscriptionsFactory;
use Magento\Framework\Stdlib\DateTime\DateTime  as Date;

class UpdateRecurringAmount
{
    const  USERNAME         = "payment/recurringpaypal/api_username";
    const  PASSWORD         = "payment/recurringpaypal/api_password";
    const  SIGNATURE        = "payment/recurringpaypal/api_signature";
    const  SANDBOX          = "payment/recurringpaypal/sandbox";
    const  URL              = "https://api-3t.";
    const  URL_COMPLETE     = "paypal.com/nvp";

    /**
     * @var PageFactory
     */
    protected $helper;

    /**
     * for logging.
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var Date
     */
    private $date;
    
    /**
     * @var SubscriptionsFactory
     */
    private $subscriptionsFactory;
    
    /**
     * @var Order
     */
    private $orderModel;
    
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param OrderModel $orderModel
     * @param Date $date
     * @param Subscriptions $subscriptions
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Webkul\Recurring\Helper\Paypal $helper
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        OrderModel $orderModel,
        Date $date,
        SubscriptionsFactory $subscriptionsFactory,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Webkul\Recurring\Helper\Paypal $helper
    ) {
        $this->logger                   = $logger;
        $this->date                     = $date;
        $this->subscriptionsFactory     = $subscriptionsFactory;
        $this->orderModel               = $orderModel;
        $this->curl                     = $curl;
        $this->helper                   = $helper;
    }

    /**
     * Print log in cron.log
     *
     * @param string $message
     * @return void
     */
    private function printLog($message)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($message);
    }

    /**
     * Cron job executed 1 time per day to update next recurring amount of subscription on paypal
     */
    public function updateNextAmounts()
    {
        try {
            $orderIds = [];
            $subscriptionsCollection = $this->subscriptionsFactory->create()
                ->getCollection()
                ->addFieldToFilter("status", true)
                ->addFieldToFilter("ref_profile_id", ['neq' => null])
                ->addFieldToFilter("discount_managed", false);
            foreach ($subscriptionsCollection as $subscriptionsModel) {
                $result            = false;
                $subscriptionId    = $subscriptionsModel->getId();
                $planId            = $subscriptionsModel->getPlanId();
                $orderId           = $subscriptionsModel->getOrderId();
                $startDate         = $subscriptionsModel->getStartDate();
                $refProfileId      = $subscriptionsModel->getRefProfileId();
                $order             = $this->orderModel->load($orderId);
                if (!in_array($orderId, $orderIds)) {
                    if ($order->getPayment()->getMethodInstance()->getCode() == 'recurringpaypal') {
                        $result = $this->updatePaypalRecurringProfile($refProfileId, $orderId, $startDate);
                    }
                    if ($result) {
                        $subscriptionsModel->setDiscountManaged(true);
                        $this->saveModel($subscriptionsModel);
                    }
                    $orderIds[] = $orderId;
                }
            }
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
            $this->printLog($e->getMessage());
        }
    }

    /**
     * Update amount(remove discount if any) in future paypal recurring payments
     *
     * @param integer $planId
     * @param integer $orderId
     * @param string $startDate
     * @return boolean
     */
    private function updatePaypalRecurringProfile($refProfileId, $orderId, $startDate)
    {
        $canUpdate = false;
        $date      = $this->date->gmtDate('m/d/Y');
        $dateFrom  =  date_create($startDate);
        $dateTo    =  date_create($date);
        if ($dateFrom < $dateTo) {
            $canUpdate = true;
        }
        if ($canUpdate == true) {
            $order = $this->orderModel->load($orderId);
            foreach ($order->getAllItems() as $item) {
                $itemAmount = $item->getPrice();
                $data = [
                    'USER'          => $this->helper->getConfig(self::USERNAME),
                    'PWD'           => $this->helper->getConfig(self::PASSWORD),
                    'SIGNATURE'     => $this->helper->getConfig(self::SIGNATURE),
                    'METHOD'        => 'UpdateRecurringPaymentsProfile',
                    "VERSION"       => '86',
                    'PROFILEID'     => $refProfileId,
                    'AMT'           => number_format($itemAmount, 2),
                    'CURRENCYCODE'  => $order->getOrderCurrencyCode()
                ];
                $endPointUrl =  $this->getEndPoint();
                $this->curl->post($endPointUrl, $data);
                $response = $this->curl->getBody();
                $responseData = $this->helper->getParsedString($response);
                if ($responseData['ACK'] == 'Success') {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get the end point of paypal (url)
     *
     * @return string
     */
    private function getEndPoint()
    {
        $isSandBox          = $this->helper->getConfig(self::SANDBOX);
        $endPointUrl        = self::URL;
        $endPointUrl       .= (($isSandBox) ? "sandbox." : "");
        $endPointUrl       .= self::URL_COMPLETE;
        return $endPointUrl;
    }

    /**
     * Saves the Model
     *
     * @param \Webkul\Recurring\Model\Subscriptions $model
     * @return void
     */
    private function saveModel($model)
    {
        $model->save();
    }
}
