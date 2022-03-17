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
 * @category  Ced
 * @package   Ced_CsSubAccount
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Helper;

use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 * @package Ced\CsSubAccount\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    public $session;

    /**
     * Data constructor.
     * @param \Magento\Customer\Model\Session $session
     * @param Context $context
     */
    public function __construct(
        \Magento\Customer\Model\Session $session,
        Context $context
    )
    {
        parent::__construct($context);
        $this->session = $session;
    }
}
