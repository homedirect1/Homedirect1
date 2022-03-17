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
 * Class Save
 * @package Ced\CsAdvTransaction\Controller\Adminhtml\Fee
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\CsAdvTransaction\Model\FeeFactory
     */
    protected $feeFactory;

    /**
     * Save constructor.
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
        $postData = $this->getRequest()->getPostValue('fee_fieldset');
        $postData['mode'] = '';

        if (empty($postData['id'])) {
            try {
                if (isset($postData['value'])) {
                    $feeModel = $this->feeFactory->create();
                    $feeModel->setData('field_code', $postData['field_code']);
                    $feeModel->setData('field_label', $postData['field_label']);
                    $feeModel->setData('value', $postData['value']);
                    $feeModel->setData('mode', $postData['mode']);
                    $feeModel->setData('order_state', 1);
                    $feeModel->setData('is_system', 0);
                    $feeModel->setData('type', $postData['type']);
                    $feeModel->setData('status', $postData['status']);
                    $feeModel->save();
                }

                $this->messageManager->addSuccessMessage(__('You Saved ' . $postData['field_label'] . ' Successfully'));
                $this->_redirect('*/*/index');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                $this->_redirect('*/*/index');
            }
        } else {
            try {
                if (isset($postData['value'])) {
                    $feeModel = $this->feeFactory->create()->load($postData['id']);
                    $feeModel->setData('field_label', $postData['field_label']);
                    $feeModel->setData('value', $postData['value']);
                    $feeModel->setData('mode', $postData['mode']);

                    $feeModel->setData('type', $postData['type']);
                    $feeModel->setData('status', $postData['status']);
                    $feeModel->save();
                }

                $this->messageManager->addSuccessMessage(__('You Updated ' . $postData['field_label'] . ' Successfully'));
                $this->_redirect('*/*/index');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                $this->_redirect('*/*/index');
            }

        }
    }
}
