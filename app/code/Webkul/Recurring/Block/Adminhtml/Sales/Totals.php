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
namespace Webkul\Recurring\Block\Adminhtml\Sales;

/**
 * Webkul_Recurring Block Totals
 */
class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $currency;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Model\Currency $currency
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Model\Currency $currency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->currency = $currency;
    }

    /**
     * Get order
     *
     * @return object
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }
    
    /**
     * Source Object
     *
     * @return object
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get currency symbol
     *
     * @return object
     */
    public function getCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

    /**
     * Initialize total
     *
     * @return object
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getOrder();
        $this->getSource();
        if (!$this->getSource()->getInitialFee()) {
            return $this;
        }
        $total = new \Magento\Framework\DataObject(
            [
                'code' => 'initail_fee',
                'value' => $this->getSource()->getInitialFee(),
                'label' => __("Initial Fee"),
            ]
        );
        $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        return $this;
    }
}
