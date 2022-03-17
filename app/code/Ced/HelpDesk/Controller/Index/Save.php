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
 * @package     Ced_HelpDesk
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\HelpDesk\Controller\Index;

use Magento\Framework\App\Action\Context;

/**
 * Class Save
 * @package Ced\HelpDesk\Controller\Index
 */
class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Ced\HelpDesk\Helper\Data
     */
    protected $helpdeskHelper;

    protected $storeManager;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $ticketcollectionFactory;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Ced\HelpDesk\Model\MessageFactory
     */
    protected $messageFactory;

    /**
     * @var \Ced\HelpDesk\Model\TicketFactory
     */
    protected $ticketFactory;

    /**
     * @var \Ced\HelpDesk\Model\DepartmentFactory
     */
    protected $departmentFactory;

    /**
     * @var \Ced\HelpDesk\Model\AgentFactory
     */
    protected $agentFactory;

    /**
     * Save constructor.
     * @param \Ced\HelpDesk\Helper\Data $helpdeskHelper
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketcollectionFactory
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Ced\HelpDesk\Model\MessageFactory $messageFactory
     * @param \Ced\HelpDesk\Model\TicketFactory $ticketFactory
     * @param \Ced\HelpDesk\Model\DepartmentFactory $departmentFactory
     * @param \Ced\HelpDesk\Model\AgentFactory $agentFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Context $context
     */
    public function __construct(
        \Ced\HelpDesk\Helper\Data $helpdeskHelper,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketcollectionFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Ced\HelpDesk\Model\MessageFactory $messageFactory,
        \Ced\HelpDesk\Model\TicketFactory $ticketFactory,
        \Ced\HelpDesk\Model\DepartmentFactory $departmentFactory,
        \Ced\HelpDesk\Model\AgentFactory $agentFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Context $context
    )
    {
        $this->_storeManager = $storeManager;
        $this->helpdeskHelper = $helpdeskHelper;
        $this->userFactory = $userFactory;
        $this->timezone = $timezone;
        $this->ticketcollectionFactory = $ticketcollectionFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->messageFactory = $messageFactory;
        $this->ticketFactory = $ticketFactory;
        $this->departmentFactory = $departmentFactory;
        $this->agentFactory = $agentFactory;
        parent::__construct($context);
    }

    /**
     * Save Message Data
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $helper = $this->helpdeskHelper;
        if (!$helper->getStoreConfig('helpdesk/general/enable')) {
            $this->_redirect('cms/index/index');
            return;
        }

        if (!empty($this->getRequest()->getPostValue())) {
            $dept = $this->getRequest()->getParam('dept');
            $image_limit = $this->getRequest()->getParam('image_count');
            $upload_image = $this->getRequest()->getParam('upload_image');
            $unupload_image = $this->getRequest()->getParam('unupload_image');
            $finalUploadedImage = array_diff(explode(',', $upload_image), explode(',', $unupload_image));
            $subject = $this->getRequest()->getParam('subject');
            $message = $this->getRequest()->getParam('message');

            $attachment = $this->getRequest()->getParam('attachment');
            $priority = $this->getRequest()->getParam('priority');
            $order = $this->getRequest()->getParam('order');
            $priorityValue = $helper->getStoreConfig('helpdesk/frontend/allow_priority');
            $deptValue = $helper->getStoreConfig('helpdesk/frontend/select_dept');
            if (!empty($subject) && !empty($message)) {
                if ($priorityValue) {
                    if (!empty($priority)) {
                        $priority_value = $priority;
                    }
                } else {
                    $priority_value = 'Normal';
                }
                if ($deptValue) {
                    if (!empty($dept)) {
                        $dept_value = $dept;
                    }
                } else {
                    $defaultDept = $helper->getStoreConfig('helpdesk/frontend/default_dept');
                    $dept_value = 'admin';
                }

                $getadmin = $this->userFactory->create()->load(1);
                $staffName = $getadmin->getUserName();

                $adminname = $getadmin->getUserId();
                $staff = $adminname;


                $customer_name = $this->getRequest()->getParam('name');

                $customer_Id = 'guest';

                $customer_email = $this->getRequest()->getParam('email');

                $date = $date = $this->timezone->date()->format('Y-m-d H:i:s');
                $store_id = $this->_storeManager->getStore()->getId();
                $ticket = $this->ticketcollectionFactory->create();
                $tic = $ticket->count();

                if ($tic > 0) {
                    $ticketModel = $this->ticketcollectionFactory->create()->getLastItem()->getData();
                    $id = $ticketModel['ticket_id'] + 1;
                } else {
                    $id = 10000001;
                }

                $ext = $helper->getStoreConfig('helpdesk/frontend/allow_extensions');
                $extension = explode(',', $ext);
                $string = array();

                foreach ($finalUploadedImage as $value) {
                    if (!$this->getRequest()->getFiles()->toArray()) {
                        try {
                            $fileIndex = "file" . $value;
                            $fileUploaderFactory = $this->uploaderFactory;
                            $filesystem = $this->filesystem;
                            $uploader = $fileUploaderFactory->create(['fileId' => $fileIndex]);
                            $uploader->setAllowedExtensions($extension);
                            $uploader->setAllowRenameFiles(false);
                            $uploader->setFilesDispersion(false);
                            $uploader->setAllowCreateFolders(true);
                            $path = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                            $abs_path = $path->getAbsolutePath('images/helpdesk/' . $customer_Id . '/' . $id . '/' . $date . '/');
                            $uploader->save($abs_path);
                            $uploadedFileName = $uploader->getUploadedFileName();
                            $string[] = $date . '/' . $uploadedFileName;
                            $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

                        } catch (\Exception $e) {
                            $this->messageManager->addErrorMessage($e->getMessage());
                            $this->_redirect('helpdesk/tickets/index');
                            return;
                        }
                    }
                }
                $string = implode(',', $string);

                if (!isset($string)) {
                    $string = '';
                }
                $num_msg = 1;
                try {

                    $ticketMessage = $this->messageFactory->create();
                    $ticketMessage->setData('ticket_id', $id)
                        ->setData('message', $message)
                        ->setData('type', 'reply')
                        ->setData('attachment', $string)
                        ->setData('created', $date)
                        ->setData('from', $customer_name);
                    $ticketMessage->save();


                    $ticketModel = $this->ticketFactory->create();
                    $ticketModel->setData('message', $message)
                        ->setData('ticket_id', $id)
                        ->setData('department', $dept_value)
                        ->setData('subject', $subject)
                        ->setData('status', 'New')
                        ->setData('num_msg', $num_msg)
                        ->setData('order', 'N/A')
                        ->setData('customer_name', $customer_name)
                        ->setData('customer_email', $customer_email)
                        ->setData('customer_id', $customer_Id)
                        ->setData('agent', $staff)
                        ->setData('agent_name', $staffName)
                        ->setData('lock', 0)
                        ->setData('store_view', $store_id)
                        ->setData('created_time', $date)
                        ->setData('priority', $priority_value);
                    $ticketModel->save();
                } catch (\Exception $e) {
                    echo($e->getMessage());
                }
                if ($helper->getStoreConfig('helpdesk/general/support_email')) {
                    if ($helper->getStoreConfig('helpdesk/email/mail_customer')) {
                        $mailCustomer = $helper->mailguestCustomer($id, $customer_name, $customer_email);
                    }
                    if ($helper->getStoreConfig('helpdesk/email/mail_head')) {
                        $headId = $this->departmentFactory->create()
                            ->load($dept_value, 'code')
                            ->getDepartmentHead();
                        $agentModel = $this->agentFactory->create()
                            ->load($headId);
                        $head_email = $agentModel->getEmail();
                        $head_name = $agentModel->getUsername();
                        $mailDepartmentHead = $helper->mailHeadTicketCreate($head_name, $head_email, $id, $customer_name);
                    }
                } else {
                    $this->messageManager->addErrorMessage(__('Unable to send mail.'));
                }
                $message = __('You have Created a Ticket. Your Ticket id is ' . $id);
                $this->messageManager->addSuccessMessage($message);
                return $this->_redirect('helpdesk/index/index');
            } else {
                $message = __('Please Fill All The Fields');
                $this->messageManager->addErrorMessage($message);
                $this->_redirect('helpdesk/index/ticket');
                return;
            }
        }
    }

}