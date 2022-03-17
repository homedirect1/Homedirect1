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

/**
 * Webkul Recurring Helper Paypal
 */
class Paypal extends \Magento\Framework\App\Helper\AbstractHelper
{
    const  MOD_ENABLE       = "recurring/general_settings/enable";
    const  SANDBOX          = "payment/recurringpaypal/sandbox";
    const  USERNAME         = "payment/recurringpaypal/api_username";
    const  PASSWORD         = "payment/recurringpaypal/api_password";
    const  SIGNATURE        = "payment/recurringpaypal/api_signature";
    const  URL              = "https://api-3t.";
    const  URL_COMPLETE     = "paypal.com/nvp";
    const  PAYPAL_STATUS    = 'ManageRecurringPaymentsProfileStatus';
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $subscriptions;

    /**
     * @var \Zend\Uri\Uri
     */
    protected $zendUri;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Webkul\Recurring\Model\Subscriptions $subscriptions
     * @param \Zend\Uri\Uri $zendUri
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Webkul\Recurring\Model\Subscriptions $subscriptions,
        \Zend\Uri\Uri $zendUri
    ) {
        $this->curl                     = $curl;
        $this->subscriptions            = $subscriptions;
        $this->zendUri                  = $zendUri;
        parent::__construct($context);
    }

    /**
     * This function returns the recurring cancel url
     *
     * @param integer $isSandBox
     * @return void
     */
    protected function getRecurringCancelUrl($isSandBox)
    {
        return self::URL.(($isSandBox) ? "sandbox." : "").self::URL_COMPLETE;
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
            $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Calculate duration
     *
     * @param int $duration
     * @return array
     */
    public function calculateDuration($duration)
    {
        if ($duration >= 7 && $duration < 30 && $duration % 7 == 0) {
            return [
                'peroid'    => 'Week',
                'frequency' => (int)$duration / 7
            ];
        } elseif ($duration >= 30 && $duration < 365 && $duration % 30 == 0) {
            return [
                'peroid'    => 'Month',
                'frequency' => (int)$duration / 30
            ];
        } elseif ($duration == 365) {
            return [
                'peroid'    => 'Year',
                'frequency' => (int)$duration / 365
            ];
        } elseif ($duration >= 1 && $duration < 365 && $duration % 1 == 0) {
            return [
                'peroid'    => 'Day',
                'frequency' => (int)$duration / 1
            ];
        }
        return [
            'peroid'    => '',
            'frequency' => 0
        ];
    }

    /**
     * This function is used to return the paypal credentials
     *
     * @return array
     */
    private function getCredentials()
    {
        $isSandBox          = $this->getConfig(self::SANDBOX);
        $userName           = $this->getConfig(self::USERNAME);
        $password           = $this->getConfig(self::PASSWORD);
        $signature          = $this->getConfig(self::SIGNATURE);
        return [
            $isSandBox, $userName, $password, $signature
        ];
    }

    /**
     * This model is used to cancel the paypal recurring payment
     *
     * @param object $model
     * @return bool
     */
    public function cancelSubscriptions($model)
    {
        list($isSandBox, $userName, $password, $signature) = $this->getCredentials();
        $postData = [
            'USER' => $userName,
            'PWD' => $password,
            'SIGNATURE' => $signature,
            'VERSION' => '86',
            'METHOD' => self::PAYPAL_STATUS,
            'PROFILEID' => $model->getRefProfileId(),
            'ACTION'    => 'Cancel'
        ];
        $endPointUrl = $this->getRecurringCancelUrl($isSandBox);
        
        $this->curl->post($endPointUrl, $postData);
        $response = $this->curl->getBody();
        $responseData = $this->getParsedString($response);
        
        if ($responseData['ACK'] == "Success") {
            return true;
        }
        return false;
    }

    /**
     * Check paypal details are valid or not
     *
     * @return void
     */
    public function checkPaypalDetails()
    {
        list($isSandBox, $userName, $password, $signature) = $this->getCredentials();
        
        if (!$this->getConfig(self::MOD_ENABLE)) {
            return false;
        }
        if ($userName != "" && $password != "" && $signature != "") {
            return true;
        }
        return false;
    }

    /**
     * This function parses a query string into variables
     *
     * @param string $response
     * @return array
     */
    public function getParsedString($response)
    {
        $this->zendUri->setQuery($response);
        $responseData = $this->zendUri->getQueryAsArray();
        return $responseData;
    }
}
