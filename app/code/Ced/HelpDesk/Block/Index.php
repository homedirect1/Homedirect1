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

use Ced\HelpDesk\Model\ResourceModel\Ticket\Collection;

/**
 * Class Index
 * @package Ced\HelpDesk\Block
 */
class Index extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    public $session;

    /**
     * @var \Ced\HelpDesk\Model\Department
     */
    protected $departmentModel;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $ticketcollectionFactory;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Department\CollectionFactory
     */
    protected $departmentcollectionFactory;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Priority\CollectionFactory
     */
    protected $prioritycollectionFactory;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Status\CollectionFactory
     */
    protected $statuscollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $ordercollectionFactory;

    /**
     * Index constructor.
     * @param \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketcollectionFactory
     * @param \Ced\HelpDesk\Model\ResourceModel\Department\CollectionFactory $departmentcollectionFactory
     * @param \Ced\HelpDesk\Model\ResourceModel\Priority\CollectionFactory $prioritycollectionFactory
     * @param \Ced\HelpDesk\Model\ResourceModel\Status\CollectionFactory $statuscollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $ordercollectionFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Ced\HelpDesk\Model\Department $departmentModel
     * @param array $data
     */
    public function __construct(
        \Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory $ticketcollectionFactory,
        \Ced\HelpDesk\Model\ResourceModel\Department\CollectionFactory $departmentcollectionFactory,
        \Ced\HelpDesk\Model\ResourceModel\Priority\CollectionFactory $prioritycollectionFactory,
        \Ced\HelpDesk\Model\ResourceModel\Status\CollectionFactory $statuscollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $ordercollectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Ced\HelpDesk\Model\Department $departmentModel,
        array $data = []
    )
    {
        $this->session = $customerSession;
        $this->ticketcollectionFactory = $ticketcollectionFactory;
        $this->departmentcollectionFactory = $departmentcollectionFactory;
        $this->prioritycollectionFactory = $prioritycollectionFactory;
        $this->statuscollectionFactory = $statuscollectionFactory;
        $this->ordercollectionFactory = $ordercollectionFactory;
        $this->departmentModel = $departmentModel;
        parent::__construct($context, $data);
    }

    /**
     * Set Ticket Model
     */
    public function _construct()
    {
        $customerSession = $this->session;
        $customer_Id = $customerSession->getCustomer()->getId();
        $ticketModel = $this->ticketcollectionFactory->create()
            ->addFieldtoFilter('customer_id', array('customer_id' => $customer_Id))
            ->setOrder('id', 'DESC');
        $this->setTicket($ticketModel);
    }

    /**
     * Prepare Pager Layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var \Magento\Theme\Block\Html\Pager */
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'helpdesk.ticcket.list.pager'
        )->setAvailableLimit(array(5 => 5))
            ->setCollection($this->getTicket());
        $this->setChild('helpdesk.ticcket.list.pager', $pager);
        $this->getTicket()->load();
        return $this;
    }

    /*
     * get pager html
     * */
    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('helpdesk.ticcket.list.pager');
    }

    /**
     * Retrieve department data
     * @return array
     */
    public function deptModel()
    {
        $deptModel = $this->departmentcollectionFactory->create()->addFieldToFilter('active', 1)->getData();
        if (is_array($deptModel) && isset($deptModel)) {
            return $deptModel;
        } else {
            return false;
        }
    }

    /**
     * Retrieve priority data
     * @return array
     */
    public function priorityModel()
    {
        $priorityModel = $this->prioritycollectionFactory->create()->addFieldToFilter('status', 1)->getData();
        if (is_array($priorityModel) && isset($priorityModel)) {
            return $priorityModel;
        } else {
            return false;
        }
    }

    /**
     * Retrieve ticket data
     * @return collection
     */
    public function ticketModel()
    {
        $customer = $this->session->getCustomer();
        $customer_email = $customer->getEmail();
        $ticketModel = $this->ticketcollectionFactory->create()
            ->addFieldtoFilter('customer_email', array('customer_email' => $customer_email))->setOrder('id', 'DESC')->setPageSize(10);
        $count = $ticketModel->count();
        if ($count > 0) {
            return $ticketModel;
        }
    }


    /**
     * Retrieve priority color
     * @return color
     */
    public function priorityColor($priority)
    {
        $Model = $this->prioritycollectionFactory->create()
            ->addFieldtoFilter('title', array('title' => $priority));
        $count = $Model->count();
        if ($count > 0) {
            $pri = $Model->getData();
            if (isset($pri) && is_array($pri)) {
                foreach ($pri as $color) {
                    $bg1 = $color['bgcolor'];
                }
                return $bg1;
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
        $Model = $this->statuscollectionFactory->create()
            ->addFieldtoFilter('title', array('title' => $status));
        $count = $Model->count();
        if ($count > 0) {
            $status_model = $Model->getData();
            if (isset($status_model) && is_array($status_model)) {
                foreach ($status_model as $color) {
                    $bg = $color['bgcolor'];
                }
                return $bg;
            } else {
                return false;
            }
        }
    }

    /**
     * Retrieve sales data
     * @return collection
     */
    public function salesModel()
    {
        $customer = $this->session->getCustomer();
        $customer_Id = $customer->getId();
        $collection = $this->ordercollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('customer_id', $customer_Id);
        $count = $collection->count();
        if ($count > 0) {
            return $collection;
        } else {
            return false;
        }
    }

    /**
     * @param $deptCode
     * @return mixed
     */
    public function departmentName($deptCode)
    {
        if (isset($deptCode) && !empty($deptCode)) {
            return $this->departmentModel->load($deptCode, 'code')->getName();
        } else {
            return $deptCode;
        }
    }
}