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
namespace Webkul\Recurring\Model\Total\Invoice;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

/**
 * Webkul_Recurring Model Initialfee
 */
class Initialfee extends AbstractTotal
{
    /**
     * Function collect
     *
     * @param Invoice $invoice
     * @return $this
     */
    public function collect(Invoice $invoice)
    {
        $invoice->setInitialFee(0);
        $invoice->setBaseInitialFee(0);
        $amount = $invoice->getOrder()->getInitialFee();
        $invoice->setInitialFee($amount);
        $amount = $invoice->getOrder()->getBaseInitialFee();
        $invoice->setBaseInitialFee($amount);
        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getInitialFee());
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getInitialFee());
        return $this;
    }
}
