<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsSubAccount
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Controller\Customer;

use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;
use Magento\Framework\App\Action\Context;

/**
 * Class Profilesave
 * @package Ced\CsSubAccount\Controller\Customer
 */
class Profilesave extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var \Ced\CsSubAccount\Model\CsSubAccountFactory
     */
    protected $csSubAccountFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * Profilesave constructor.
     * @param \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlFactory $urlFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper
     * @param \Ced\CsMarketplace\Helper\Acl $aclHelper
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendor
     */
    public function __construct(
        \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Session $customerSession,
        UrlFactory $urlFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        \Ced\CsMarketplace\Helper\Acl $aclHelper,
        \Ced\CsMarketplace\Model\VendorFactory $vendor
    )
    {

        parent::__construct(
            $context,
            $resultPageFactory,
            $customerSession,
            $urlFactory,
            $registry,
            $jsonFactory,
            $csmarketplaceHelper,
            $aclHelper,
            $vendor
        );

        $this->csSubAccountFactory = $csSubAccountFactory;
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
    }

    /**
     * Promo quote edit action
     *
     * @return                                  void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        if (!$this->_getSession()->getVendorId())
            return;
        if ($this->getRequest()->getPost()) {
            $subvendor = $this->_getSession()->getSubVendorData();
            $model = $this->csSubAccountFactory->create();
            if ($id = $subvendor['id']) {
                try {
                    $model->load($id);
                    $post = $this->getRequest()->getParams();
                    $data = array();
                    $data['first_name'] = $post['first_name'];
                    $data['last_name'] = $post['last_name'];
                    if (isset($post['image_delete'])) {
                        $data['profile_image'] = '';
                    }

                    /*
                    *upload image
                    */
                    if (!empty($_FILES['image']['name']) && strlen($_FILES['image']['name']) > 0) {
                        try {
                            $fieldName = $_FILES['image']['name'];
                            $mediaDirectory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                            $path = $mediaDirectory->getAbsolutePath('ced/cssubaccount/images/' . $id);
                            $uploader = $this->uploaderFactory->create(array('fileId' => "image"));
                            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                            $uploader->setAllowRenameFiles(true);
                            $uploader->setFilesDispersion(false);
                            $fileData = $uploader->validateFile();
                            $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
                            $fileName = $fieldName;
                            $flag = $uploader->save($path, $fileName);
                            $data['profile_image'] = 'ced/cssubaccount/images/' . $id . '/' . $_FILES['image']['name'];
                            if (strlen($data['profile_image']) == 0) {
                                $data['profile_image'] = 'noimage';
                            }
                        } catch (\Exception $e) {
                            $this->messageManager->addErrorMessage("Image Not Uploaded");
                            $this->_redirect("*/*/profile");
                            return;
                        }
                    }
                    $model->addData($data);
                    $model->save();
                    $this->messageManager->addSuccessMessage(__('The profile information has been saved.'));
                    $this->_redirect('*/*/profile');
                    return;
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    $this->_redirect('*/*/profile');
                    return;
                }
            }

        }
        $this->messageManager->addErrorMessage(__('Unable to find sub-vendor to save'));
        $this->_redirect('*/*/profile');

    }

}
