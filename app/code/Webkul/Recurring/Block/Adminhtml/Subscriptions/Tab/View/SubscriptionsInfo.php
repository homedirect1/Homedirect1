<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Block\Adminhtml\Subscriptions\Tab\View;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;

/**
 * Adminhtml customer view personal information sales block.
 */
class SubscriptionsInfo extends \Magento\Backend\Block\Template
{
    /**
     * @var Magento\Sales\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var subscriptions
     */
    protected $subscriptions;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Webkul\Recurring\Model\SubscriptionTypeFactory
     */
    protected $planFactory;

    /**
     * Date time
     *
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Registry $registry
     * @param UrlInterface $urlBuilder
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Webkul\Recurring\Model\SubscriptionTypeFactory $planFactory
     * @param Order $order
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Registry $registry,
        UrlInterface $urlBuilder,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Webkul\Recurring\Model\SubscriptionTypeFactory $planFactory,
        Order $order,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->order =  $order;
        $this->planFactory =  $planFactory;
        $this->productFactory = $productFactory;
        $this->customerFactory = $customerFactory;
        $this->coreRegistry = $registry;
        $this->dateTime = $dateTime;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get subscription started date
     *
     * @return string
     */
    public function getStartDate()
    {
        return $this->formatDate(
            $this->getSubscriptions()->getStartDate(),
            \IntlDateFormatter::FULL,
            false
        );
    }

    /**
     * Get subscription data
     *
     * @return object
     */
    public function getSubscriptions()
    {
        return $this->coreRegistry->registry('subscriptions_data');
    }
    
    /**
     * Get order url
     *
     * @return string
     */
    public function getOrderUrl()
    {
        return $this->urlBuilder->getUrl(
            'sales/order/view',
            ['order_id' => $this->getSubscriptions()->getOrderId()]
        );
    }

    /**
     * Get order id
     *
     * @return string
     */
    public function getOrderId()
    {
        return '#'.$this->order->load(
            $this->getSubscriptions()->getOrderId()
        )->getIncrementId();
    }

    /**
     * Get Product
     *
     * @return object
     */
    public function getProduct()
    {
        return $this->productFactory->create()->load(
            $this->getSubscriptions()->getProductId()
        );
    }

    /**
     * Get customer
     *
     * @return object
     */
    public function getCustomer()
    {
        return $this->customerFactory->create()->load(
            $this->getSubscriptions()->getCustomerId()
        );
    }

    /**
     * Get customer edit url
     *
     * @return string
     */
    public function getCustomerUrl($id)
    {
        return $this->urlBuilder->getUrl(
            'customer/index/edit',
            ['id' => $id]
        );
    }

    /**
     * Get name of Subscription
     *
     * @return object
     */
    public function getSubscriptionType()
    {
        return $this->planFactory->create()->load(
            $this->getSubscriptions()->getPlanId()
        )->getName();
    }

    /**
     * Get subscription creation date
     *
     * @return string
     */
    public function getCreateDate()
    {
        return $this->formatDate(
            $this->getSubscriptions()->getCreatedAt(),
            \IntlDateFormatter::FULL,
            false
        );
    }

    /**
     * Get Status
     *
     * @param boolean $status
     * @return string
     */
    public function getStatus($status)
    {
        if ($status == 1) {
            return __("Subscribed");
        }
        return __("UnSubscribed");
    }
}
