<?php
/**
 * Webkul Software.
 *
 * @category   Webkul
 * @package    Webkul_Recurring
 * @author     Webkul
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\DataModel;

use Webkul\Recurring\Helper\Data as RecurringHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Attachment
 * @inheritDoc
 */
class Recurring implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var RecurringHelper
     */
    protected $recurringHelper;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @param RecurringHelper $recurringHelper
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        RecurringHelper $recurringHelper,
        JsonHelper $jsonHelper
    ) {
        $this->recurringHelper = $recurringHelper;
        $this->jsonHelper      = $jsonHelper;
    }

    /**
     * Get Recurring Helper
     *
     * @return object \Webkul\Recurring\Helper\Data
     */
    public function getRecurringHelper()
    {
        return $this->recurringHelper;
    }

    /**
     * Get Json Helper
     *
     * @return object \Magento\Framework\Json\Helper\Data
     */
    public function getJsonHelper()
    {
        return $this->jsonHelper;
    }
}
