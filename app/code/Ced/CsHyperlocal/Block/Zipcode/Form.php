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
 * @package     Ced_CsHyperlocal
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsHyperlocal\Block\Zipcode;

/**
 * Class Form
 * @package Ced\CsHyperlocal\Block\Shiparea
 */
class Form extends \Magento\Framework\View\Element\Template
{

    /**
     * Form constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry
    )
    {
        parent::__construct($context);
        $this->setUseConfirm(true);
        $this->setUseAjax(true);
        $this->setDefaultStoreName(__('Default Values'));
        $this->registry = $registry;

        $zipcodeData = $this->registry->registry('zipcode_data');
        if ($zipcodeData) {
            $this->addData($zipcodeData);
        }
    }
}
