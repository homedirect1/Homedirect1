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

namespace Ced\HelpDesk\Block;

/**
 * Class Ticket
 * @package Ced\HelpDesk\Block
 */
class Ticket extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $ticketCollectionFactory;

    /**
     * @var \Ced\HelpDesk\Model\StatusFactory
     */
    protected $statusFactory;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Priority\CollectionFactory
     */
    protected $priorityCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Message\CollectionFactory
     */
    protected $messageCollectionFactory;

    /**
     * @var \Magento\Authorization\Model\RoleFactory
     */
    protected $roleFactory;

    /**
     * @var \Ced\HelpDesk\Model\DepartmentFactory
     */
    protected $departmentFactory;

    /**
     * Ticket constructor.
     * @param \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory
     * @param \Ced\HelpDesk\Model\StatusFactory $statusFactory
     * @param \Ced\HelpDesk\Model\ResourceModel\Priority\CollectionFactory $priorityCollectionFactory
     * @param \Magento\Customer\Model\Session $session
     * @param \Ced\HelpDesk\Model\ResourceModel\Message\CollectionFactory $messageCollectionFactory
     * @param \Magento\Authorization\Model\RoleFactory $roleFactory
     * @param \Ced\HelpDesk\Model\DepartmentFactory $departmentFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory,
        \Ced\HelpDesk\Model\StatusFactory $statusFactory,
        \Ced\HelpDesk\Model\ResourceModel\Priority\CollectionFactory $priorityCollectionFactory,
        \Magento\Customer\Model\Session $session,
        \Ced\HelpDesk\Model\ResourceModel\Message\CollectionFactory $messageCollectionFactory,
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Ced\HelpDesk\Model\DepartmentFactory $departmentFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        $this->_storeManager = $context->getStoreManager();
        $this->ticketCollectionFactory = $ticketCollectionFactory;
        $this->statusFactory = $statusFactory;
        $this->priorityCollectionFactory = $priorityCollectionFactory;
        $this->session = $session;
        $this->messageCollectionFactory = $messageCollectionFactory;
        $this->roleFactory = $roleFactory;
        $this->departmentFactory = $departmentFactory;
        parent::__construct($context, $data);

    }

    /**
     * Retrieve ticket data
     * @return array
     */
    public function ticketModel()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $ticketModel = $this->ticketCollectionFactory->create()
                ->addFieldtoFilter('ticket_id', $id);
            $count = $ticketModel->count();
            if ($count > 0) {
                $model = $ticketModel->getData();
                if (isset($model) && is_array($model)) {
                    return $model;
                }
            } else {
                return false;
            }
        }
    }

    /**
     * Retrieve status color
     * @return color
     */
    public function statusColor($status)
    {
        $Model = $this->statusFactory->create()->load($status, 'code')->getBgcolor();
        if (!empty($Model)) {
            return $Model;
        } else {
            return false;
        }
    }

    /**
     * Retrieve priority color
     * @return color
     */
    public function priorityColor($priority)
    {
        $Model = $this->priorityCollectionFactory->create()
            ->addFieldtoFilter('title', array('title' => $priority));
        $count = $Model->count();
        if ($count > 0) {
            $priorityModel = $Model->getData();
            if (isset($priorityModel) && is_array($priorityModel)) {
                foreach ($priorityModel as $color) {
                    $bg = $color['bgcolor'];
                }
                return $bg;
            } else {
                return false;
            }
        }
    }

    /**
     * @return customer email
     */
    public function customerEmail()
    {
        $customer = $this->session->getCustomer();
        $customer_email = $customer->getEmail();
        return $customer_email;
    }

    /**
     * @return mixed
     */
    public function customerId()
    {
        $customer = $this->session->getCustomer();
        $customer_id = $customer->getId();
        return $customer_id;
    }

    /**
     * Retrieve message data
     * @return array
     */
    public function messageModel()
    {
        $id = $this->getRequest()->getParam('id');
        $ticketMessage = $this->messageCollectionFactory->create()->addFieldtoFilter('ticket_id', $id)->getData();
        if (isset($ticketMessage) && is_array($ticketMessage)) {
            return $ticketMessage;
        } else {
            return false;
        }
    }

    /**
     * @return message from details
     */
    public function fromDetails($value)
    {
        $id = $this->getRequest()->getParam('id');
        if (is_numeric($value['from'])) {
            $getadmin = $this->roleFactory->create()->load($value['from']);
            $adminName = $getadmin->getUsername();
            return $adminName;
        } else {
            $ticketModel = $this->ticketCollectionFactory->create()
                ->addFieldtoFilter('ticket_id', $value['ticket_id'])->getData();
            if (isset($ticketModel) && is_array($ticketModel)) {
                foreach ($ticketModel as $value) {
                    $from = $value['customer_name'];
                }
                return $from;
            }
        }
    }

    /**
     * @param $deptCode
     * @return mixed
     */
    public function departmentName($deptCode)
    {
        if (isset($deptCode) && !empty($deptCode)) {
            return $this->departmentFactory->create()->load($deptCode, 'code')->getName();
        } else {
            return $deptCode;
        }
    }

}