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

namespace Ced\HelpDesk\Cron;

/**
 * Class EmailGateway
 * @package Ced\HelpDesk\Cron
 */
class EmailGateway
{
    /**
     * @var \Ced\HelpDesk\Helper\Data
     */
    protected $helpdeskHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Ced\HelpDesk\Model\TicketFactory
     */
    protected $ticketFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     * @var \Ced\HelpDesk\Model\MessageFactory
     */
    protected $messageFactory;

    /**
     * EmailGateway constructor.
     * @param \Ced\HelpDesk\Helper\Data $helpdeskHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Ced\HelpDesk\Model\TicketFactory $ticketFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Ced\HelpDesk\Model\MessageFactory $messageFactory
     */
    public function __construct(
        \Ced\HelpDesk\Helper\Data $helpdeskHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Ced\HelpDesk\Model\TicketFactory $ticketFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $file,
        \Ced\HelpDesk\Model\MessageFactory $messageFactory
    )
    {
        $this->helpdeskHelper = $helpdeskHelper;
        $this->dateTime = $dateTime;
        $this->userFactory = $userFactory;
        $this->customerFactory = $customerFactory;
        $this->ticketFactory = $ticketFactory;
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->messageFactory = $messageFactory;
    }

    /*
     * Create Ticket By Fetch email from email gateway
     */
    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function createTicket()
    {
        if (!($this->helpdeskHelper->getStoreConfig('helpdesk/gateway/enable_gateway')))
            return;
        $protocol = $this->helpdeskHelper->getStoreConfig('helpdesk/gateway/protocol');
        $gateway = $this->helpdeskHelper->getStoreConfig('helpdesk/gateway/email_gateway');
        $port = $this->helpdeskHelper->getStoreConfig('helpdesk/gateway/port');
        $loginId = $this->helpdeskHelper->getStoreConfig('helpdesk/gateway/login_id');
        $password = $this->helpdeskHelper->getStoreConfig('helpdesk/gateway/password');
        $date = $this->dateTime->date();
        if ($protocol == 'imap') {
            $imapPath = '{imap.' . $gateway . '.com:' . $port . '/' . $protocol . '/ssl/novalidate-cert/norsh}INBOX';
        } elseif ($protocol) {
            $imapPath = '{pop.' . $gateway . '.com:' . $port . '/' . $protocol . '/ssl/novalidate-cert/norsh}INBOX';
        }
        $inbox = imap_open($imapPath, $loginId, $password) or die('Cannot connect to ' . $gateway . ': ' . imap_last_error());
        $emails = imap_search($inbox, 'UNSEEN');
        $adminModel = $this->userFactory->create()->load(1);
        $customerModel = $this->customerFactory->create();
        $adminId = $adminModel->getUserId();
        $adminName = $adminModel->getUsername();
        if (!empty($emails)) {
            foreach ($emails as $mail) {
                $headerInfo = json_decode(json_encode(imap_headerinfo($inbox, $mail)), true);
                $message = quoted_printable_decode(imap_fetchbody($inbox, $mail, 1));
                $customer_name = $headerInfo['from'][0]['personal'];
                $mailbox = $headerInfo['from'][0]['mailbox'];
                $host = $headerInfo['from'][0]['host'];
                $customer_email = $mailbox . '@' . $host;
                $subject = $headerInfo['subject'];
                $senderMailBox = $headerInfo['sender'][0]['mailbox'];
                $customer_id = $customerModel->getCollection()->addFieldToFilter('email', $customer_email)->getFirstItem()->getId();
                if (!empty($customer_id)) {
                    $customerId = $customer_id;
                } else {
                    $customerId = 'guest';
                }
                $ticketModel = $this->ticketFactory->create();
                $ticketCount = $ticketModel->getCollection()->count();
                if ($ticketCount > 0) {
                    $ticketId = $ticketModel->getCollection()->getLastItem()->getTicketId();
                    $ticketId = $ticketId + 1;
                } else {
                    $ticketId = 100000001;
                }
                $replyData = explode(':', $subject);
                if (!empty($replyData)) {
                    foreach ($replyData as $key => $val) {
                        if (strpos($val, 'Ticket')) {
                            $ticketIdIndex = $key + 1;
                            break;
                        } else {
                            $ticketIdIndex = null;
                        }
                    }
                }
                $structure = imap_fetchstructure($inbox, $mail);
                $attachments = [];
                if (isset($structure->parts) && count($structure->parts)) {
                    for ($i = 0; $i < count($structure->parts); $i++) {
                        $attachments[$i] = array(
                            'is_attachment' => false,
                            'filename' => '',
                            'name' => '',
                            'attachment' => ''
                        );
                        if ($structure->parts[$i]->ifdparameters) {
                            foreach ($structure->parts[$i]->dparameters as $object) {
                                if (strtolower($object->attribute) == 'filename') {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['filename'] = $object->value;
                                    if (empty($customer_Id)) {
                                        $customer_Id = 'guest';
                                    }

                                }
                            }
                        }
                        if ($structure->parts[$i]->ifparameters) {
                            foreach ($structure->parts[$i]->parameters as $object) {
                                if (strtolower($object->attribute) == 'name') {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['name'] = $object->value;
                                    if (empty($customer_Id)) {
                                        $customer_Id = 'guest';
                                    }
                                }
                            }
                        }
                        if ($attachments[$i]['is_attachment']) {
                            $attachments[$i]['attachment'] = imap_fetchbody($inbox, $mail, $i + 1);
                            if ($structure->parts[$i]->encoding == 3) {
                                $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                                if (empty($customer_Id)) {
                                    $customer_Id = 'guest';
                                }
                            } elseif ($structure->parts[$i]->encoding == 4) {
                                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                                if (empty($customer_Id)) {
                                    $customer_Id = 'guest';
                                }
                            }
                        }
                    }
                }
                $files = [];
                foreach ($attachments as $attachment) {
                    if ($attachment['is_attachment'] == 1) {
                        if (empty($filename))
                            $filename = $attachment['filename'];
                        $filesystem = $this->filesystem;
                        $id = $ticketId;
                        $path = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                        $abs_path = $path->getAbsolutePath('images/helpdesk/' . $customerId . '/' . $id . '/' . $date . '/');
                        $io = $this->file;
                        $io->mkdir($abs_path, 0777);
                        $filename = $attachment['name'];
                        $files[] = $date . '/' . $filename;
                        if (empty($filename)) $filename = $attachment['filename'];
                        if (empty($filename)) $filename = time() . ".dat";
                        $fp = fopen($abs_path . "/" . $filename, "w+");
                        fwrite($fp, $attachment['attachment']);
                        fclose($fp);
                    }
                }
                if (!empty($senderMailBox) && !strpos($senderMailBox, 'noreply') && !($replyData[0] == 'Re')) {
                    $ticketModel->setTicketId($ticketId)
                        ->setMessage($message)
                        ->setDepartment('admin')
                        ->setAgent($adminId)
                        ->setAgentName($adminName)
                        ->setSubject($subject)
                        ->setOrder('N/A')
                        ->setCustomerId($customerId)
                        ->setCustomerName($customer_name)
                        ->setCustomerEmail($customer_email)
                        ->setPriority('Normal')
                        ->setStoreView(1)
                        ->setNumMsg(1)
                        ->setStatus('New')
                        ->setCreatedTime($date);
                    $ticketModel->save();
                    $messageModel = $this->messageFactory->create();
                    $messageModel->setMessage($message)
                        ->setFrom($customer_name)
                        ->setTo($adminName)
                        ->setTicketId($ticketId)
                        ->setCreated($date)
                        ->setType('reply');
                    if (!empty($files) && is_array($files)) {
                        $messageModel->setAttachment(implode(',', $files));
                    }
                    $messageModel->save();
                }
                if ($replyData[0] == 'Re' && !empty($ticketIdIndex)) {
                    $messageModel = $this->messageFactory->create();
                    $messageModel->setMessage($message)
                        ->setFrom($customer_name)
                        ->setTo($headerInfo['reply_to'][0]['personal'])
                        ->setTicketId($replyData[$ticketIdIndex])
                        ->setCreated($date)
                        ->setType('reply');
                    if (!empty($files) && is_array($files)) {
                        $messageModel->setAttachment(implode(',', $files));
                    }
                    $messageModel->save();
                }
            }
        }
        imap_expunge($inbox);
        imap_close($inbox);
    }
}