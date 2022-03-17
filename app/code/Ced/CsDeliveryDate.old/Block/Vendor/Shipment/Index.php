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
 * @package     Ced_CsDeliveryDate
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeliveryDate\Block\Vendor\Shipment;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;

/**
 * Class Index
 * @package Ced\CsDeliveryDate\Block\Vendor\Shipment
 */
class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * @var \Magento\Customer\Model\Session
     */
    public $session;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    public $itemCollectionFactory;

    /**
     * @var \Ced\CsOrder\Model\VordersFactory
     */
    public $vordersFactory;

    /**
     * Index constructor.
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollectionFactory
     * @param \Ced\CsOrder\Model\VordersFactory $vordersFactory
     * @param Registry $registry
     * @param TemplateContext $context
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollectionFactory,
        \Ced\CsOrder\Model\VordersFactory $vordersFactory,
        \Magento\Framework\Registry $registry,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->registry = $registry;
        $this->session = $session;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->vordersFactory = $vordersFactory;
    }
}
