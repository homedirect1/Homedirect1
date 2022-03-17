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

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Ced\HelpDesk\Controller\Adminhtml\Filter\FilterCus;
use Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory;

/**
 * Class MassDelete
 * @package Ced\HelpDesk\Controller\Adminhtml\Tickets
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var \Ced\HelpDesk\Model\Message
     */
    public $messageModel;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_file;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param FilterCus $filter
     * @param CollectionFactory $collectionFactory
     * @param \Ced\HelpDesk\Model\Message $messageModel
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        Context $context,
        FilterCus $filter,
        CollectionFactory $collectionFactory,
        \Ced\HelpDesk\Model\Message $messageModel,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\Filesystem $filesystem
    )
    {
        $this->_file = $file;
        $this->filter = $filter;
        $this->messageModel = $messageModel;
        $this->collectionFactory = $collectionFactory;
        $this->filesystem = $filesystem;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $ticketDeleted = 0;
        foreach ($collection->getItems() as $ticket) {
            $id = $ticket->getTicketId();
            $customerId = $ticket->getCustomerId();
            $ticket->delete();
            $this->unlinkUrl($id, $customerId);
            $this->messageDelete($id);
            $ticketDeleted++;
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $ticketDeleted)
        );
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/tickets/ticketsinfo');
    }

    /**
     * @param $ticketId
     * @throws \Exception
     */
    public function messageDelete($ticketId)
    {
        $messages = $this->messageModel->getCollection()->addFieldToFilter('ticket_id', $ticketId);

        foreach ($messages as $value) {
            $id = $value->getId();
            $this->messageModel->load($id)->delete();
        }
    }

    /**
     * @param $ticketId
     * @param $customerId
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function unlinkUrl($ticketId, $customerId)
    {
        $path = $this->filesystem
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $abs_path = $path->getAbsolutePath('images/helpdesk/' . $customerId . '/' . $ticketId . '/');
        $mesModel = $this->messageModel;
        $mesCollection = $mesModel->getCollection()->addFieldToFilter('ticket_id', $ticketId);
        foreach ($mesCollection->getItems() as $message) {
            $attach = $message->getAttachment();
            $allAttach = explode(',', $attach);
            if (!empty($allAttach) && is_array($allAttach)) {
                foreach ($allAttach as $value) {
                    if ($this->_file->isExists($abs_path . $value)) {
                        $this->_file->isFile($abs_path . $value)?
                        $this->_file->deleteFile($abs_path . $value) :
                        $this->_file->deleteDirectory($abs_path . $value) ;
                    }
                }
            }
        }
    }
}
