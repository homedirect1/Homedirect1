<?php
/**
 * Webkul Software.
 *
 * @category   Webkul
 * @package    Webkul_Recurring
 * @author     Webkul Software Private Limited
 * @copyright  Copyright Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Helper;

use Webkul\Recurring\Model\Subscriptions  as Subscriptions;

/**
 * Webkul Recurring Helper Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Webkul\Recurring\Logger\Logger
     */
    private $logger;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var subscriptions
     */
    protected $subscriptions;

    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $coreSession;
    
    /**
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Session\SessionManager $coreSession
     * @param Subscriptions $subscriptions
     * @param \Webkul\Recurring\Model\Plans\DataProvider $dataProvider
     * @param \Webkul\Recurring\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product $product,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Session\SessionManager $coreSession,
        Subscriptions $subscriptions,
        \Webkul\Recurring\Model\Plans\DataProvider $dataProvider,
        \Webkul\Recurring\Logger\Logger $logger
    ) {
        $this->product          = $product;
        $this->coreSession      = $coreSession;
        $this->subscriptions    = $subscriptions;
        $this->dataProvider     = $dataProvider;
        $this->checkoutSession  = $checkoutSession;
        $this->jsonHelper       = $jsonHelper;
        $this->customerSession  = $customerSession;
        $this->logger           = $logger;
        parent::__construct($context);
    }

    /**
     * Get cart data
     *
     * @return array
     */
    public function getCartData()
    {
        $additionalOptions = [];
        $quote = $this->checkoutSession->getQuote();
        if ($this->customerSession->create()->isLoggedIn()) {
            $cartData = $quote->getAllVisibleItems();
            foreach ($cartData as $item) {
                if ($customAdditionalOptionsQuote = $item->getOptionByCode('custom_additional_options')) {
                    $options = $this->jsonHelper->jsonDecode(
                        $customAdditionalOptionsQuote->getValue()
                    );
                    foreach ($options as $option) {
                        $additionalOptions["termId"]              =  0;
                        if ($option['label'] == 'Plan Id') {
                            $additionalOptions["planId"]              =  $option['value'];
                        }
                        if ($option['label'] == 'Start Date') {
                            $additionalOptions["startDate"]           = $option['value'];
                        }
                        if ($option['label'] == 'Initial Fee') {
                            $additionalOptions["initialFee"]          = $option['value'];
                        }
                        if ($option['label'] == 'Subscription Charge') {
                            $additionalOptions["subscriptionsCharge"] = $option['value'];
                        }
                    }
                }
            }
        }
        return $additionalOptions;
    }
    /**
     * This function will let us know the product supports subscription or not.
     *
     * @param integer $productId
     * @return void
     */
    public function getRecurring($productId)
    {
        $model = $this->product->load($productId);
        $returnArray = [];
        $returnArray ['subscription'] = $model->getData('subscription');
        return $returnArray;
    }

    /**
     * This function returns the configuration setting data array
     *
     * @return array
     */
    public function getConfigData()
    {
        $returnData = [];
        $returnData['enable'] = $this->getConfig('general_settings/enable');
        return $returnData;
    }

    /**
     * Get Configuration setting values for allowed payment methods to buy subscription
     *
     * @return string
     */
    public function getAllowedPaymentMethods()
    {
        return $this->scopeConfig->getValue(
            'recurring/general_settings/allowedpaymentmethods',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * This function will return the every configuration field value.
     *
     * @param string $field
     * @return string
     */
    public function getConfig($field)
    {
        return  $this->scopeConfig->getValue(
            'recurring/'.$field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * This function will return the all subscription and terms content from model
     *
     * @return array
     */
    public function getSubscriptionContent()
    {
        return $this->dataProvider->toArray();
    }

    /**
     * This function will return customer id if the customer is loggedin
     *
     * @return integer
     */
    public function getIsCustomerloggedIn()
    {
        $customerData =  $this->customerSession->create();
        $groupId    = $customerData->getcustomer_group_id();
        $customerId = $customerData->getcustomer_id();
        return ($customerId || $groupId);
    }

    /**
     * This function will write the data into the log file
     *
     * @param [mix] $data
     * @return void
     */
    public function logDataInLogger($data)
    {
        $this->logger->info($data);
    }
}
