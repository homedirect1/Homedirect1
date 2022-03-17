<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Model;

/**
 * Class RulesApplier
 * @package Ced\Affiliate\Model
 */
class RulesApplier extends \Magento\SalesRule\Model\RulesApplier
{

    /**
     * @var \Magento\SalesRule\Model\Utility
     */
    protected $validatorUtility;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * RulesApplier constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory $calculatorFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\SalesRule\Model\Utility $utility
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory $calculatorFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\SalesRule\Model\Utility $utility,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->validatorUtility = $utility;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($calculatorFactory, $eventManager, $utility);
    }

    /**
     * Apply rules to current order item
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\Collection $rules
     * @param bool $skipValidation
     * @param mixed $couponCode
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function applyRules($item, $rules, $skipValidation, $couponCode)
    {
        $address = $item->getAddress();
        $appliedRuleIds = [];
        $check = $this->_scopeConfig->getValue('affiliate/discount/discountto',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $customer = $this->checkoutSession;
        $affiliateId = $customer->getAffiliateId();
        if (!$affiliateId)
            return parent::applyRules($item, $rules, $skipValidation, $couponCode);

        if ($check == 'cartaffiliate' || $check == 'cart'):
            /* @var $rule \Magento\SalesRule\Model\Rule */
            foreach ($rules as $rule) {
                if (!$this->validatorUtility->canProcessRule($rule, $address)) {
                    continue;
                }

                if (!$skipValidation && !$rule->getActions()->validate($item)) {
                    $childItems = $item->getChildren();
                    $isContinue = true;
                    if (!empty($childItems)) {
                        foreach ($childItems as $childItem) {
                            if ($rule->getActions()->validate($childItem)) {
                                $isContinue = false;
                            }
                        }
                    }
                    if ($isContinue) {
                        continue;
                    }
                }

                $this->applyRule($item, $rule, $address, $couponCode);
                $appliedRuleIds[$rule->getRuleId()] = $rule->getRuleId();

                if ($rule->getStopRulesProcessing()) {
                    break;
                }
            }
        endif;

        return $appliedRuleIds;
    }


}
