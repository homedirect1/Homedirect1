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
namespace Webkul\Recurring\Model\Total;

class Initialfee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

    const INITIAL_FEE = 'initial_fee';
    const BASE_INITIAL_FEE = 'base_initial_fee';

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

   /**
    * @var null
    */
    protected $quoteValidator = null;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    
    /**
     * @param \Magento\Quote\Model\QuoteValidator $quoteValidator
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Quote\Model\QuoteValidator $quoteValidator,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->quoteValidator = $quoteValidator;
        $this->jsonHelper               = $jsonHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return void
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $initialFee = '';
        $cartData = $quote->getAllVisibleItems();
        foreach ($cartData as $item) {
            if ($additionalOptionsQuote = $item->getOptionByCode('custom_additional_options')) {
                $allOptions = $this->jsonHelper->jsonDecode(
                    $additionalOptionsQuote->getValue()
                );
                foreach ($allOptions as $key => $option) {
                    if ($option['label'] == 'Initial Fee') {
                        $initialFee = ((float)$initialFee) + $option['value'];
                    }
                }
            }
        }
        if ($initialFee != "") {
            $total->setData(static::INITIAL_FEE, $initialFee);
            $total->setData(static::BASE_INITIAL_FEE, $initialFee);
            $total->setTotalAmount(static::INITIAL_FEE, $initialFee);
            $total->setBaseTotalAmount(static::BASE_INITIAL_FEE, $initialFee);
        }
        return $this;
    }
    
    /**
     * Clear totals
     *
     * @param Address\Total $total
     * @return void
     */
    protected function clearValues(Address\Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }
    
    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $initialFee = '';
        $cartData = $quote->getAllVisibleItems();
        foreach ($cartData as $item) {
            if ($additionalOptionsQuote = $item->getOptionByCode('custom_additional_options')) {
                $allOptions = $this->jsonHelper->jsonDecode(
                    $additionalOptionsQuote->getValue()
                );
                foreach ($allOptions as $key => $option) {
                    if ($option['label'] == 'Initial Fee') {
                        $initialFee = ((float)$initialFee) + $option['value'];
                    }
                }
            }
        }
        if ($initialFee != "") {
            return [
            'code' => 'initial_fee',
            'title' => $this->getLabel(),
            'label' => $this->getLabel(),
            'value' =>  $initialFee
            ];
        }
    }
 
    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Initial Fee');
    }
}
