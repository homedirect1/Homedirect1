<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_HelpDesk
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\HelpDesk\Ui;

use Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory;
use Magento\Backend\Model\Auth\Session;
use Magento\Authorization\Model\RoleFactory;

/**
 * Class TicketsDataProvider
 * @package Ced\HelpDesk\Ui
 */
class TicketsDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Tickets collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    /**
     * @var RoleFactory
     */
    protected $roleFactory;

    /**
     * @var Session
     */
    protected $authSession;

    /**
     * @var \Ced\HelpDesk\Model\AgentFactory
     */
    protected $agentFactory;

    /**
     * @var \Ced\HelpDesk\Model\DepartmentFactory
     */
    protected $departmentFactory;

    /**
     * TicketsDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param Session $authSession
     * @param RoleFactory $roleFactory
     * @param \Ced\HelpDesk\Model\AgentFactory $agentFactory
     * @param \Ced\HelpDesk\Model\DepartmentFactory $departmentFactory
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        Session $authSession,
        RoleFactory $roleFactory,
        \Ced\HelpDesk\Model\AgentFactory $agentFactory,
        \Ced\HelpDesk\Model\DepartmentFactory $departmentFactory,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
        $this->authSession = $authSession;
        $this->roleFactory = $roleFactory;
        $this->agentFactory = $agentFactory;
        $this->departmentFactory = $departmentFactory;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $collection = $this->collection->getData();
        $user = $this->authSession->getUser()->getData('user_id');
        $adminUserRoleId = $this->roleFactory->create()->load('Administrators', 'role_name')->getRoleId();

        $agentId = $this->agentFactory->create()->load($user, 'user_id')->getId();
        $agent = $this->roleFactory->create()->load($user, 'user_id')->getData();
        if ($agent['parent_id'] == $adminUserRoleId) {
            return [
                'totalRecords' => $this->collection->getSize(),
                'items' => array_values($collection),

            ];
        } else {
            $deptData = $this->departmentFactory->create()->getCollection()->addFieldToFilter('department_head', $agentId);
            $dept = [];
            foreach ($deptData as $key => $val) {
                $dept[] = $val['code'];
            }


            if (count($dept) > 0) {
                $newCollection = $this->collection->addFieldToFilter('department', ['in' => $dept])->getData();
                return [
                    'totalRecords' => count($newCollection),
                    'items' => array_values($newCollection),
                ];

            } else {

                $AgentTickets = $this->collection->addFieldToFilter('agent', $agentId)->getData();


                return [
                    'totalRecords' => count($AgentTickets),
                    'items' => array_values($AgentTickets),

                ];
            }
        }
    }

}