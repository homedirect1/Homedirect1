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

use Magento\Framework\View\Result\PageFactory;

/**
 * Class TicketsInfo
 * @package Ced\HelpDesk\Controller\Adminhtml\Tickets
 */
class TicketsInfo extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $session;

    /**
     * @var \Ced\HelpDesk\Model\AgentFactory
     */
    protected $agentFactory;

    /**
     * TicketsInfo constructor.
     * @param \Magento\Backend\Model\Auth\Session $session
     * @param \Ced\HelpDesk\Model\AgentFactory $agentFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $session,
        \Ced\HelpDesk\Model\AgentFactory $agentFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $session;
        $this->agentFactory = $agentFactory;
        parent::__construct($context);
    }

    /*
     * Create Page
     */
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $user = $this->session->getUser()->getData('user_id');
        $agent = $this->agentFactory->create()->load($user, 'user_id');
        if ($agent->getRoleName() == "Agent") {
            $this->_redirect('*/*/agentticket');
        }
        return $this->resultPageFactory->create();
    }
}
