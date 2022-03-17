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
namespace Webkul\Recurring\Model\Payment;

class RecurringOrder extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_METHOD_CASHONDELIVERY_CODE = "recurringorders";

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_CASHONDELIVERY_CODE;

    /**
     * @var boolean
     */
    protected $_isOffline = true;

    /**
     * Returns config Instructions
     *
     * @return string
     */
    public function getInstructions()
    {
        return trim($this->getConfigData("instructions"));
    }
}
