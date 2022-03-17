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

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;

/**
 * Class CreatePost
 * @package Ced\CsSubAccount\Controller\Customer
 */
class CreatePost extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var \Ced\CsMarketplace\Model\VendorFactory
     */
    protected $vendorFactory;

    /**
     * @var \Ced\CsSubAccount\Model\CsSubAccountFactory
     */
    protected $csSubAccountFactory;

    /**
     * @var \Ced\CsSubAccount\Model\ResourceModel\AccountStatus\CollectionFactory
     */
    protected $accountstatusCollectionFactory;

    /**
     * @var \Magento\Framework\Encryption\Encryptor
     */
    protected $encryptor;

    /**
     * @var \Ced\CsSubAccount\Model\AccountStatusFactory
     */
    protected $accountStatusFactory;

    /**
     * CreatePost constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory
     * @param \Ced\CsSubAccount\Model\ResourceModel\AccountStatus\CollectionFactory $accountstatusCollectionFactory
     * @param \Magento\Framework\Encryption\Encryptor $encryptor
     * @param \Ced\CsSubAccount\Model\AccountStatusFactory $accountStatusFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory,
        \Ced\CsSubAccount\Model\ResourceModel\AccountStatus\CollectionFactory $accountstatusCollectionFactory,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Ced\CsSubAccount\Model\AccountStatusFactory $accountStatusFactory
    )
    {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->vendorFactory = $vendorFactory;
        $this->csSubAccountFactory = $csSubAccountFactory;
        $this->accountstatusCollectionFactory = $accountstatusCollectionFactory;
        $this->encryptor = $encryptor;
        $this->accountStatusFactory = $accountStatusFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $parentVendor = $this->_customerSession->getParentVendor();
        $requestId = $this->_customerSession->getRequestId();
        $this->_customerSession->unsParentVendor();
        $this->_customerSession->unsRequestId();
        try {
            if (!$parentVendor || !$requestId) {
                $this->messageManager->addErrorMessage(__('Kindly click on the accept link from mail sent by seller.'));
                $this->_redirect('cssubaccount/customer/create/');
                return;
            }
            if (!$this->getRequest()->isPost()) {
                $this->_redirect('cssubaccount/customer/create/');
                return;
            }
            $post = $this->getRequest()->getPost();
            $password = $post['password'];
            $email = $post['email'];
            $vendor_coll = $this->vendorFactory->create()->loadByEmail($email);
            $subvendor_coll = $this->csSubAccountFactory->create()->load($email, 'email');
            $request_coll = $this->accountstatusCollectionFactory->create()->addFieldToFilter('email', $email);
            if (!count($request_coll->getData())) {
                $this->messageManager->addErrorMessage(__('Seller has not sent any request to ' . $email . ' mail Id.'));
                $this->_redirect('cssubaccount/customer/create/');
                return;
            }
            if (($vendor_coll) || !empty($subvendor_coll->getData())) {
                $this->messageManager->addErrorMessage(__($email . ' Mail id already exist.'));
                $this->_redirect('csmarketplace/account/login/');
                return;
            }
            $data = array();
            $data['parent_vendor'] = $parentVendor;
            $data['first_name'] = $post['firstname'];
            $data['last_name'] = $post['lastname'];
            $data['middle_name'] = $post['middlename'];
            $data['email'] = $post['email'];
            $data['role'] = 'all';
            $data['password'] = $this->encryptor->encrypt($post['password']);
            $data['status'] = 0;
            $model = $this->csSubAccountFactory->create()->setData($data);
            $model->save();
            $this->_customerSession->setParentVendor($parentVendor);
            $this->_customerSession->setSubvendorEmail($post['email']);

            $requestModel = $this->accountStatusFactory->create()->load($requestId);
            $requestModel->setStatus(\Ced\CsSubAccount\Model\AccountStatus::ACCOUNT_STATUS_ACCEPTED);
            $requestModel->save();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $this->_redirect('cssubaccount/customer/create/');
            return;
        }
        $this->messageManager->addSuccessMessage(__('You have successfully registered for sub vendor.'));
        $this->_redirect('cssubaccount/vendor/approval');
        return;
    }
}
 
