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

namespace Ced\CsAdvTransaction\Block\Adminhtml\Payments;

use Magento\Backend\Block\Widget\Context;

/**
 * Class Details
 * @package Ced\CsAdvTransaction\Block\Adminhtml\Payments
 */
class Details extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;

    /**
     * @var \Ced\CsMarketplace\Model\VpaymentFactory
     */
    public $vpaymentFactory;

    /**
     * @var \Ced\CsAdvTransaction\Model\FeeFactory
     */
    public $feeFactory;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    public $priceCurrency;

    /**
     * Details constructor.
     * @param \Ced\CsMarketplace\Model\VpaymentFactory $vpaymentFactory
     * @param \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        \Ced\CsMarketplace\Model\VpaymentFactory $vpaymentFactory,
        \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        Context $context,
        array $data = []
    )
    {
        $this->_storeManager = $context->getStoreManager();
        $this->vpaymentFactory = $vpaymentFactory;
        $this->feeFactory = $feeFactory;
        $this->priceCurrency = $priceCurrency;
        $this->_controller = '';
        parent::__construct($context, $data);
        $this->_headerText = __('Transaction Details');
        $this->removeButton('reset')
            ->removeButton('delete')
            ->removeButton('save');
        $url = $this->getUrl('csmarketplace/vpayments/index');
        $this->updateButton('back', 'onclick', "setLocation('" . $url . "')");
    }

    /**
     * @return $this|\Magento\Backend\Block\Widget\Form\Container
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setChild('form', $this->getLayout()->createBlock('Ced\CsAdvTransaction\Block\Adminhtml\Payments\Details\Form'));
        return $this;
    }
}