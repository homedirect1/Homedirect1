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

namespace Ced\HelpDesk\Controller\Adminhtml\Agents;

use Magento\Backend\App\Action;

/**
 * Class Delete
 * @package Ced\HelpDesk\Controller\Adminhtml\Agents
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\HelpDesk\Model\AgentFactory
     */
    protected $agentFactory;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * Delete constructor.
     * @param \Ced\HelpDesk\Model\AgentFactory $agentFactory
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param Action\Context $context
     */
    public function __construct(
        \Ced\HelpDesk\Model\AgentFactory $agentFactory,
        \Magento\User\Model\UserFactory $userFactory,
        Action\Context $context
    )
    {
        $this->agentFactory = $agentFactory;
        $this->userFactory = $userFactory;
        parent::__construct($context);
    }

    /*
     * delete agent
     */
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if (isset($id) && !empty($id)) {
            $agentModel = $this->agentFactory->create()->load($id);
            $userId = $agentModel->getUserId();

            $agentModel->delete();
            $userModel = $this->userFactory->create()->load($userId);
            $userModel->delete();
            $this->messageManager->addSuccessMessage(
                __('Agent Deleted Successfully...')
            );
        } else {
            $this->messageManager->addSuccessMessage(
                __('Something wents Wrong...')
            );
        }
        return $this->_redirect('*/*/agentsinfo');
    }
}