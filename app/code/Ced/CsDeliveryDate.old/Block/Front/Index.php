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

namespace Ced\CsDeliveryDate\Block\Front;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;

/**
 * Class Index
 * @package Ced\CsDeliveryDate\Block\Front
 */
class Index extends \Magento\Sales\Block\Order\Info
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
     * @param Registry $registry
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollectionFactory
     * @param \Ced\CsOrder\Model\VordersFactory $vordersFactory
     * @param TemplateContext $context
     * @param Registry $registry
     * @param PaymentHelper $paymentHelper
     * @param AddressRenderer $addressRenderer
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollectionFactory,
        \Ced\CsOrder\Model\VordersFactory $vordersFactory,
        TemplateContext $context,
        Registry $registry,
        PaymentHelper $paymentHelper,
        AddressRenderer $addressRenderer,
        array $data = []
    ){
        parent::__construct($context, $registry, $paymentHelper, $addressRenderer, $data);

        $this->registry = $registry;
        $this->session = $session;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->vordersFactory = $vordersFactory;
    }

}
