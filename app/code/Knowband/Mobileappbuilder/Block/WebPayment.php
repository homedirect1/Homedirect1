<?php

namespace Knowband\Mobileappbuilder\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class WebPayment extends Template {

    public function __construct(
            Context $context,
            \Magento\Checkout\Model\Session $checkoutSession,
            \Magento\Payment\Model\Config $paymentModelConfig
            )
    {
        $this->quote = $checkoutSession;
        $this->sp_scopeConfig = $context->getScopeConfig();
        $this->paymentModelConfig = $paymentModelConfig;
        parent::__construct($context);
        $this->setTemplate('Knowband_Mobileappbuilder::front/payment.phtml');
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }
    
    public function getQuote()
    {
        return $this->quote->getQuote();
    }

    /**
     * Check payment method model
     *
     * @param Mage_Payment_Model_Method_Abstract|null
     * @return bool
     */
    public function getMethods()
    {
        $session = $this->quote;
        $quote = $session->getQuote();
        $address = $quote->getBillingAddress();
        $country = $address->getCountryId();
        $address->setCountryId($country);
        $quote->save();

        $methods = null;
        if ($methods === null) {
            $quote = $session->getQuote();
            $store = $quote ? $quote->getStoreId() : null;
            $methods = array();
            $payments = $this->paymentModelConfig->getActiveMethods();  
            $methods = [];
            foreach ($payments as $paymentCode => $paymentModel) {
                if ($this->_canUseMethod('', $paymentCode)) {
                    $paymentTitle = $this->sp_scopeConfig
                            ->getValue('payment/' . $paymentCode . '/title');
                    $methods[$paymentCode] = $paymentTitle;
                }
            }
            $this->setData('methods', $methods);
        }

        return $methods;
    }
    
    protected function _sortMethods($a, $b)
    {
        if (is_object($a)) {
            return (int) $a->sort_order < (int) $b->sort_order ? -1 : ((int) $a->sort_order > (int) $b->sort_order ? 1 : 0);
        }
        
        return 0;
    }

    public function getPaymentMethods($store = null)
    {
        $payments = $this->_paymentModelConfig->getActiveMethods();
        $methods = array();
        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = $this->sp_scopeConfig
                ->getValue('payment/'.$paymentCode.'/title', $store);
            $methods[$paymentCode] = array(
                'label' => $paymentTitle,
                'value' => $paymentCode
            );
        }
        return $methods;
    }

    protected function _assignMethod($method)
    {
        $session = $this->quote;
        $method->setInfoInstance($session->getQuote()->getPayment());
        return $this;
    }

    protected function _canUseMethod($method, $code)
    {
        $session = $this->quote;
        return $this->isApplicableToQuote($session->getQuote(), $code);
    }

    public function isApplicableToQuote($quote, $code)
    {
        if (!$this->canUseForCountry($quote->getBillingAddress()->getCountry(), $code)) {
            return false;
        } else {
            return true;
        }
        
    }

    public function canUseForCountry($country, $code)
    {
        /*
          for specific country, the flag will set up as 1
         */
        if ($this->getConfigData('allowspecific', $code) == 1) {
            $availableCountries = explode(',', $this->getConfigData('specificcountry', $code));
            if (!in_array($country, $availableCountries)) {
                return false;
            }
        }
        
        return true;
    }

    public function getConfigData($field, $code, $storeId = null)
    {
        $quote = $this->quote->getQuote();
        if (null === $storeId) {
            $storeId = $quote->getStoreId(); //$this->getStore();
        }
        
        $path = 'payment/' . $code . '/' . $field;
        return $this->sp_scopeConfig->getValue($path);
    }

    /**
     * Retrieve code of current payment method
     *
     * @return mixed
     */
    public function getSelectedMethodCode()
    {
        if ($method = $this->getQuote()->getPayment()->getMethod()) {
            return $method;
        }
        
        return false;
    }

    public function getPaymentMethodFormHtml($_code)
    {
        return $this->getChildHtml('payment.method.' . $_code);
    }
    
}
