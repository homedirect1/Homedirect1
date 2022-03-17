<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Controller\Subscription;

use Magento\Framework\Exception\LocalizedException;

/**
 * Webkul Recurring Landing page Index Controller.
 */
class View extends SubscriptionAbstract
{
    /**
     * Get session
     *
     * @return object
     */
    protected function _getSession()
    {
        return $this->sessionManager;
    }

    /**
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->subscription;
        /**
         * @var \Magento\Backend\Model\View\Result\Page $resultPage
        */
        $resultPage = $this->resultPageFactory->create();
        
        $resultRedirect = $this->resultRedirectFactory->create();
        $isError = 0;
        if ($id) {
            $resultPage->getConfig()->getTitle()->prepend(__('View Subscription'));
            try {
                $model->load($id);
                if ($model->getCustomerId() != $this->customerSession->getCustomer()->getId()) {
                    $isError = 1;
                    $this->messageManager->addError(__('Illegal Access.'));
                }
                if (!$model->getId()) {
                    $this->messageManager->addError(__('This record no longer exists.'));
                    $isError = 1;
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addError(__($e->getMessage()));
                $isError = 1;
            } catch (\Exception $e) {
                $this->messageManager->addError(__($e->getMessage()));
                $isError = 1;
            }
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New'));
        }

        if ($isError) {
            $resultRedirect->setPath("recurring/subscription/manage");
            return $resultRedirect;
        }

        return $resultPage;
    }
}
