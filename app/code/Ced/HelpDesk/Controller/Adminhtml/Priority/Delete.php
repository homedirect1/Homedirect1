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

namespace Ced\HelpDesk\Controller\Adminhtml\Priority;

use Magento\Backend\App\Action;

/**
 * Class Delete
 * @package Ced\HelpDesk\Controller\Adminhtml\Priority
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\HelpDesk\Model\PriorityFactory
     */
    protected $priorityFactory;

    /**
     * Delete constructor.
     * @param \Ced\HelpDesk\Model\PriorityFactory $priorityFactory
     * @param Action\Context $context
     */
    public function __construct(\Ced\HelpDesk\Model\PriorityFactory $priorityFactory, Action\Context $context)
    {
        $this->priorityFactory = $priorityFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if (isset($id) && !empty($id)) {
            $this->priorityFactory->create()->load($id)->delete();
            $this->messageManager->addSuccessMessage(
                __('Priority Deleted Successfully...')
            );
        } else {
            $this->messageManager->addSuccessMessage(
                __('Something wents Wrong...')
            );
        }
        $this->_redirect('*/*/priorityinfo');
    }
}