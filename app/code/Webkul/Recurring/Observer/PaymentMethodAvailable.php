<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul Software Private Limited
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Observer;

use Magento\Framework\Event\ObserverInterface;
use Webkul\Recurring\Model\SubscriptionType;
use Webkul\Recurring\Model\Term  as Term;

/**
 * Webkul Recurring PaymentMethodAvailable Observer Model.
 */
class PaymentMethodAvailable implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Webkul\Recurring\Helper\Data
     */
    protected $helper;

    /**
     * @var \Webkul\Recurring\Helper\Paypal
     */
    protected $paypalHelper;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Webkul\Recurring\Helper\Data $helper
     * @param \Webkul\Recurring\Helper\Paypal $paypalHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param SubscriptionType $plans
     * @param Term $term
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Webkul\Recurring\Helper\Data $helper,
        \Webkul\Recurring\Helper\Paypal $paypalHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        SubscriptionType $plans,
        Term $term
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->helper          = $helper;
        $this->jsonHelper      = $jsonHelper;
        $this->paypalHelper    = $paypalHelper;
        $this->customerSession = $customerSession;
        $this->plans           = $plans;
        $this->term            = $term;
    }

    /**
     * Get the frequency of the plan
     *
     * @param integer $planId
     * @return array
     */
    private function getFrequency($planId)
    {
        $typeId         = $this->plans->load($planId)->getType();
        $termDuration   = $this->term->load($typeId)->getDuration();
        $result         = $this->paypalHelper->calculateDuration($termDuration);
        return $result;
    }
    
    /**
     * Get plan id
     *
     * @param object $cartData
     * @param boolean $flag
     * @param integer $planId
     * @return array
     */
    private function getPlanId($cartData, $flag, $planId)
    {
        $startDate      = date("m/d/Y");
        $currentDate    = date("m/d/Y");
        foreach ($cartData as $item) {
            if ($additionalOptionsQuote = $item->getOptionByCode('custom_additional_options')) {
                $allOptions = $this->jsonHelper->jsonDecode(
                    $additionalOptionsQuote->getValue()
                );
                foreach ($allOptions as $key => $option) {
                    if ($option['label'] == 'Plan Id') {
                        $planId = $option['value'];
                    }
                    if ($option['label'] == 'Start Date') {
                        $startDate = $option['value'];
                    }
                    $flag = 1;
                }
            }
        }
        $startDateFlag = false;
        if ($startDate > $currentDate) {
            $startDateFlag = true;
        }
        if ($startDateFlag) {
            $fromDate   = date_create($startDate);
            $toDate     = date_create($currentDate);
            $difference = date_diff($fromDate, $toDate);
            if ($difference->days == 0) {
                $startDateFlag = false;
            }
        }
        return [$planId, $flag, $startDateFlag];
    }

    /**
     * Show payment method according to configuration settings if the order is for subscription
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $data        = $this->helper->getConfigData();
            $paymentMethods = [];
            $paymentMethodsString = $this->helper->getAllowedPaymentMethods();
            $paymentMethods = explode(',', $paymentMethodsString);

            $checkResult = $observer->getEvent()->getResult();
            $methodCode  = $observer->getEvent()->getMethodInstance()->getCode();
            if ($data['enable'] == true && $this->customerSession->isLoggedIn()) {
                /** @var \Magento\Quote\Model\Quote  */
                $quote              = $this->checkoutSession->getQuote();
                $flag               = 0;
                $planId             = $offLinePaypal = 0;
                $startDateFlag      = false;
                if ($quote) {
                    $cartData       = $quote->getAllVisibleItems();
                    list($planId, $flag, $startDateFlag) = $this->getPlanId($cartData, $flag, $planId);
                }
                if ($planId) {
                    $result = $this->getFrequency($planId);
                    if ($result['frequency'] == 0) {
                        $offLinePaypal = 1;
                    }
                }
                $hidePaymentMethod = $this->getShouldHide(
                    $flag,
                    $offLinePaypal,
                    $paymentMethods,
                    $methodCode,
                    $checkResult,
                    $startDateFlag
                );
            } else {
                if ($methodCode == 'recurringpaypal' || $methodCode == 'recurringstripe') {
                    $checkResult->setData('is_available', false);
                }
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "Observer_PaymentMethodAvailable execute : ".$e->getMessage()
            );
        }
    }

    /**
     * Hide payment from frontend flag
     *
     * @param boolean $flag
     * @param boolean $offLinePaypal
     * @param array $paymentMethods
     * @param string $methodCode
     * @return boolean
     */
    private function getShouldHide(
        $flag,
        $offLinePaypal,
        $paymentMethods,
        $methodCode,
        $checkResult,
        $startDateFlag
    ) {
        $isHide = false;
        if ($flag == 1) {
            if ($offLinePaypal) {
                $key = array_search('recurringpaypal', $paymentMethods);
                unset($paymentMethods[$key]);
                $key = array_search('recurringstripe', $paymentMethods);
                unset($paymentMethods[$key]);
            }
            $condition = ($methodCode == 'recurringstripe' && $startDateFlag);
            if (!in_array($methodCode, $paymentMethods) || $condition) {
                $checkResult->setData('is_available', false);
            }
        } else {
            if ($methodCode == "recurringpaypal" || $methodCode == 'recurringstripe') {
                $checkResult->setData('is_available', false);
            }
        }
        
        if ($methodCode == 'recurringpaypal') {
            if (!$this->paypalHelper->checkPaypalDetails()) {
                $checkResult->setData('is_available', false);
            }
        }
        
        return $isHide;
    }
}
