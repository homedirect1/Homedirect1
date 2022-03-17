<?php

namespace Knowband\Mobileappbuilder\Controller\Adminhtml\Mobileappbuilder;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class Index extends \Magento\Backend\App\Action
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
        
        if ($this->getRequest()->getParam('store')) {
            $scope_id = $this->sp_storeManager->getStore($this->getRequest()->getParam('store'))->getId();
            $scope = "stores";
        } elseif ($this->getRequest()->getParam('website')) {
            $scope_id = $this->sp_storeManager->getWebsite($this->getRequest()->getParam('website'))->getId();
            $scope = "websites";
        } elseif ($this->getRequest()->getParam('group')) {
            $scope_id = $this->sp_storeManager->getGroup($this->getRequest()->getParam('group'))->getWebsite()->getId();
            $scope = "groups";
        } else {
            $scope = "default";
            $scope_id = 0;
        }
        if ($this->sp_request->isPost()) {
            $post_data = $this->sp_request->getPostValue();
            unset($post_data["form_key"]);
            
            //saving the logo image
            $logoImage = $this->getRequest()->getFiles('vss_mobileappbuilder');
            $logoImage = $logoImage['general_settings']['logo_image'];
            $fileName = ($logoImage && array_key_exists('name', $logoImage)) ? $logoImage['name'] : null;
            if ($logoImage && $fileName) {
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                $scopeAndScopeId = '_'.$scope.'_'.$scope_id;
                $fileName = 'kb_mab_logo_image'.$scopeAndScopeId.'.'.$extension;
                $logoImage['name'] = $fileName;
                $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $path = $mediaDirectory->getAbsolutePath('Knowband_Mobileappbuilder');
                $mask = $path . '/'. $fileName;
                $matches = glob($mask);
                if (!empty($matches))
                    array_map('unlink', $matches);

                try {
                    
                    $uploader = $this->_objectManager->create(
                        '\Magento\MediaStorage\Model\File\Uploader',
                        ['fileId' => $logoImage]
                    );
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
                    $uploader->setFilesDispersion(false);
                    $imageAdapterFactory = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')
                        ->create();
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setAllowCreateFolders(true);
                    $result = $uploader->save(
                        $mediaDirectory
                            ->getAbsolutePath('Knowband_Mobileappbuilder')
                    );
                    $this->sp_resource->saveConfig("knowband/mobileappbuilder/logo", json_encode(['image' => $fileName]), $scope, $scope_id);
                } catch (\Exception $e) {
                    if ($e->getCode() == 0) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            }
            
            $value = json_encode($post_data['vss_mobileappbuilder']);
            $this->sp_resource->saveConfig("knowband/mobileappbuilder/settings", $value, $scope, $scope_id);

            $this->messageManager->addSuccess(__('Settings saved successfully.'));
            $types = array('config');
            foreach ($types as $type) {
                $this->sp_cacheTypeList->cleanType($type);
            }
            foreach ($this->sp_cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl('*/*/');
            return $resultRedirect;
        }

        return $resultPage;
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Knowband_Mobileappbuilder::main');
    }
}
