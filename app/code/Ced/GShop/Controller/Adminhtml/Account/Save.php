<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
  * @package  Ced_GShop
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GShop\Controller\Adminhtml\Account;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * Class Save
 * @package Ced\GShop\Controller\Adminhtml\Account
 */
class Save extends Action
{
    public $_mediaDirectory;
    public $_fileUploaderFactory;

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ced_GShop::GXpress';

    /**
     * @var \Ced\GShop\Helper\MultiAccount
     */
    protected $multiAccountHelper;

    /** @var \Ced\GShop\Model\AccountsFactory $accounts */
    protected $accounts;

    /**
     * Save constructor.
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param Context $context
     * @param \Ced\GShop\Model\AccountsFactory $accounts
     * @param \Ced\GShop\Helper\MultiAccount $multiAccountHelper
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        Context $context,
        \Ced\GShop\Model\AccountsFactory $accounts,
        \Ced\GShop\Helper\MultiAccount $multiAccountHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        parent::__construct($context);
        $this->accounts = $accounts;
        $this->multiAccountHelper = $multiAccountHelper;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $accountDetails = $this->getRequest()->getParams();
        $accountDetails['included_destination'] = isset($accountDetails['included_destination']) ?
            implode(',', $accountDetails['included_destination']) : '';
        try {
            if (isset($accountDetails['account_code']) || isset($accountDetails['id'])) {
                if (isset($accountDetails['id'])) {
                    $accounts = $this->accounts->create()->load($accountDetails['id']);
                } else {
                    $accounts = $this->accounts->create();
                }
                if ($this->getRequest()->getFiles('account_file')['name']) {
                    $target = $this->_mediaDirectory->getAbsolutePath('GXpress/');
                    $uploader = $this->_fileUploaderFactory->create(['fileId' => 'account_file']);

                    $uploader->setAllowedExtensions(['json']);
                    $uploader->setAllowRenameFiles(true);
                    $result = $uploader->save($target, $accountDetails['merchant_id'] . '.json');
                    $accounts->addData(["account_file" => $result['path'] . $result['file']]);
                }
                if (!isset($accountDetails['id'])) {
                    $merchantId = $accounts->getCollection()
                        ->addFieldToFilter('merchant_id', ['in' => $accountDetails['merchant_id']])
                        ->getData();
                    if ($merchantId) {
                        throw new \Exception('Merchant Id Already Exists');
                    }
                    $accountCode = $accounts->getCollection()
                        ->addFieldToFilter('account_code', ['in' => $accountDetails['account_code']])
                        ->getData();
                    if ($accountCode) {
                        throw new \Exception('Account Code Already Exists');
                    }
                }
                $accountDetails['account_type'] = 'ads';
                $accounts->addData($accountDetails)->save();
                $this->multiAccountHelper->createProfileAttribute($accounts->getId(), $accounts->getAccountCode());
                $this->messageManager->addSuccessMessage(__('Account Saved Successfully.'));
                $this->_redirect('*/*/index', ['id' => $accounts->getId()]);
            } else {
                $this->messageManager->addNoticeMessage(__('Please fill the Account Code'));
                $this->_redirect('*/*/new');
            }
        } catch (\Exception $e) {
            $this->_objectManager->create('Ced\GShop\Helper\Logger')->addError('In Save Account: ' . $e->getMessage(), ['path' => __METHOD__]);
            $this->messageManager->addErrorMessage(__('Unable to Save Account Details Please Try Again.' . $e->getMessage()));
            $this->_redirect('*/*/new');
        }
        return;
    }
}
