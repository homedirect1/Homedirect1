<?php

namespace Knowband\Mobileappbuilder\Block\Adminhtml\Tab;

class MobileMenuSettings extends \Magento\Backend\Block\Widget implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    private $sp_helper;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Knowband\Mobileappbuilder\Helper\Data $helper,
        array $data = []
    ) {
        $this->sp_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getTabLabel()
    {
        return __('Menu Settings');
    }

    public function getTabTitle()
    {
        return __('Menu Settings');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getSettings()
    {
        return $this->sp_helper->getSavedSettings();
    }
    
    public function getCmsPages()
    {
        return $this->sp_helper->getCMSPages();
    }
}
