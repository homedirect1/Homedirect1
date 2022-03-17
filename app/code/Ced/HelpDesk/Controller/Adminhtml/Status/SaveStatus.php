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

namespace Ced\HelpDesk\Controller\Adminhtml\Status;

/**
 * Class SaveStatus
 * @package Ced\HelpDesk\Controller\Adminhtml\Status
 */
class SaveStatus extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\HelpDesk\Model\StatusFactory
     */
    protected $statusFactory;

    /**
     * SaveStatus constructor.
     * @param \Ced\HelpDesk\Model\StatusFactory $statusFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Ced\HelpDesk\Model\StatusFactory $statusFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->statusFactory = $statusFactory;
        parent::__construct($context);
    }

    /**
     * Save Status Information
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $back = $this->getRequest()->getParam('back');
        $statusModel = $this->statusFactory->create();
        if (!empty($data['id'])) {
            $statusModel->load($data['id']);
            $statusModel->setTitle($data['title']);
            $statusModel->setStatus($data['status']);
            $statusModel->setBgcolor('#' . $data['bgcolor']);
            $statusModel->setCode($data['code']);
            $statusModel->save();
        } else {
            $statusModel->setTitle($data['title']);
            $statusModel->setStatus($data['status']);
            $statusModel->setBgcolor('#' . $data['bgcolor']);
            $statusModel->setCode($data['code']);
            $statusModel->save();
            $data['id'] = $statusModel->getId();
        }
        $this->messageManager->addSuccessMessage(
            __('Save Status Successfully...')
        );
        (isset($back) && $back == 'edit') ? $this->_redirect('*/*/editstatus/id/' . $data['id']) : $this->_redirect('*/*/statusinfo');
    }
}