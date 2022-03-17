<?php

namespace Knowband\Mobileappbuilder\Controller\Adminhtml\Mobileappbuilder;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class SaveAndSendPushNotification extends \Magento\Framework\App\Action\Action {

    protected $sp_resultRawFactory;
    protected $sp_request;
    protected $sp_helper;
    protected $sp_scopeConfig;
    protected $inlineTranslation;
    protected $sp_transportBuilder;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, 
            \Magento\Framework\App\Request\Http $request, 
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, 
            \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, 
            \Magento\Framework\View\LayoutFactory $viewLayoutFactory, 
            \Knowband\Mobileappbuilder\Helper\Data $dataHelper, 
            \Knowband\Mobileappbuilder\Helper\Firebase $firebaseHelper,
            \Magento\Framework\Filesystem $fileSystem,
            \Knowband\Mobileappbuilder\Model\PushNotification $pn_model
    ) {
        parent::__construct($context);
        $this->sp_request = $request;
        $this->sp_scopeConfig = $scopeConfig;
        $this->inlineTranslation = $inlineTranslation;
        $this->_viewLayoutFactory = $viewLayoutFactory;
        $this->dataHelper = $dataHelper;
        $this->firebaseHelper = $firebaseHelper;
        $this->_filesystem = $fileSystem;
        $this->pn_model = $pn_model;
    }

    public function execute() {

        if ($this->getRequest()->isPost()) {
            $post_data = $this->getRequest()->getPost();
            if (isset($post_data['notification'])) {
                try {
                    $error = false;
                    $notification_data = $post_data['notification'];
                    $notification_title = $notification_data['title'];
                    $notification_message = $notification_data['message'];
                    $notification_image_type = $notification_data['image_type'];
                    $redirect_activity_type = $notification_data['redirect_activity'];
                    $redirect_category_id = $notification_data['category_id'];
                    $redirect_product_id = $notification_data['product_id'];
                    $redirect_product_name = $post_data['product_name'];
                    $redirect_category_name = $post_data['category_name'];
                    $deviceType = $notification_data['device_type'];
                    $firebase_server_key = $this->dataHelper->getFirebaseServerKey();
                    if (!$firebase_server_key) {
                        $error = true;
                        $msg = __('Firebase Server key is not available');
                    }
                    
                    $notification_image_url = '';


                    if ($notification_image_type == 'url') {
                        $notification_image_url = $notification_data['image_url'];
                    } elseif ($notification_image_type == 'image') {
                        
                        $logoImage = $this->getRequest()->getFiles('notification');
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
                                $notification_image_url = $this->dataHelper->getMediaUrl().'Knowband_Mobileappbuilder/'.$fileName;
                            } catch (\Exception $e) {
                                if ($e->getCode() == 0) {
                                    $error = true;
                                    $msg = __($e->getMessage());
                                }
                            }
                        }
                    }

                    if (!$error) {

                        $imageUrl = $notification_image_url; // image url
                        $user_id = ""; // user_id

                        $filters = '';
                        $product_id = null;
                        $category_id = null;

                        $cat_id = "";
                        $cat_name = "";
                        $prod_id = "";
                        $prod_name = "";

                        switch ($redirect_activity_type) {
                            case 'product':
                                $push_type = 'promotional_product';
                                $product_id = $redirect_product_id;
                                $prod_id = $redirect_product_id;
                                $prod_name = $redirect_product_name;
                                break;
                            case 'category':
                                $push_type = 'promotional_category';
                                $category_id = $redirect_category_id;
                                $cat_id = $redirect_category_id;
                                $cat_name = $redirect_category_name;
                                break;
                            default: 
                                $push_type = 'promotional_home';
                                break;
                        }

                        $firebase_data = array();
                        $firebase_data['data']['title'] = $notification_title;
                        $firebase_data['data']['is_background'] = false;
                        $firebase_data['data']['message'] = $notification_message;
                        $firebase_data['data']['image'] = $imageUrl;
                        $firebase_data['data']['payload'] = '';
                        $firebase_data['data']['user_id'] = $user_id;
                        $firebase_data['data']['push_type'] = $push_type;
                        $firebase_data['data']['category_id'] = $category_id;
                        $firebase_data['data']['product_id'] = $product_id;
                        $firebase_data['data']['filters'] = $filters;
                        $firebase_data['data']['category_name'] = 'Test';


                        if ($deviceType == 'android') {
                            $response = $this->firebaseHelper->sendToTopic("ANDROID_USERS", $firebase_data, $firebase_server_key, $deviceType);
                        } else if ($deviceType == 'ios') {
                            $response = $this->firebaseHelper->sendToTopic("IOS_USERS", $firebase_data, $firebase_server_key, $deviceType);
                        } else {
                            $response = $this->firebaseHelper->sendToTopic("ANDROID_USERS", $firebase_data, $firebase_server_key, 'android');
                            $response = $this->firebaseHelper->sendToTopic("IOS_USERS", $firebase_data, $firebase_server_key, 'ios');
                        }

                        $response = $this->firebaseHelper->sendToTopic("PROMO_OFFERS", $firebase_data, $firebase_server_key, $deviceType);


                        $data = array(
                            'title' => $notification_title,
                            'message' => $notification_message,
                            'image_type' => $notification_image_type,
                            'image_url' => $imageUrl,
                            'redirect_activity' => $redirect_activity_type,
                            'category_id' => $cat_id,
                            'category_name' => $cat_name,
                            'product_id' => $prod_id,
                            'product_name' => $prod_name,
                            'status' => 'sent',
                            'date_add' => $this->dataHelper->getDate(),
                            'device_type' => $deviceType
                        );
                        $notificationmodel = $this->pn_model->setData($data);
                        $notificationmodel->save();

                        $msg = __('Notificaton Send successfully');
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
            } else if(isset($post_data['method']) && $post_data['method'] == 'view') {
                $block = $this->_viewLayoutFactory->create()->createBlock('Knowband\Mobileappbuilder\Block\Adminhtml\ViewPushNotification');
                $this->getResponse()->appendBody($block->toHtml());
            } else {
                $block = $this->_viewLayoutFactory->create()->createBlock('Knowband\Mobileappbuilder\Block\Adminhtml\GetPushNotificationForm');
                $this->getResponse()->appendBody($block->toHtml());
            }
        }
    }

}
