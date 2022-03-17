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

use Magento\Backend\App\Action;

/**
 * Class Delete
 * @package Ced\HelpDesk\Controller\Adminhtml\Dept
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\HelpDesk\Model\DepartmentFactory
     */
    protected $departmentFactory;

    /**
     * Delete constructor.
     * @param \Ced\HelpDesk\Model\DepartmentFactory $departmentFactory
     * @param Action\Context $context
     */
    public function __construct(
        \Ced\HelpDesk\Model\DepartmentFactory $departmentFactory,
        Action\Context $context
    )
    {
        $this->departmentFactory = $departmentFactory;
        parent::__construct($context);
    }

    /**
     * delete department
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if (isset($id) && !empty($id)) {
            $model = $this->departmentFactory->create();
            $model->load($id)->delete();
            $this->messageManager->addSuccessMessage(
                __('Department Deleted Successfully...')
            );
        } else {
            $this->messageManager->addSuccessMessage(
                __('Something wents Wrong...')
            );
        }
        $this->_redirect('*/*/deptinfo');
    }
}