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
 * @package     Ced_CsMarketplace
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsReport\Block\Product\Payments\Renderer;

/**
 * Class Orderdesc
 * @package Ced\CsReport\Block\Product\Payments\Renderer
 */
class Orderdesc extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /**
     * @var bool
     */
    protected $_frontend = false;

    /**
     * @var \Magento\Framework\Locale\Currency
     */
    protected $_currencyInterface;

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $design;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Ced\CsMarketplace\Model\VordersFactory
     */
    protected $vordersFactory;

    /**
     * Orderdesc constructor.
     * @param \Magento\Framework\View\DesignInterface $design
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Ced\CsMarketplace\Model\VordersFactory $vordersFactory
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\Locale\Currency $localeCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Ced\CsMarketplace\Model\VordersFactory $vordersFactory,
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Locale\Currency $localeCurrency,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_currencyInterface = $localeCurrency;
        $this->design = $design;
        $this->orderFactory = $orderFactory;
        $this->vordersFactory = $vordersFactory;
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     * @throws \Zend_Currency_Exception
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        try {

            $amountDesc = $row->getAmountDesc();
            $html = '';
            $area = $this->design->getArea();
            if ($amountDesc != '') {

                $amountDesc = json_decode($amountDesc, true);
                foreach ($amountDesc as $incrementId => $baseNetAmount) {
                    if ($incrementId == 'order_id') {
                        //compatibilty with payments made with advance transaction
                        if (is_array($baseNetAmount)) {
                            $testFlag = true;
                            $orderIncrementId = $baseNetAmount['order_id'];
                            $orderBaseAmount = $baseNetAmount['vendor_payment'];
                            $url = 'javascript:void(0);';
                            $target = "";
                            $amount = $this->_currencyInterface->getCurrency($row->getBaseCurrency())->toCurrency($orderBaseAmount);

                            $vorder = $this->orderFactory->create()->loadByIncrementId($orderIncrementId);

                            $orderId = $this->vordersFactory->create()->load($orderIncrementId, 'order_id')->getId();

                            if ($area != 'adminhtml' && $vorder && $vorder->getId()) {

                                $url = $this->getUrl("csmarketplace/vorders/view/", array('order_id' => $orderId));
                                $target = "target='_blank'";
                                $html .= '<label for="order_id_' . $orderIncrementId . '"><b>Order# </b>' . "<a href='" . $url . "' " . $target . " >" . $orderIncrementId . "</a>" . '</label>, <b>Net Earned </b>' . $amount . '<br/>';
                            } else {

                                $html .= '<label for="order_id_' . $orderIncrementId . '"><b>Order# </b>' . $orderIncrementId . '</label>, <b>Amount </b>' . $amount . '<br/>';
                            }
                            return $html;
                        }
                    } else {
                        $url = 'javascript:void(0);';
                        $target = "";
                        $amount = $this->_currencyInterface->getCurrency($row->getBaseCurrency())->toCurrency($baseNetAmount);

                        $vorder = $this->orderFactory->create()->loadByIncrementId($incrementId);

                        $orderId = $this->vordersFactory->create()->load($incrementId, 'order_id')->getId();

                        if ($area != 'adminhtml' && $vorder && $vorder->getId()) {

                            $url = $this->getUrl("csmarketplace/vorders/view/", array('order_id' => $orderId));
                            $target = "target='_blank'";
                            $html .= '<label for="order_id_' . $incrementId . '"><b>Order# </b>' . "<a href='" . $url . "' " . $target . " >" . $incrementId . "</a>" . '</label>, <b>Net Earned </b>' . $amount . '<br/>';
                        } else {

                            $html .= '<label for="order_id_' . $incrementId . '"><b>Order# </b>' . $incrementId . '</label>, <b>Amount </b>' . $amount . '<br/>';
                        }
                    }
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $html;
    }

}