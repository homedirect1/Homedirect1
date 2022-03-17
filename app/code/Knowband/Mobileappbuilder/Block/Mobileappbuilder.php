<?php
namespace Knowband\Mobileappbuilder\Block;
 
class Mobileappbuilder extends \Magento\Framework\View\Element\Template
{
    public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \Knowband\Mobileappbuilder\Helper\Data $helper
            )
    {
        $this->sp_helper = $helper;
        parent::__construct($context);
    }
    
    public function getSettings($key = 'knowband/mobileappbuilder/settings')
    {
        return $this->sp_helper->getSavedSettings($key);
    }
}
