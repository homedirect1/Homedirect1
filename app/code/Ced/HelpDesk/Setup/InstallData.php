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

namespace Ced\HelpDesk\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\UserContextInterface;

/**
 * Class InstallData
 * @package Ced\HelpDesk\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * RoleFactory
     * @var roleModel
     */
    private $roleModel;

    /**
     * RulesFactory
     * @var rulesModel
     */
    private $rulesModel;

    /**
     * @var \Ced\HelpDesk\Model\PriorityFactory
     */
    protected $priorityFactory;

    /**
     * @var \Ced\HelpDesk\Model\StatusFactory
     */
    protected $statusFactory;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * @var \Ced\HelpDesk\Model\DepartmentFactory
     */
    protected $departmentFactory;

    /**
     * @var \Ced\HelpDesk\Model\AgentFactory
     */
    protected $agentFactory;

    /**
     * InstallData constructor.
     * @param \Magento\Authorization\Model\RoleFactory $roleModel
     * @param \Magento\Authorization\Model\RulesFactory $rulesModel
     * @param \Ced\HelpDesk\Model\PriorityFactory $priorityFactory
     * @param \Ced\HelpDesk\Model\StatusFactory $statusFactory
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Ced\HelpDesk\Model\DepartmentFactory $departmentFactory
     * @param \Ced\HelpDesk\Model\AgentFactory $agentFactory
     */
    public function __construct(
        \Magento\Authorization\Model\RoleFactory $roleModel,
        \Magento\Authorization\Model\RulesFactory $rulesModel,
        \Ced\HelpDesk\Model\PriorityFactory $priorityFactory,
        \Ced\HelpDesk\Model\StatusFactory $statusFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Ced\HelpDesk\Model\DepartmentFactory $departmentFactory,
        \Ced\HelpDesk\Model\AgentFactory $agentFactory
    )
    {
        $this->roleModel = $roleModel;
        $this->rulesModel = $rulesModel;
        $this->priorityFactory = $priorityFactory;
        $this->statusFactory = $statusFactory;
        $this->userFactory = $userFactory;
        $this->departmentFactory = $departmentFactory;
        $this->agentFactory = $agentFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Create role Agent
         */
        $role = $this->roleModel->create();
        $role->setName('Agent')//Set Role Name Which you want to create
        ->setPid(0)//set parent role id of your role
        ->setRoleType(RoleGroup::ROLE_TYPE)
            ->setUserType(UserContextInterface::USER_TYPE_ADMIN);
        $role->save();
        $resource = ['Magento_Backend::admin',
            'Magento_Backend::dashboard',
            'Ced_HelpDesk::helpdesk',
            'Ced_HelpDesk::helpdesk_menu',
            'Ced_HelpDesk::tickets_info'
        ];
        $this->rulesModel->create()->setRoleId($role->getId())->setResources($resource)->saveRel();
        $priority = $this->priorityFactory->create();
        $priority->setData('status', '1');
        $priority->setData('title', 'Normal');
        $priority->setData('code', 'Normal');
        $priority->setData('bgcolor', '#CDF7FC');
        $priority->save();
        $priority = $this->priorityFactory->create();
        $priority->setData('status', '1');
        $priority->setData('title', 'Urgent');
        $priority->setData('code', 'Urgent');
        $priority->setData('bgcolor', '#FF0C09');
        $priority->save();
        $priority = $this->priorityFactory->create();
        $priority->setData('status', '1');
        $priority->setData('title', 'ASAP');
        $priority->setData('code', 'Asap');
        $priority->setData('bgcolor', '#FCFF09');
        $priority->save();
        $status = $this->statusFactory->create();
        $status->setData('status', '1');
        $status->setData('title', 'New');
        $status->setData('code', 'New');
        $status->setData('bgcolor', '#7658FF');
        $status->save();
        $status = $this->statusFactory->create();
        $status->setData('status', '1');
        $status->setData('title', 'Open');
        $status->setData('code', 'Open');
        $status->setData('bgcolor', '#B0E0E6');
        $status->save();
        $status = $this->statusFactory->create();
        $status->setData('status', '1');
        $status->setData('title', 'Closed');
        $status->setData('code', 'Closed');
        $status->setData('bgcolor', '#F71F2A');
        $status->save();
        $status = $this->statusFactory->create();
        $status->setData('status', '1');
        $status->setData('title', 'Waiting for customer');
        $status->setData('code', 'Waiting for customer');
        $status->setData('bgcolor', '#FFA19D');
        $status->save();
        $status = $this->statusFactory->create();
        $status->setData('status', '1');
        $status->setData('title', 'Resolved');
        $status->setData('code', 'Resolved');
        $status->setData('bgcolor', '#9FFF11');
        $status->save();
        $adminModel = $this->userFactory->create()->load(1);
        $adminId = $adminModel->getUserId();
        $dept = $this->departmentFactory->create();
        $dept->setName('Admin');
        $dept->setCode('admin');
        $dept->setSortOrder(1);
        $dept->setActive(1);
        $dept->setDepartmentHead($adminId);
        $dept->setAgent($adminId);
        $dept->setDeptSignature(null);
        $dept->save();
        $agentModel = $this->agentFactory->create();
        $agentModel->setUsername($adminModel->getUsername());
        $agentModel->setEmail($adminModel->getEmail());
        $agentModel->setUserId($adminId);
        $agentModel->setActive(1);
        $agentModel->setRoleName('Administrators');
        $agentModel->save();
    }
}