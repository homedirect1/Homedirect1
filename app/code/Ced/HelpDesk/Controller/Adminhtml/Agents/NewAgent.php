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

/**
 * Class NewAgent
 * @package Ced\HelpDesk\Controller\Adminhtml\Agents
 */
class NewAgent extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * @var \Ced\HelpDesk\Model\AgentFactory
     */
    protected $agentFactory;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * NewAgent constructor.
     * @param \Ced\HelpDesk\Model\AgentFactory $agentFactory
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Ced\HelpDesk\Model\AgentFactory $agentFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->agentFactory = $agentFactory;
        $this->userFactory = $userFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if (isset($id) && $id != null) {
            $model = $this->agentFactory->create()->load($id);
            $userId = $model->getUserId();
            $userModel = $this->userFactory->create()->load($userId);
            $data = $model->getData();
            $data['firstname'] = $userModel->getFirstname();
            $data['lastname'] = $userModel->getLastname();
            $this->registry->register('ced_agent', $data);
            $title = $data['firstname'] . ' ' . $data['lastname'];
            $resultRedirect = $this->resultPageFactory->create();
            $resultRedirect->getConfig()->getTitle()->set($title);
            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            return $resultRedirect;
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        return $this->resultPageFactory->create();
    }
}