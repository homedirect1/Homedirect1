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

namespace Ced\HelpDesk\Block\Adminhtml\Ticket;

use Magento\Framework\View\Element\Template;

/**
 * Class AddNewDelete
 * @package Ced\HelpDesk\Block\Adminhtml\Ticket
 */
class AddNewDelete extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $backendSession;

    /**
     * @var \Ced\HelpDesk\Model\ResourceModel\Agent\CollectionFactory
     */
    protected $agentCollectionFactory;

    /**
     * AddNewDelete constructor.
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     * @param \Ced\HelpDesk\Model\ResourceModel\Agent\CollectionFactory $agentCollectionFactory
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Ced\HelpDesk\Model\ResourceModel\Agent\CollectionFactory $agentCollectionFactory,
        Template\Context $context,
        array $data = []
    )
    {
        $this->backendSession = $backendSession;
        $this->agentCollectionFactory = $agentCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getUserData()
    {
        $user_id = $this->backendSession->getUser()->getData('user_id');
        $data = $this->agentCollectionFactory->create()
            ->addFieldToFilter('user_id', $user_id)
            ->getFirstItem()
            ->getRoleName();
        return $data;
    }
}