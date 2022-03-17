<?php
/**
 */
namespace Knowband\Mobileappbuilder\Controller\Index;

class AppSpinWin extends \Magento\Framework\App\Action\Action 
{
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context, 
        \Magento\Framework\View\Result\PageFactory $resultRawFactory
    ) {
        parent::__construct($context);
        $this->sp_resultRawFactory = $resultRawFactory;
    }

    
    /**
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        
        $resultPage = $this->sp_resultRawFactory->create();
        return $resultPage;
        
    }
}
