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

namespace Ced\CsAdvTransaction\Controller\Adminhtml\Pay;

use Magento\Backend\App\Action\Context;

/**
 * Class Deleterequest
 * @package Ced\CsAdvTransaction\Controller\Adminhtml\Pay
 */
class Deleterequest extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\CsAdvTransaction\Model\RequestFactory
     */
    protected $requestFactory;

    /**
     * Deleterequest constructor.
     * @param \Ced\CsAdvTransaction\Model\RequestFactory $requestFactory
     * @param Context $context
     */
    public function __construct(
        \Ced\CsAdvTransaction\Model\RequestFactory $requestFactory,
        Context $context
    )
    {
        $this->requestFactory = $requestFactory;
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
                foreach ($postData as $v) {
                    $ReqyestModel = $this->requestFactory->create()->load($v);
                    $ReqyestModel->delete();
                }
                $this->messageManager->addSuccessMessage(__('You Deleted Pay Request Successfully'));
                $this->_redirect('*/*/index');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                $this->_redirect('*/*/index');
            }
        }
    }
}
