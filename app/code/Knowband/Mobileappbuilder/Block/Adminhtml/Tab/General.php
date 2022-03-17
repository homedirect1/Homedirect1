<?php

namespace Knowband\Mobileappbuilder\Block\Adminhtml\Tab;

class General extends \Magento\Backend\Block\Widget implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        return __('General Settings');
    }

    public function getTabTitle()
    {
        return __('General Settings');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getSettings($key = 'knowband/mobileappbuilder/settings')
    {
        return $this->sp_helper->getSavedSettings($key);
    }
    
    public function getProductCarouselTypes() {
        return array(
            'special' => __('Special Products'),
            'new_arrival' => __('New Arrival'),
            'best_seller' => __('Best Seller'),
        );
    }
    
    public function getProductCount() {
        return array(
            '5' => 5,
            '10' => 10,
            '15' => 15,
            '20' => 20,
        );
    }
    
    /**
     * Function to get layouts for home page
     * @return array
     */
    public function getHomePageLayout() {
        return $this->sp_helper->getHomePageLayout();
    }
    
    /**
     * Function to get media base url
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->sp_helper->getMediaUrl();
    }
    
    /**
     * Function to get list of shipping methods
     * @return array
     */
    public function getShippingMethods()
    {
        return $this->sp_helper->getShippingMethods();
    }
    
    public function isSpinWinEnabled()
    {
        return $this->sp_helper->isSpinWinEnabled();
    }
}
