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
 * @package     Ced_CsAdvTransaction
 * @author     CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Controller\Adminhtml\Fee;

use Magento\Backend\App\Action\Context;

/**
 * Class Delete
 * @package Ced\CsAdvTransaction\Controller\Adminhtml\Fee
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\CsAdvTransaction\Model\FeeFactory
     */
    protected $feeFactory;

    /**
     * Delete constructor.
     * @param \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory
     * @param Context $context
     */
    public function __construct(
        \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory,
        Context $context
    )
    {
        $this->feeFactory = $feeFactory;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */

    public function execute()
    {
        $postData = $this->getRequest()->getParam('id');

        if (!empty($postData)) {
            try {

                $feeModel = $this->feeFactory->create()->load($postData);
                $fee = $feeModel->getFieldLabel();
                $feeModel->delete();

                $this->messageManager->addSuccessMessage(__('You Deleted ' . $fee . ' Successfully'));
                $this->_redirect('*/*/index');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                $this->_redirect('*/*/index');
            }
        }
    }
}
