<?php

namespace Knowband\Mobileappbuilder\Controller\Adminhtml\Mobileappbuilder;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class EditLayout extends \Magento\Backend\App\Action
{
    public $resultPageFactory = false;
    public $sp_request;
    public $sp_resource;
    public $sp_storeManager;
    public $sp_cacheFrontendPool;
    public $sp_cacheTypeList;
    protected $sp_helper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        \Knowband\Mobileappbuilder\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->sp_request = $request;
        $this->resultPageFactory = $resultPageFactory;
        $this->sp_resource = $resource;
        $this->sp_storeManager = $storeManager;
        $this->sp_cacheFrontendPool = $cacheFrontendPool;
        $this->sp_cacheTypeList = $cacheTypeList;
        $this->_filesystem = $fileSystem;
        $this->directory_list = $directory_list;  
        $this->sp_helper = $helper;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Knowband_Mobileappbuilder::main');
        $resultPage->getConfig()->getTitle()->prepend(__('Mobile App Builder'));
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Layout'));
        if ($this->sp_request->isPost()) {
            
        }

        return $resultPage;
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Knowband_Mobileappbuilder::main');
    }
}
