<?php

namespace Ced\CsPromotions\Block\Adminhtml\Promo\Renderer;

/**
 * Grid column block that is displayed only in multistore mode
 *
 * @api
 * @deprecated 100.2.0 in favour of UI component implementation
 * @since 100.0.2
 */
class Approved extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * Set column renderer
     *
     * @param \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer $renderer
     * @return $this
     */
    public function getRenderer()
    {
        return $this->getLayout()->createBlock('Ced\CsPromotions\Block\Adminhtml\Promo\Renderer\Approve')->setColumn($this);
    }
}
