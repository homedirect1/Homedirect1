<?php

namespace Ced\CsReport\Block\Adminhtml\Payment\Renderer;

/**
 * Class Orderdesc
 * @package Ced\CsReport\Block\Adminhtml\Payment\Renderer
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
     * Orderdesc constructor.
     * @param \Magento\Framework\View\DesignInterface $design
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\Locale\Currency $localeCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Locale\Currency $localeCurrency,
        array $data = []
    )
    {
        $this->_currencyInterface = $localeCurrency;
        $this->design = $design;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     * @throws \Zend_Currency_Exception
     */
    public function render(\Magento\Framework\DataObject $row)
    {

        $amountDesc = $row->getAmountDesc();
        $html = '';
        $area = $this->design->getArea();
        if ($amountDesc != '') {
            $amountDesc = json_decode($amountDesc, true);
            foreach ($amountDesc as $incrementId => $baseNetAmount) {
                $url = 'javascript:void(0);';
                $target = "";
                $amount = $this->_currencyInterface->getCurrency($row->getBaseCurrency())->toCurrency($baseNetAmount);

                $html .= "Order# " . $incrementId . " Amount " . $amount . "\n";
            }
        }

        return $html;
    }

}
