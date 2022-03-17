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
 * Class Save
 * @package Ced\HelpDesk\Controller\Adminhtml\Agents
 */
class Save extends \Magento\Backend\App\Action
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
     * @var \Magento\Authorization\Model\RoleFactory
     */
    protected $roleFactory;

    /**
     * Save constructor.
     * @param \Ced\HelpDesk\Model\AgentFactory $agentFactory
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Magento\Authorization\Model\RoleFactory $roleFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Ced\HelpDesk\Model\AgentFactory $agentFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->agentFactory = $agentFactory;
        $this->userFactory = $userFactory;
        $this->roleFactory = $roleFactory;
        parent::__construct($context);
    }

    /**
     * save agent data
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            $back = $this->getRequest()->getParam('back');
            $agentModel = $this->agentFactory->create();
            $userModel = $this->userFactory->create();
            if (!empty($data['id'])) {
                $agentModel->load($data['id']);
                $userId = $agentModel->getUserId();
                $agentModel->setData($data);
                $agentModel->save();
                $userModel->load($userId);
                $userModel->setFirstname($data['firstname']);
                $userModel->setLastname($data['lastname']);
                $userModel->setUsername($data['username']);
                $userModel->setEmail($data['email']);
                $userModel->setIsActive($data['active']);
                if (isset($data['change_password']) && !empty($data['change_password'])) {
                    $userModel->setPassword($data['change_password']);
                }
                $userModel->save();
            } else {
                $roleModel = $this->roleFactory->create();
                $roleId = $roleModel->getCollection()->addFieldToFilter('role_name', 'Agent')->getFirstItem()->getRoleId();
                $userModel->setFirstname($data['firstname']);
                $userModel->setLastname($data['lastname']);
                $userModel->setUsername($data['username']);
                $userModel->setEmail($data['email']);
                $userModel->setIsActive($data['active']);
                $userModel->setPassword($data['password']);
                $userModel->save();

                $userId = $userModel->getUserId();

                $roleModel->setRoleName($data['username']);
                $roleModel->setUserId($userId);
                $roleModel->setRoleType('U');
                $roleModel->setParentId($roleId);
                $roleModel->setUserType('2');
                $roleModel->setTreeLevel('2');
                $roleModel->save();

                $agentModel->setUsername($data['username']);
                $agentModel->setEmail($data['email']);
                $agentModel->setUserId($userId);
                $agentModel->setActive($data['active']);
                $agentModel->setRoleName($data['role_name']);
                $agentModel->save();

                $data['id'] = $agentModel->getId();
            }
            $this->messageManager->addSuccessMessage(
                __('Save Agent Successfully...')
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        (isset($back) && $back == 'edit' && isset($data['id'])) ? $this->_redirect('*/*/editagent/id/' . $data['id']) : $this->_redirect('*/*/agentsinfo');
    }
}
