<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul Software Private Limited
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Helper;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;

/**
 * Stripe data helper.
 */
class Stripe extends \Magento\Framework\App\Helper\AbstractHelper
{
    const METHOD_CODE = \Webkul\Recurring\Model\Stripe\PaymentMethod::CODE;

    const MAX_SAVED_CARDS = 30;

    const CARD_IS_ACTIVE = 1;

    const CARD_NOT_ACTIVE = 0;

    /**
     * @var Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * Customer session.
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Locale\Resolver
     */
    protected $resolver;

    /**
     * @var \Magento\Framework\View\Element\Template
     */
    protected $template;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * Constructor
     *
     * @param Session $customerSession
     * @param \Magento\Framework\App\Helper\Context $context
     * @param FormKeyValidator $formKeyValidator
     * @param DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\View\Element\Template $template
     * @param \Magento\Framework\Locale\Resolver $resolver
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     */
    public function __construct(
        Session $customerSession,
        \Magento\Framework\App\Helper\Context $context,
        FormKeyValidator $formKeyValidator,
        DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\View\Element\Template $template,
        \Magento\Framework\Locale\Resolver $resolver,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\HTTP\Client\Curl $curl
    ) {
        $this->date = $date;
        $this->customerSession = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->template =  $template;
        $this->resolver =  $resolver;
        $this->encryptor = $encryptor;
        $this->curl = $curl;
        parent::__construct($context);
    }
    
    /**
     * Function to get Config Data.
     *
     * @return string
     */
    public function getConfigValue($field = false)
    {
        if ($field) {
            if ($field == 'api_secret_key' || $field == 'api_publish_key') {
                return $this->encryptor->decrypt(
                    $this->scopeConfig->getValue(
                        'payment/'.self::METHOD_CODE.'/'.$field,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    )
                );
            } else {
                return $this->scopeConfig->getValue(
                    'payment/'.self::METHOD_CODE.'/'.$field,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
            }
        } else {
            return;
        }
    }

    /**
     * Check if payment method active.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->getConfigValue('active');
    }

    /**
     * This model is used to cancel the paypal recurring payment
     *
     * @param object $model
     * @return bool
     */
    public function cancelSubscriptions($model)
    {
        /*
         * set api key for payment  >> sandbox api key or live api key
         */
        \Stripe\Stripe::setApiKey($this->getConfigValue('api_secret_key'));
        $subscriptionId = $model->getRefProfileId();
        $subscription = \Stripe\Subscription::retrieve($subscriptionId);
        if (isset($subscription["id"])) {
            $subscription->cancel();
            return true;
        }
        return false;
    }

    /**
     * Get media url
     *
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Get the current set locale code from configuration.
     *
     * @return string
     */
    public function getLocaleFromConfiguration()
    {
        return $this->resolver->getLocale();
    }

    /**
     * Returns the locale value exist in stripe api
     * other wise return "auto"
     *
     * @return string
     */
    public function getLocaleForStripe()
    {
        $configLocale = $this->getLocaleFromConfiguration();
        if ($configLocale) {
            $temp = explode('_', $configLocale);
            if (isset($temp['0'])) {
                $configLocale = $temp['0'];
            }
        }
        $stripeLocale = $this->matchCodeSupportedByStripeApi($configLocale);
        return $stripeLocale;
    }

    /**
     * Matches the configuration locale to the locale exixt in strip api
     *
     * @param string $configLocale
     * @return string
     */
    public function matchCodeSupportedByStripeApi($configLocale)
    {
        $localeArray = [
           "zh",
           "da",
           "nl",
           "en",
           "fi",
           "fr",
           "de",
           "it",
           "ja",
           "no",
           "es",
           "sv"
        ];
        if (in_array($configLocale, $localeArray)) {
            return $configLocale;
        }
        return "auto";
    }

    /**
     * Get whether customer exists or not on stripe
     *
     * @param string $custID
     * @return boolean
     */
    public function customerExist($custID = null)
    {
        try {
            $secretKey = $this->getConfigValue('api_secret_key');
            $url = "https://api.stripe.com/v1/customers/".$custID;
            $headers =['Authorization: Bearer '.$secretKey,];
            $arr = [
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTPHEADER =>$headers,
                CURLOPT_HEADER => true,
                CURLOPT_RETURNTRANSFER => true,
            ];
            $this->curl->addHeader('Authorization: Bearer ', $secretKey);
            $this->curl->setOptions($arr);
            $this->curl->get($url);
            if ($this->curl->getStatus() == 200) {
                return 1;
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
}
