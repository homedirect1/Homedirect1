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

namespace Ced\CsHyperlocal\Block\Adminhtml\Shiparea\Edit;

class Tab extends \Magento\Backend\Block\Widget\Tab
{
    public $_template = 'Ced_CsHyperlocal::shiparea_form.phtml';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Tab constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->registry = $registry;

    }

    /**
     *  get current location data
     */
    public function getCurrentLocation()
    {
        return $this->registry->registry('ced_shiparea_data');
    }
}
