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
 * @package   Ced_StorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\StorePickup\Block;

/**
 * Class Email
 * @package Ced\StorePickup\Block
 */
class Email extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \Ced\StorePickup\Model\StoreInfoFactory
     */
    public $storeInfoFactory;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    public $countryFactory;

    /**
     * Email constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\Order $order
     * @param \Ced\StorePickup\Model\StoreInfoFactory $storeInfoFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\Order $order,
        \Ced\StorePickup\Model\StoreInfoFactory $storeInfoFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory
    )
    {
        $this->order = $order;
        $this->storeInfoFactory = $storeInfoFactory;
        $this->countryFactory = $countryFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrderDetails()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->order->load($orderId);
        return $order;
    }
}
