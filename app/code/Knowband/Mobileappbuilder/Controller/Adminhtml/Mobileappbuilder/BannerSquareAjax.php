<?php

namespace Knowband\Mobileappbuilder\Controller\Adminhtml\Mobileappbuilder;
use Magento\Framework\View\LayoutFactory;

class BannerSquareAjax extends \Magento\Framework\App\Action\Action
{
    protected $sp_resultRawFactory;
    protected $sp_request;
    protected $sp_helper;
    protected $sp_scopeConfig;
    protected $inlineTranslation;
    protected $sp_transportBuilder;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultRawFactory,
        LayoutFactory $viewLayoutFactory
    ) {
        parent::__construct($context);
        $this->sp_resultRawFactory = $resultRawFactory;
        $this->_viewLayoutFactory = $viewLayoutFactory;
    }

    public function execute() {
        $block = $this->_viewLayoutFactory->create()->createBlock('\Knowband\Mobileappbuilder\Block\Adminhtml\Layout\Grid\BannerSquare');
        $this->getResponse()->appendBody($block->toHtml());
    }
}
