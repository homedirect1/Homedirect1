<?php

namespace Knowband\Mobileappbuilder\Block\Adminhtml\Tab;

class FacebookSettings extends \Magento\Backend\Block\Widget implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        return __('Facebook Login Settings');
    }

    public function getTabTitle()
    {
        return __('Facebook Login Settings');
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
    
    public function getFrontEndUrl($action) {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $frontendUrlBuilder = $om->get(\Magento\Framework\Url::class);
        $url = $frontendUrlBuilder->getUrl(
            $action,
            [
                '_secure' => true,
            ]
        );
        return $url;
    }
    
}
