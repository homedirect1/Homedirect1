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
 * Class Message
 * @package Ced\HelpDesk\Controller\Index
 */
class Message extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Ced\HelpDesk\Helper\Data
     */
    protected $helpdeskHelper;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $ticketcollectionFactory;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Department\CollectionFactory
     */
    protected $departmentcollectionFactory;

    /**
     * @var \Ced\HelpDesk\Model\AgentFactory
     */
    protected $agentFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Ced\HelpDesk\Model\MessageFactory
     */
    protected $messageFactory;

    /**
     * Message constructor.
     * @param \Ced\HelpDesk\Helper\Data $helpdeskHelper
     * @param \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketcollectionFactory
     * @param \Ced\HelpDesk\Model\ResourceModel\Department\CollectionFactory $departmentcollectionFactory
     * @param \Ced\HelpDesk\Model\AgentFactory $agentFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ced\HelpDesk\Model\MessageFactory $messageFactory
     * @param Context $context
     */
    public function __construct(
        \Ced\HelpDesk\Helper\Data $helpdeskHelper,
        \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketcollectionFactory,
        \Ced\HelpDesk\Model\ResourceModel\Department\CollectionFactory $departmentcollectionFactory,
        \Ced\HelpDesk\Model\AgentFactory $agentFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ced\HelpDesk\Model\MessageFactory $messageFactory,
        Context $context
    )
    {
        $this->helpdeskHelper = $helpdeskHelper;
        $this->ticketcollectionFactory = $ticketcollectionFactory;
        $this->departmentcollectionFactory = $departmentcollectionFactory;
        $this->agentFactory = $agentFactory;
        $this->timezone = $timezone;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->messageFactory = $messageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $attach = [];
        $helper = $this->helpdeskHelper;
        if (!$helper->getStoreConfig('helpdesk/general/enable')) {
            $this->_redirect('cms/index/index');
            return;
        }
        $ticket_id = $this->getRequest()->getParam('id');
        $ticketModel = $this->ticketcollectionFactory->create()
            ->addFieldToFilter('ticket_id', $ticket_id)
            ->getFirstItem();
        $customer_name = $ticketModel->getCustomerName();
        $customer_Id = $ticketModel->getCustomerId();
        $customer_email = $ticketModel->getCustomerEmail();
        $departmentCode = $ticketModel->getDepartment();
        $agentId = $ticketModel->getAgent();
        $departmentModel = $this->departmentcollectionFactory->create()
            ->addFieldToFilter('code', $departmentCode)
            ->getFirstItem();
        $departmentHeadId = $departmentModel->getDepartmentHead();
        $agentModel = $this->agentFactory->create();
        $agentRole = $agentModel->load($agentId)->getRoleName();

        $image_limit = $this->getRequest()->getParam('image_count');
        $upload_image = $this->getRequest()->getParam('upload_image');
        $unupload_image = $this->getRequest()->getParam('unupload_image');
        $finalUploadedImage = array_diff(explode(',', $upload_image), explode(',', $unupload_image));
        $message = $this->getRequest()->getParam('message');
        $date = $this->timezone->date()->format('Y-m-d H:i:s');
        if ($this->getRequest()->isPost()) {
            if (!empty($message) && !empty($ticket_id)) {

                $ext = $helper->getStoreConfig('helpdesk/frontend/allow_extensions');
                $extension = explode(',', $ext);
                $string = [];

                foreach ($finalUploadedImage as $value) {
                    if (!empty($_FILES)) {
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
                            $abs_path = $path->getAbsolutePath('images/helpdesk/' . $customer_Id . '/' . $ticket_id . '/' . $date . '/');
                            $uploader->save($abs_path);
                            $uploadedFileName = $uploader->getUploadedFileName();
                            $string[] = $date . '/' . $uploadedFileName;
                            $attach[] = ['filename' => $uploadedFileName,
                                'filepath' => $abs_path];
                            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                        } catch (\Exception $e) {
                            $this->messageManager->addErrorMessage($e->getMessage());
                            $this->_redirect('helpdesk/index/viewticket', array('id' => $ticket_id, 'email' => $customer_email));
                            return;
                        }
                    }
                }


                if (!($string)) {
                    $string = '';
                } else {
                    if (!empty($string)) {
                        $string = implode(',', $string);
                    }

                }
                $ticketMessage = $this->messageFactory->create();
                $ticketMessage->setData('ticket_id', $ticket_id)
                    ->setData('message', $message)
                    ->setData('type', 'reply')
                    ->setData('attachment', $string)
                    ->setData('created', $date)
                    ->setData('from', $customer_name)
                    ->save();
                $ticketModel = $this->ticketcollectionFactory->create()
                    ->addFieldToFilter('ticket_id', $ticket_id)->getData();
                if (isset($ticketModel) && is_array($ticketModel)) {
                    foreach ($ticketModel as $value) {
                        $count = $value['num_msg'] + 1;
                        $ticketModel = $this->ticketcollectionFactory->create()
                            ->addFieldToFilter('ticket_id', $ticket_id)->getFirstItem();
                        $ticketModel->setData('num_msg', $count)
                            ->setData('status', 'Open')->save();
                    }
                    $agent_id = $value['agent'];
                    if (!isset($attach)) {
                        $attach = '';
                    }
                    $mailMessage = strip_tags($message);
                    if ($helper->getStoreConfig('helpdesk/general/support_email')) {
                        if ($helper->getStoreConfig('helpdesk/email/mail_customer')) {
                            $mailtoSupport = $helper->mailSupportFromCustomer($ticket_id, $customer_name, $customer_email, $attach, $mailMessage);
                        }
                        if (isset($agentId) && isset($departmentHeadId) && !empty($agentId) && !empty($departmentHeadId)) {
                            if ($agentId == $departmentHeadId) {
                                if ($helper->getStoreConfig('helpdesk/email/mail_head') && $helper->getStoreConfig('helpdesk/email/mail_agent')) {
                                    $agentLoad = $agentModel->load($agentId);
                                    $agentName = $agentLoad->getUsername();
                                    $agentEmail = $agentLoad->getEmail();
                                    $mailtoHead = $helper->mailAgentFromCustomer($ticket_id, $agentName, $agentEmail, $attach, $mailMessage, $customer_name);
                                }
                            } elseif ($agentRole == 'Administrators') {
                                if ($helper->getStoreConfig('helpdesk/email/mail_admin')) {
                                    $adminLoad = $agentModel->load($agentId);
                                    $adminName = $adminLoad->getUsername();
                                    $adminEmail = $adminLoad->getEmail();
                                    $mailtoAdmin = $helper->mailAgentFromCustomer($ticket_id, $adminName, $adminEmail, $attach, $mailMessage, $customer_name);
                                }

                            } else {
                                $agentLoad = $agentModel->load($agentId);
                                $agentName = $agentLoad->getUsername();
                                $agentEmail = $agentLoad->getEmail();
                                $departmentHeadLoad = $agentModel->load($departmentHeadId);
                                $headName = $departmentHeadLoad->getUsername();
                                $headEmail = $departmentHeadLoad->getEmail();
                                if ($helper->getStoreConfig('helpdesk/email/mail_agent')) {
                                    $mailtoAgent = $helper->mailAgentFromCustomer($ticket_id, $agentName, $agentEmail, $attach, $mailMessage, $customer_name);
                                }
                                if ($helper->getStoreConfig('helpdesk/email/mail_head')) {
                                    $mailtoHead = $helper->mailAgentFromCustomer($ticket_id, $headName, $headEmail, $attach, $mailMessage, $customer_name);
                                }
                            }
                        }
                    } else {
                        $this->messageManager->addErrorMessage(__('Unable to send mail.'));
                    }

                }
                if (!empty($message)) {
                    $ticketModel->setData('message', $message)->save();
                }

                $this->_redirect('helpdesk/index/viewticket', array('id' => $ticket_id, 'email' => $customer_email));
                return;
            } else {
                $message = __('Please Fill The Message');
                $this->messageManager->addErrorMessage($message);
                $this->_redirect('helpdesk/index/viewticket', array('id' => $ticket_id, 'email' => $customer_email));
                return;
            }
        } else {
            $message = __('Please Fill The form');
            $this->messageManager->addErrorMessage($message);
            $this->_redirect('helpdesk/index/viewticket', array('id' => $ticket_id, 'email' => $customer_email));
            return;
        }
    }
}