<?php

namespace Knowband\Mobileappbuilder\Controller\Adminhtml\Mobileappbuilder;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class BannerAjax extends \Magento\Framework\App\Action\Action
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
        \Knowband\Mobileappbuilder\Model\Banner $bannerModel, 
        \Knowband\Mobileappbuilder\Helper\Data $helper, 
        \Magento\Framework\Filesystem $fileSystem,
        LayoutFactory $viewLayoutFactory
    ) {
        parent::__construct($context);
        $this->sp_resultRawFactory = $resultRawFactory;
        $this->bannerModel = $bannerModel;
        $this->helper = $helper;
        $this->_filesystem = $fileSystem;
        $this->_viewLayoutFactory = $viewLayoutFactory;
    }
    

    public function execute() {
        if ($this->getRequest()->isPost()) {
            $post_data = $this->getRequest()->getPost();
            if (isset($post_data['method']) && $post_data['method'] == 'edit') {
                $block = $this->_viewLayoutFactory->create()->createBlock('Knowband\Mobileappbuilder\Block\Adminhtml\BannerSlider');
                $this->getResponse()->appendBody($block->toHtml());
            }elseif (isset($post_data['banner_slider'])) {
                try {
                    $error = false;
                    $data = $post_data['banner_slider'];
                    $status = $data['status'];
                    $image_type = $data['image_type'];
                    $redirect_activity_type = $data['redirect_activity'];
                    $redirect_category_id = $data['category_id'];
                    $redirect_product_id = $data['product_id'];
                    $kb_banner_id = $data['banner_slider_id'];
                    $type = $data['type'];
                    $image_url = $data['image_url'];

                    if ($image_type == 'url') {
                        $image_url = $post_data['image_url'];
                    } elseif ($image_type == 'image') {
                        
                        $logoImage = $this->getRequest()->getFiles('banner_slider');
                        $logoImage = $logoImage['image_upload'];
                        $fileName = ($logoImage && array_key_exists('name', $logoImage)) ? $logoImage['name'] : null;
                        if ($logoImage && $fileName) {
                            
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
                                $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
                                $result = $uploader->save(
                                    $mediaDirectory
                                        ->getAbsolutePath('Knowband_Mobileappbuilder')
                                );
                                $image_url = $this->helper->getMediaUrl().'Knowband_Mobileappbuilder/'.$fileName;
                            } catch (\Exception $e) {
                                if ($e->getCode() == 0) {
                                    $error = true;
                                    $msg = __($e->getMessage());
                                }
                            }
                        }
                    }

                    if (!$error) {

                        $imageUrl = $image_url; // image url


                        $cat_id = "";
                        $cat_name = "";
                        $prod_id = "";
                        $prod_name = "";
                        if ($redirect_activity_type == 'category') {
                            $cat_id = $redirect_category_id;
                            $cat_name = $post_data['category_name'];
                        }
                        if ($redirect_activity_type == 'product') {
                            $prod_id = $redirect_product_id;
                            $prod_name = $post_data['product_name'];
                        }

                        $data = array(
                            'kb_banner_id' => $kb_banner_id,
                            'status' => $status,
                            'image_type' => $image_type,
                            'image_url' => $imageUrl,
                            'redirect_activity' => $redirect_activity_type,
                            'category_id' => $cat_id,
                            'category_name' => $cat_name,
                            'product_id' => $prod_id,
                            'product_name' => $prod_name,
                            'date_upd' => $this->helper->getDate(),
                        );
                        $slider_baner_model = $this->bannerModel->setData($data);
                        $slider_baner_model->save();

                        $msg = __('Settings saved successfully');
                    } else {
                        
                    }
                    $jsondata = array('error' => $error, 'msg' => $msg);
                    $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                    $resultJson->setData($jsondata);
                    return $resultJson;
                } catch (\Exception $ex) {
                    $jsondata = array('error' => $error, 'msg' => $ex->getMessage());
                    $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                    $resultJson->setData($jsondata);
                    return $resultJson;
                }
            }else{
                $block = $this->_viewLayoutFactory->create()->createBlock('Knowband\Mobileappbuilder\Block\Adminhtml\Tab\BannerSettings');
                $this->getResponse()->appendBody($block->toHtml());
            }
        }
    }
}
