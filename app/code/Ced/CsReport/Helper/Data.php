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
 * @package   Ced_CsReport
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsReport\Helper;

use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 * @package Ced\CsReport\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    public $formKey;

    private $session;

    /**
     * Data constructor.
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Customer\Model\Session $session,
        Context $context
    )
    {
        parent::__construct($context);
        $this->formKey = $formKey;
        $this->session = $session;
    }  

    public function getVendorId()
    {
        return $this->session->getVendorId();
    }

    /**
     * @return string
     */
    public function getKey(){
        return $this->formKey->getFormKey();
    }
}
