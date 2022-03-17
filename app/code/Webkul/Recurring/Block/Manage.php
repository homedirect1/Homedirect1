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
 namespace Webkul\Recurring\Block;

 use Magento\Framework\View\Element\Template;
 use Magento\Framework\View\Element\Template\Context;
 use Magento\Framework\UrlInterface;

class Manage extends Template
{
    const REQUEST_KEY = "id";
    /**
     * @var \Webkul\Recurring\Model\Subscriptions
     */
    private $subscription;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @var \Magento\Customer\Model\Term
     */
    private $term;

    /**
     * @var \Magento\Customer\Model\SubscriptionType
     */
    private $subscriptionType;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $productModel;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $orderModel;

    /**
     * @var \Webkul\Recurring\Model\SubscriptionTypeFactory
     */
    private $planFactory;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * @var \Webkul\Recurring\Model\Mapping
     */
    private $mapping;

    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Webkul\Recurring\Model\Subscriptions $subscription
     * @param \Webkul\Recurring\Model\Term $term
     * @param UrlInterface $urlBuilder
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Webkul\Recurring\Model\SubscriptionType $subscriptionType
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \Magento\Sales\Model\Order $orderModel
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Webkul\Recurring\Model\Mapping $mapping
     * @param \Webkul\Recurring\Model\SubscriptionTypeFactory $planFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $session,
        \Webkul\Recurring\Model\Subscriptions $subscription,
        \Webkul\Recurring\Model\Term $term,
        UrlInterface $urlBuilder,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Webkul\Recurring\Model\SubscriptionType $subscriptionType,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Webkul\Recurring\Model\Mapping $mapping,
        \Webkul\Recurring\Model\SubscriptionTypeFactory $planFactory,
        array $data = []
    ) {
        $this->mapping            = $mapping;
        $this->customerFactory    = $customerFactory;
        $this->planFactory        =  $planFactory;
        $this->productFactory     = $productFactory;
        $this->subscription       = $subscription;
        $this->subscriptionType   = $subscriptionType;
        $this->term               = $term;
        $this->urlBuilder         = $urlBuilder;
        $this->session            = $session;
        $this->productModel       = $productModel;
        $this->orderModel         = $orderModel;
        parent::__construct($context, $data);
        $this->setCollection($this->getGridCollection());
    }

    /**
     * Get Customer
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
     * Subscription Grid Collection
     *
     * @return object
     */
    public function getGridCollection()
    {
        $collection = $this->subscription->getCollection();
        $collection->addFieldToFilter(
            "customer_id",
            ['eq' => $this->session->getCustomer()->getId()]
        );
        $collection->setOrder("entity_id", "DESC");
        return $collection;
    }
    
    /**
     * Subscription Mapping Grid Collection
     *
     * @return object
     */
    public function getGridChildCollection()
    {
        $collection = $this->mapping->getCollection()->addFieldToFilter(
            'subscription_id',
            $this->getRequest()->getParam(self::REQUEST_KEY)
        );
        return $collection;
    }

    /**
     * Get row url
     *
     * @param integer $id
     * @return string
     */
    public function getRowUrl($id)
    {
        return $this->getUrl(
            "recurring/subscription/view",
            [self::REQUEST_KEY => $id]
        );
    }
    
    /**
     * Back url
     *
     * @return string
     */
    public function getBackUrl()
    {
         return $this->getUrl("recurring/subscription/manage");
    }

    /**
     * Unsubscribe url
     *
     * @return string
     */
    public function getUnsubscribeUrl()
    {
        $id = $this->getRequest()->getParam(self::REQUEST_KEY);
        return $this->getUrl(
            "recurring/subscription/unsubscribe",
            [self::REQUEST_KEY => $id]
        );
    }

    /**
     * Plan type name
     *
     * @param integer $planId
     * @return string
     */
    public function getTypeName($planId)
    {
        $model = $this->subscriptionType->load($planId);
        return $model->getName();
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

    /**
     * product name
     *
     * @param integer $productId
     * @return string
     */
    public function getProductName($productId)
    {
        return $this->productFactory->create()->load($productId)->getName();
    }

    /**
     * Order Id
     *
     * @param integer $orderId
     * @return string
     */
    public function getOrderId($orderId)
    {
        return $this->orderModel->load($orderId)->getIncrementId();
    }

    /**
     * Subscription Order increment id
     *
     * @return void
     */
    public function getWKOrderId()
    {
        return '#'.$this->getOrderId($this->getSubscriptions()->getOrderId());
    }

    /**
     * Prepare pager for rate list
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCollection()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'recurring.list.pager'
            )->setCollection(
                $this->getCollection()
            );
              $this->setChild('pager', $pager);
              $this->getCollection()->load();
        }
            return $this;
    }
    
    /**
     * Get edit page status
     *
     * @return boolean
     */
    public function isEditPage()
    {
        return $this->getRequest()->getParam(self::REQUEST_KEY) ? true : false;
    }

    /**
     * Form data
     *
     * @return array
     */
    public function getFormData()
    {
        $rows = [
        [
            "value" => "",
            "input" => "text",
            "options" => [],
            "name" => "name",
            "index" => "name",
            "class" => "required-entry input-text",
            'label' => __('Rule Name'),
            "isRequired" => 'required'
        ],
        [
            "value" => "",
            "input" => "text",
            "options" => [],
            "name" => "description",
            "index" => "description",
            "class" => "required-entry input-text",
            "label" => __('Description'),
            "isRequired" => 'required',
            "notice" => __('Description ..')
        ]
        ];
    }

    /**
     * Get pager html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Subscriotion row
     *
     * @return object
     */
    public function getSubscriptions()
    {
        $model = $this->subscription->load(
            $this->getRequest()->getParam(self::REQUEST_KEY)
        );
        return $model;
    }

    /**
     * Subscription type
     *
     * @return string
     */
    public function getSubscriptionType()
    {
        return $this->planFactory->create()->load(
            $this->getSubscriptions()->getPlanId()
        )->getName();
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
     * Order Url
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
     * Customer Url
     *
     * @return string
     */
    public function getCustomerUrl()
    {
        return $this->urlBuilder->getUrl(
            'customer/account/*'
        );
    }

    /**
     * Get Product
     *
     * @return object
     */
    public function getProduct()
    {
        return $this->productFactory->create()
        ->load($this->getSubscriptions()->getProductId());
    }
}
