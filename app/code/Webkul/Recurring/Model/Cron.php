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

use Magento\Store\Model\App\Emulation;
use Magento\Sales\Model\Order as OrderModel;
use Webkul\Recurring\Model\SubscriptionType  as PlanType;
use Webkul\Recurring\Model\Term  as Term;
use Webkul\Recurring\Model\MappingFactory  as MappingFactory;
use Webkul\Recurring\Model\SubscriptionsFactory  as SubscriptionsFactory;
use Magento\Framework\Stdlib\DateTime\DateTime  as Date;
use Magento\Quote\Api\CartManagementInterface  as CartManagement;
use Magento\Quote\Api\CartRepositoryInterface  as CartRepository;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;
use Magento\Catalog\Model\ProductFactory as ProductFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface as StockRegistry;
use Magento\Checkout\Model\CartFactory as CartFactory;

class Cron
{
    /**
     * for logging.
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var PlanType
     */
    private $planType;
    
    /**
     * @var Term
     */
    private $term;
    
    /**
     * @var MappingFactory
     */
    private $mappingFactory;
    
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
     * @var CartManagement
     */
    private $CartManagement;
    
    /**
     * @var CartRepository
     */
    private $cartManagementInterface;
    
    /**
     * @var CustomerRepository
     */
    private $customerRepositoryInterface;
    
    /**
     * @var ProductRepository
     */
    private $productRepository;
    
    /**
     * @var ProductRepository
     */
    private $productFactory;
    
    /**
     * @var StockRegistry
     */
    private $stockRegistry;
    
    /**
     * @var CartFactory
     */
    private $cartFactory;

    /**
     * This variable is set the store scope for order
     *
     * @var Magento\Store\Model\App\Emulation;
     */
    private $emulate;

    /**
     * @var \Webkul\Recurring\Helper\Order
     */
    protected $orderHelper;

    /**
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param PlanType $planType
     * @param Emulation $emulate
     * @param Term $term
     * @param MappingFactory $mappingFactory
     * @param OrderModel $orderModel
     * @param Date $date
     * @param SubscriptionsFactory $subscriptionsFactory
     * @param CartManagement $cartManagementInterface
     * @param CartRepository $cartRepositoryInterface
     * @param CustomerRepository $customerRepositoryInterface
     * @param ProductRepository $productRepository
     * @param StockRegistry $stockRegistry
     * @param ProductFactory $productFactory
     * @param CartFactory $cartFactory
     * @param \Webkul\Recurring\Helper\Order $orderHelper
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        PlanType $planType,
        Emulation $emulate,
        Term $term,
        MappingFactory $mappingFactory,
        OrderModel $orderModel,
        Date $date,
        SubscriptionsFactory $subscriptionsFactory,
        CartManagement $cartManagementInterface,
        CartRepository $cartRepositoryInterface,
        CustomerRepository $customerRepositoryInterface,
        ProductRepository $productRepository,
        StockRegistry $stockRegistry,
        ProductFactory $productFactory,
        CartFactory $cartFactory,
        \Webkul\Recurring\Helper\Order $orderHelper
    ) {
        $this->orderHelper              = $orderHelper;
        $this->logger                   = $logger;
        $this->emulate                  = $emulate;
        $this->planType                 = $planType;
        $this->term                     = $term;
        $this->mappingFactory           = $mappingFactory;
        $this->date                     = $date;
        $this->subscriptionsFactory     = $subscriptionsFactory;
        $this->orderModel               = $orderModel;
        $this->cartFactory              = $cartFactory;
        $this->cartRepositoryInterface  = $cartRepositoryInterface;
        $this->cartManagementInterface  = $cartManagementInterface;
        $this->customerRepository       = $customerRepositoryInterface;
        $this->productRepository        = $productRepository;
        $this->productFactory           = $productFactory;
        $this->stockRegistry            = $stockRegistry;
    }

    /**
     * Print log in cron.log
     *
     * @param string $message
     * @return void
     */
    public function printLog($message)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($message);
    }
    
    /**
     * Cron job executed 1 time per five minutes to check the offline recurring orders creation
     */
    public function recurringOrder()
    {
        try {
            $orderIds = [];
            $subscriptionsCollection = $this->subscriptionsFactory->create()
                ->getCollection()
                ->addFieldToFilter("status", true);
            foreach ($subscriptionsCollection as $subscriptionsModel) {
                $subscriptionId    = $subscriptionsModel->getId();
                $planId            = $subscriptionsModel->getPlanId();
                $orderId           = $subscriptionsModel->getOrderId();
                $startDate         = $subscriptionsModel->getStartDate();
                if (!in_array($orderId, $orderIds)) {
                    $this->reProcessSubscription($planId, $orderId, $startDate, $subscriptionId);
                    $orderIds[] = $orderId;
                }
            }
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
            $this->printLog($e->getMessage());
        }
    }

    /**
     * Re processing subscription
     *
     * @param integer $planId
     * @param integer $orderId
     * @param string $startDate
     * @param integer $subscriptionId
     * @return void
     */
    private function reProcessSubscription($planId, $orderId, $startDate, $subscriptionId)
    {
        $startDateArray = explode(" ", $startDate);
        $subscriptionType = $this->getSubscriptionType($planId);
        $duration = 0;
        $date = $this->date->gmtDate('m/d/Y');
        if (isset($subscriptionType['type'])) {
            $durationDetails = $this->getDurationDetails($subscriptionType['type']);
            $duration = isset($durationDetails['duration']) ? $durationDetails['duration'] : 0;
            $dateFrom =  date_create($startDateArray[0]);
            $dateTo   =  date_create($date);
            $diff     =  date_diff($dateFrom, $dateTo);
            $reNew    =  $this->canRenewed($diff->format('%a'), $duration, $subscriptionId);
            $paymentMethods = [
                "recurringpaypal",
                "recurringstripe"
            ];
            $this->printLog('reNew '.$reNew);
            $order      = $this->orderModel->load($orderId);
            if ($reNew == true && !in_array($order->getPayment()->getMethodInstance()->getCode(), $paymentMethods)) {
                $this->createOrder($planId, $order, $subscriptionId);
            }
        }
    }

    /**
     * Create order
     *
     * @param integer $planId
     * @param object $order
     * @param integer $subscriptionId
     * @return void
     */
    private function createOrder($planId, $order, $subscriptionId)
    {
        try {
            $plan = $this->getSubscriptionType($planId);
            $result = $this->orderHelper->createMageOrder($order, $plan['name']);
            if (isset($result['error']) && $result['error'] == 0) {
                $this->saveMapping($planId, $result['id'], $subscriptionId);
            }
        } catch (\Exception $e) {
            $this->printLog($e->getMessage());
        }
    }

    /**
     * This function is used for mapping the child order with the subscription
     *
     * @param integer $planId
     * @param integer $orderId
     * @param integer $subscriptionId
     * @return Array
     */
    public function saveMapping($planId, $orderId, $subscriptionId)
    {
        $time = date('Y-m-d H:i:s');
        $model = $this->mappingFactory->create();
        $model->setSubscriptionId($subscriptionId);
        $model->setOrderId($orderId);
        $model->setCreatedAt($time);
        $model->save();
    }

    /**
     * This will decide plan should renew or not.
     *
     * @param integer $serveredDays
     * @param integer $duration
     * @param integer $subscriptionId
     * @return array
     */
    private function canRenewed($serveredDays, $duration, $subscriptionId)
    {
        $this->printLog('serveredDays '.$serveredDays);
        $this->printLog('duration '.$duration);
        $return = false;
        $todayDate = date('Y-m-d');
        $mappingCollection = $this->mappingFactory->create()->getCollection()
            ->addFieldToFilter('subscription_id', $subscriptionId)
            ->addFieldToFilter('created_at', ['like' => $todayDate.'%']);
        if ($mappingCollection->getSize()) {
            return $return;
        }
        if ($duration == 0) {
            $return = false;
        } elseif ($serveredDays == 0) {
            $return = false;
        } elseif ($duration <= $serveredDays && ($serveredDays % $duration == 0)) {
            $return = true;
        }
        return $return;
    }

    /**
     * This function returns the subscription type details in array form
     *
     * @param integer $planId
     * @return array
     */
    public function getSubscriptionType($planId)
    {
        return $this->planType->load($planId)->getData();
    }

    /**
     * This function returns the particular plans duration details
     *
     * @param integer $durationId
     * @return array
     */
    private function getDurationDetails($durationId)
    {
        return $this->term->load($durationId)->getData();
    }
}
