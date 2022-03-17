<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_RemoveCartRule
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Recurring\Plugin\SalesRule\Model;

use Magento\Quote\Model\QuoteRepository;

class RulesApplier
{
    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection
     */
    private $rules;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Webkul\Recurring\Helper\Data
     */
    protected $helper;

    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $rulesFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Webkul\Recurring\Helper\Data $helper
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $rulesFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Webkul\Recurring\Helper\Data $helper,
        QuoteRepository $quoteRepository
    ) {
        $this->ruleCollection = $rulesFactory;
        $this->checkoutSession = $checkoutSession;
        $this->jsonHelper      = $jsonHelper;
        $this->helper          = $helper;
        $this->quoteRepository  = $quoteRepository;
    }

    /**
     * Plugin for applyRules function
     *
     * @param \Magento\SalesRule\Model\RulesApplier $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem  $item
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\Collection $rules
     * @param bool $skipValidation
     * @param mixed $couponCode
     * @return array
     */
    public function aroundApplyRules(
        \Magento\SalesRule\Model\RulesApplier $subject,
        \Closure $proceed,
        $item,
        $rules,
        $skipValidation,
        $couponCode
    ) {
        $configData      = $this->helper->getConfigData();
        $enabled         = $configData['enable'];
        $discountEnabled = $this->helper->getConfig(
            'general_settings/enable_discount'
        );
        if ($enabled && !$discountEnabled) {
            $quote = null;
            $quoteId = $this->checkoutSession->getQuoteId();
            if ($quoteId) {
                $quote = $this->quoteRepository->get($quoteId);
            }
            $flag = 0;
            if ($quote && $quote->getId()) {
                $cartData = $quote->getAllVisibleItems();
                foreach ($cartData as $item) {
                    if ($customAdditionalOptionsQuote = $item->getOptionByCode('custom_additional_options')) {
                        $allOptions = $this->jsonHelper->jsonDecode(
                            $customAdditionalOptionsQuote->getValue()
                        );
                        foreach ($allOptions as $key => $option) {
                            $flag = 1;
                        }
                    }
                }
            }
            if ($flag == 1) {
                $rules = $this->ruleCollection->create()->addFieldToFilter("rule_id", ["eq"=>0]);
            }
        }
        $result = $proceed($item, $rules, $skipValidation, $couponCode);
        return $result;
    }
}
