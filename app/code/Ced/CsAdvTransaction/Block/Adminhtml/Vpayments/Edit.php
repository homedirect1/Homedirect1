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
 * @package     Ced_CsAdvTransaction
 * @author     CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Block\Adminhtml\Vpayments;

use Magento\Backend\Block\Widget\Context;

/**
 * Class Edit
 * @package Ced\CsAdvTransaction\Block\Adminhtml\Vpayments
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var null
     */
    protected $_availableMethods = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;

    /**
     * @var \Ced\CsAdvTransaction\Model\OrderfeeFactory
     */
    public $orderfeeFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $_scopeConfig;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    public $priceCurrency;

    /**
     * @var \Ced\CsAdvTransaction\Model\FeeFactory
     */
    public $feeFactory;

    /**
     * Edit constructor.
     * @param \Ced\CsAdvTransaction\Model\OrderfeeFactory $orderfeeFactory
     * @param \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        \Ced\CsAdvTransaction\Model\OrderfeeFactory $orderfeeFactory,
        \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        Context $context,
        array $data = []
    )
    {
        $this->_storeManager = $context->getStoreManager();
        $this->orderfeeFactory = $orderfeeFactory;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->feeFactory = $feeFactory;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $amount = $this->getRequest()->getPostValue();
        $vid = $this->getRequest()->getParam('vendor_id');
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_vpayments';
        $this->_blockGroup = 'Ced_CsAdvTransaction';
        parent::_construct();
        $url = $this->getUrl('csadvtransaction/pay/order', ['vendor_id' => $vid]);
        $this->buttonList->update('save', 'label', __('Pay Offline'));
        $this->updateButton('back', 'onclick', "setLocation('" . $url . "')");
    }


    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getHeaderText()
    {

        return __("Credit Amount");

    }
}