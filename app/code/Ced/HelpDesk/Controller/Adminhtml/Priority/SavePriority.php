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

/**
 * Class SavePriority
 * @package Ced\HelpDesk\Controller\Adminhtml\Priority
 */
class SavePriority extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\HelpDesk\Model\PriorityFactory
     */
    protected $priorityFactory;

    /**
     * SavePriority constructor.
     * @param \Ced\HelpDesk\Model\PriorityFactory $priorityFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Ced\HelpDesk\Model\PriorityFactory $priorityFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->priorityFactory = $priorityFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $back = $this->getRequest()->getParam('back');
        $priorityModel = $this->priorityFactory->create();
        if (!empty($data['id'])) {
            $priorityModel->load($data['id']);
            $priorityModel->setTitle($data['title']);
            $priorityModel->setStatus($data['status']);
            $priorityModel->setBgcolor('#' . $data['bgcolor']);
            $priorityModel->setCode($data['code']);
            $priorityModel->save();
        } else {
            $priorityModel->setTitle($data['title']);
            $priorityModel->setStatus($data['status']);
            $priorityModel->setBgcolor('#' . $data['bgcolor']);
            $priorityModel->setCode($data['code']);
            $priorityModel->save();
            $data['id'] = $priorityModel->getId();
        }
        $this->messageManager->addSuccessMessage(
            __('Save Priority Successfully...')
        );
        (isset($back) && $back == 'edit') ? $this->_redirect('*/*/editpriority/id/' . $data['id']) : $this->_redirect('*/*/priorityinfo');
    }
}