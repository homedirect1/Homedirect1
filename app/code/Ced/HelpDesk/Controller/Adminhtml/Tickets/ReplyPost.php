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

namespace Ced\HelpDesk\Controller\Adminhtml\Tickets;

/**
 * Class ReplyPost
 * @package Ced\HelpDesk\Controller\Adminhtml\Tickets
 */
class ReplyPost extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $_scopeConfig;

    /**
     * @var \Ced\HelpDesk\Helper\Data
     */
    protected $helpdeskHelper;

    /**
     * @var \Magento\Authorization\Model\RoleFactory
     */
    protected $roleFactory;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var \Ced\HelpDesk\Model\AgentFactory
     */
    protected $agentFactory;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $ticketcollectionFactory;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Department\CollectionFactory
     */
    protected $departmentcollectionFactory;

    /**
     * @var \Ced\HelpDesk\Model\MessageFactory
     */
    protected $messageFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Ced\HelpDesk\Model\TicketFactory
     */
    protected $ticketFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customercollectionFactory;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $state;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * ReplyPost constructor.
     * @param \Ced\HelpDesk\Helper\Data $helpdeskHelper
     * @param \Magento\Authorization\Model\RoleFactory $roleFactory
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Ced\HelpDesk\Model\AgentFactory $agentFactory
     * @param \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketcollectionFactory
     * @param \Ced\HelpDesk\Model\ResourceModel\Department\CollectionFactory $departmentcollectionFactory
     * @param \Ced\HelpDesk\Model\MessageFactory $messageFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Ced\HelpDesk\Model\TicketFactory $ticketFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customercollectionFactory
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Translate\Inline\StateInterface $state
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Ced\HelpDesk\Helper\Data $helpdeskHelper,
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Ced\HelpDesk\Model\AgentFactory $agentFactory,
        \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketcollectionFactory,
        \Ced\HelpDesk\Model\ResourceModel\Department\CollectionFactory $departmentcollectionFactory,
        \Ced\HelpDesk\Model\MessageFactory $messageFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Ced\HelpDesk\Model\TicketFactory $ticketFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customercollectionFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Translate\Inline\StateInterface $state,
        \Ced\HelpDesk\Model\EmailSender $emailSender,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->helpdeskHelper = $helpdeskHelper;
        $this->roleFactory = $roleFactory;
        $this->userFactory = $userFactory;
        $this->authSession = $authSession;
        $this->agentFactory = $agentFactory;
        $this->ticketcollectionFactory = $ticketcollectionFactory;
        $this->departmentcollectionFactory = $departmentcollectionFactory;
        $this->messageFactory = $messageFactory;
        $this->timezone = $timezone;
        $this->ticketFactory = $ticketFactory;
        $this->customercollectionFactory = $customercollectionFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->state = $state;
        $this->emailSender = $emailSender;
        parent::__construct($context);
    }

    /*
     * Save Reply Post Data
     */
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $admin = [];
        $helper = $this->helpdeskHelper;
        $data = $this->getRequest()->getPostValue();
        $admin = $this->roleFactory->create()->load('Administrators', 'role_name')->getRoleUsers();
        $userModel = $this->userFactory->create();
        $userId = $this->authSession->getUser()->getData('user_id');
        $agentModel = $this->agentFactory->create();
        $roleCollection = $agentModel->getCollection()->addFieldToFilter('user_id', $userId)->getFirstItem();
        $role = $roleCollection->getRoleName();
        $agentId = $roleCollection->getId();
        if (isset($data['ticket_id']) && !empty($data['ticket_id'])) {
            $departmentCollection = $this->ticketcollectionFactory->create()
                ->addFieldToFilter('ticket_id', $data['ticket_id'])
                ->getFirstItem();
            $departmentCode = $departmentCollection->getDepartment();
            $numOfMsg = $departmentCollection->getNumMsg();
            $departmentCollection->setNumMsg($numOfMsg + 1)->save();
            $deptCollection = $this->departmentcollectionFactory->create()
                ->addFieldToFilter('code', $departmentCode)
                ->getFirstItem();
            $departmentHeadId = $deptCollection->getDepartmentHead();

        }
        $back = $this->getRequest()->getParam('back');
        $messageModel = $this->messageFactory->create();
        $date = $this->timezone->date()->format('Y-m-d H:i:s');
        $ticketModel = $this->ticketFactory->create();
        if (isset($data['id']) && !empty($data['id'])) {
            $ticketModel->load($data['id'])->setStatus($data['status'])->save();
            $customer_email = $ticketModel->getCustomerEmail();
            $customer_name = $ticketModel->getCustomerName();
        }
        if (!empty($data['ticket_id'])) {
            $attach = [];
            try {
                $messageModel->setMessage($data['reply_description']);
                $messageModel->setFrom($data['from']);
                $messageModel->setTo($data['to']);
                $messageModel->setTicketId($data['ticket_id']);
                $messageModel->setCreated($date);
                $attach = $this->uploadFile($data['ticket_id'], $data['to']);
                if (isset($attach) && !empty($attach)) {
                    $messageModel->setAttachment($attach['filename']);
                }
                $messageModel->setType('reply');
                $messageModel->save();
                if (isset($data['signature']) && $data['signature']) {
                    $signature = $deptCollection->getDeptSignature();
                } else {
                    $signature = null;
                }
                $data['reply_description'] = strip_tags($data['reply_description']);
                if ($helper->getStoreConfig('helpdesk/general/support_email')) {
                    if ($helper->getStoreConfig('helpdesk/email/mail_customer')) {
                        $this->sendCustomerEmail($customer_email,
                            $customer_name,
                            $data['reply_description'],
                            $data['status'],
                            $attach,
                            $data['ticket_id'],
                            $signature);
                    }
                    if (isset($userId) && isset($role) && $role == 'Agent') {
                        if ($departmentHeadId == $agentId) {
                            if ($helper->getStoreConfig('helpdesk/email/mail_head') && $helper
                                    ->getStoreConfig('helpdesk/email/mail_agent')) {
                                foreach ($admin as $adminId) {
                                    $adminData = $userModel->load($adminId);
                                    $adminEmail = $adminData->getEmail();
                                    $adminName = $adminData->getUsername();
                                    $helper->sendDepartmentHeadEmail(
                                        $data['from'],
                                        $adminEmail,
                                        $adminName,
                                        $customer_name,
                                        $data['reply_description'],
                                        $data['status'],
                                        $attach,
                                        $data['ticket_id'],
                                        $signature
                                    );
                                }
                            }
                        } else {
                            if ($helper->getStoreConfig('helpdesk/email/mail_head')) {
                                $headData = $agentModel->load($departmentHeadId);
                                $headEmail = $headData->getEmail();
                                $headName = $headData->getUsername();
                                $helper->sendDepartmentHeadEmail(
                                    $data['from'],
                                    $headEmail,
                                    $headName,
                                    $customer_name,
                                    $data['reply_description'],
                                    $data['status'],
                                    $attach,
                                    $data['ticket_id'],
                                    $signature
                                );
                            }
                            if ($helper->getStoreConfig('helpdesk/email/mail_admin')) {
                                foreach ($admin as $adminId) {
                                    $adminData = $userModel->load($adminId);
                                    $adminEmail = $adminData->getEmail();
                                    $adminName = $adminData->getUsername();
                                    $helper->sendDepartmentHeadEmail(
                                        $data['from'],
                                        $adminEmail,
                                        $adminName,
                                        $customer_name,
                                        $data['reply_description'],
                                        $data['status'],
                                        $attach,
                                        $data['ticket_id'],
                                        $signature
                                    );
                                }
                            }
                        }
                    }
                } else {
                    $this->messageManager->addErrorMessage(__('Unable to send mail.'));
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
        if (!isset($back) && $back != 'edit' && isset($data['id'])) {
            $this->messageManager->addSuccessMessage(
                __('Replied Successfully...')
            );
            return $this->_redirect('*/*/ticketsinfo');
        } else {
            return $this->_redirect('*/*/manage/id/' . $data['id']);
        }
    }

    /*
     * Upload Files
     * */
    /**
     * @param $id
     * @param $email
     * @return array|null
     */
    public function uploadFile($id, $email)
    {
        $extension = [];
        try {
            $ext = $this->helpdeskHelper
                ->getStoreConfig('helpdesk/frontend/allow_extensions');
            $extension = explode(',', $ext);
            $date = $this->timezone->date()->format('Y-m-d H:i:s');
            $customer_Id = $this->customercollectionFactory->create()
                ->addFieldToFilter('email', $email)
                ->getFirstItem()
                ->getId();
            if (isset($customer_Id) && !empty($customer_Id)) {
                $customer_Id = $customer_Id;
            } else {
                $customer_Id = 'guest';
            }
            $fileUploaderFactory = $this->uploaderFactory;

            $filesystem = $this->filesystem;
            $uploader = $fileUploaderFactory->create(['fileId' => 'attachment']);
            $uploader->setAllowedExtensions($extension);
            $uploader->setAllowRenameFiles(false);
            $uploader->setFilesDispersion(false);
            $uploader->setAllowCreateFolders(true);
            $path = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $abs_path = $path->getAbsolutePath('images/helpdesk/' . $customer_Id . '/' . $id . '/' . $date . '/');
            $uploader->save($abs_path);
            $string = $uploader->getUploadedFileName();

            $filepath = $abs_path . $string;
            return ['filename' => $date . '/' . $string, 'filepath' => $filepath];
        } catch (\Exception $e) {
            return null;
        }
    }

    /*
     * Send Email to customer
     * */
    /**
     * @param $customer_email
     * @param $customer_name
     * @param $message
     * @param $status
     * @param $attach
     * @param $ticketId
     * @param $signature
     */
    public function sendCustomerEmail($customer_email, $customer_name, $message, $status, $attach, $ticketId, $signature)
    {
        if (!empty($customer_email) && !empty($customer_name)) {
            $senderName = "Support Sustem";
            $senderEmail = $this->helpdeskHelper
                ->getStoreConfig('helpdesk/general/support_email');
            $this->state->suspend();
            try {
                $sender = [
                    'name' => $senderName,
                    'email' => $senderEmail,
                ];
                $transport = $this->emailSender;
                $transport->setTemplateIdentifier('send_customer_email_reply_template')
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ]
                    );
                if ($status == 'Closed' || $status == 'Resolved') {
                    $transport->setTemplateVars(['customer_name' => $customer_name,
                        'message' => $message,
                        'status' => $status,
                        'ticketId' => $ticketId,
                        'signature' => $signature
                    ]);
                } else {
                    $transport->setTemplateVars(['customer_name' => $customer_name,
                        'message' => $message,
                        'ticketId' => $ticketId,
                        'signature' => $signature
                    ]);
                }
                $transport->setFrom($sender)
                    ->addTo($customer_email);
                if (isset($attach) && !empty($attach)) {
                    $fileName = [];
                    $fileName = explode('/', $attach['filename']);
                    $mimeType = $this->helpdeskHelper->getMimeType($attach['filepath']);
                    if ($mimeType == 'notFound') {
                        $this->messageManager->addErrorMessage(__('File not uploaded '));
                    } else {
                        $transport->addAttachment(file_get_contents($attach['filepath']), $mimeType, $fileName[1]);
                    }
                }
                $a = $transport->getTransport();
                $a->sendMessage();
                $this->state->resume();
                return;
            } catch (\Exception $e) {
                echo $e;
                return;
            }
        }
    }
}