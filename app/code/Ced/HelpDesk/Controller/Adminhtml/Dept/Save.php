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

namespace Ced\HelpDesk\Controller\Adminhtml\Dept;

/**
 * Class Save
 * @package Ced\HelpDesk\Controller\Adminhtml\Dept
 */
class Save extends \Magento\Backend\App\Action
{

    /**
     * @var \Ced\HelpDesk\Model\DepartmentFactory
     */
    protected $departmentFactory;

    /**
     * Save constructor.
     * @param \Ced\HelpDesk\Model\DepartmentFactory $departmentFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Ced\HelpDesk\Model\DepartmentFactory $departmentFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->departmentFactory = $departmentFactory;
        parent::__construct($context);
    }

    /**
     * Save Department Information
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $back = $this->getRequest()->getParam('back');
        $deptModel = $this->departmentFactory->create();
        if (isset($data['agent']) && is_array($data['agent']) && !empty($data['agent'])) {
            if (!in_array($data['department_head'], $data['agent'])) {
                $data['agent'][] = $data['department_head'];
            }
            $data['agent'] = implode(',', $data['agent']);
        }
        if (!empty($data['id'])) {
            $deptModel->load($data['id']);
            $deptModel->setName($data['name']);
            $deptModel->setCode($data['code']);
            $deptModel->setAgent($data['agent']);
            $deptModel->setActive($data['active']);
            $deptModel->setDepartmentHead($data['department_head']);
            $deptModel->setDeptSignature($data['dept_signature']);
            $deptModel->save();
        } else {
            $deptModel->setName($data['name']);
            $deptModel->setCode($data['code']);
            $deptModel->setAgent($data['agent']);
            $deptModel->setActive($data['active']);
            $deptModel->setDepartmentHead($data['department_head']);
            $deptModel->setDeptSignature($data['dept_signature']);
            $deptModel->save();
            $data['id'] = $deptModel->getId();
        }
        $this->messageManager->addSuccessMessage(
            __('Save Department Successfully...')
        );
        (isset($back) && $back == 'edit' && isset($data['id'])) ? $this->_redirect('*/*/editdept/id/' . $data['id']) : $this->_redirect('*/*/deptinfo');
    }
}