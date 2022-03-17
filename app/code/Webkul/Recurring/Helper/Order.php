<?php
/**
 * Webkul Software.
 *
 * @category   Webkul
 * @package    Webkul_Recurring
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Helper;

use Magento\Store\Model\App\Emulation;

/**
 * Webkul Recurring Helper Order
 */
class Order extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * This variable is set the store scope for order
     *
     * @var Magento\Store\Model\App\Emulation;
     */
    private $emulate;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;
    
    /**
     * @param Magento\Framework\App\Helper\Context $context
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Magento\Catalog\Model\ProductFactory $productFactory,
     * @param Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
     * @param Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
     * @param Magento\Customer\Model\CustomerFactory $customerFactory,
     * @param Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
     * @param Magento\Sales\Model\Order $order
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Emulation $emulate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Order $order
    ) {
        $this->emulate                  = $emulate;
        $this->jsonHelper               = $jsonHelper;
        $this->storeManager             = $storeManager;
        $this->productFactory           = $productFactory;
        $this->cartRepositoryInterface  = $cartRepositoryInterface;
        $this->cartManagementInterface  = $cartManagementInterface;
        $this->customerFactory          = $customerFactory;
        $this->customerRepository       = $customerRepository;
        $this->order                    = $order;
        parent::__construct($context);
    }
    
    /**
     * Get product
     *
     * @param integer $productId
     * @return void
     */
    private function getProduct($productId)
    {
        return $this->productFactory->create()->load($productId);
    }
    
    /**
     * Create Order On Your Store
     *
     * @param array $orderData
     * @return array
     *
     */
    public function createMageOrder($order, $planName)
    {
        $storeId = $order->getStoreId();
        $cartId = $this->cartManagementInterface->createEmptyCart(); //Create empty cart
        $quote = $this->cartRepositoryInterface->get($cartId); // load empty cart quote
        $quote->setStoreId($storeId);
        $environment  = $this->emulate->startEnvironmentEmulation($storeId);

        $customerId = $order->getCustomerId();
        $shippingAddress = ($order->getShippingAddress() && count($order->getShippingAddress()->getData())) ?
                            $order->getShippingAddress() :
                            $order->getBillingAddress();
        $billingAddress = $order->getBillingAddress();
        // if you have allready buyer id then you can load customer directly
        $customer   = $this->customerRepository->getById($customerId);
        // if you have allready buyer id then you can load customer directly
        
        $quote->setCurrency();
        $quote->assignCustomer($customer); //Assign quote to customer

        $additionalOptions [] = [
            'label' => __("Subscription"),
            'value' => $planName
        ];
        //add items in quote
        foreach ($order->getAllItems() as $item) {
            $product = $this->getProduct($item->getProductId());
            $product->setPrice($item->getPrice());
            $quote->addProduct($product, (int)($item->getQty()));
        }

        $cartData = $quote->getAllVisibleItems();
        foreach ($cartData as $item) {
            $item->addOption(
                [
                    'product_id' => $item->getProductId(),
                    'code' => 'custom_additional_options',
                    'value' => $this->jsonHelper->jsonEncode($additionalOptions)
                ]
            );
        }
        
        //Set Address to quote
        $quote->getBillingAddress()->addData($billingAddress->getData());
        $quote->getShippingAddress()->addData($shippingAddress->getData());
 
        // Collect Rates and Set Shipping & Payment Method
 
        $paymentMethod = $order->getPayment()->getMethodInstance()->getCode();
        $shippingAddress=$quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
                        ->collectShippingRates()
                        ->setShippingMethod($order->getShippingMethod()); //shipping method
        $quote->setPaymentMethod($paymentMethod); //payment method
        $quote->setInventoryProcessed(false); //not effetc inventory
        $paymentMethodArray = [
            'recurringstripe',
            'recurringpaypal'
        ];
        if (in_array($paymentMethod, $paymentMethodArray)) {
            $paymentMethod = "recurringorders";
        }
        // Set Sales Order Payment
        $quote->getPayment()->importData(['method' => $paymentMethod]);
        $quote->save(); //Now Save quote and your quote is ready
 
        // Collect Totals
        $quote->collectTotals();
 
        // Create Order From Quote
        $quote = $this->cartRepositoryInterface->get($quote->getId());
        $orderId = $this->cartManagementInterface->placeOrder($quote->getId());
        $order = $this->order->load($orderId);
        
        $order->setEmailSent(0);
        $increment_id = $order->getRealOrderId();
        if ($order->getEntityId()) {
            $result =   [
                'error' => 0,
                'order_id' => $order->getRealOrderId(),
                'id' => $order->getId()
            ];
        } else {
            $result =   [
                'error' => 1,
                'msg' => 'Your custom message'
            ];
        }
        $this->emulate->stopEnvironmentEmulation($environment);
        return $result;
    }
}
