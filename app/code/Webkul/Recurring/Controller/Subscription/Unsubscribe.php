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
class Unsubscribe extends SubscriptionAbstract
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
        $from           = $this->getRequest()->getParam('from');
        $id             = $this->getRequest()->getParam('id');
        $model          = $this->subscription;
        $resultRedirect = $this->resultRedirectFactory->create();
        $isError = 0;
        $errorFlag = 1;
        if ($id) {
            try {
                $model->load($id);
                if ($model->getCustomerId() != $this->customerSession->getCustomer()->getId()) {
                    $isError = 1;
                    $this->messageManager->addError(__('Illegal Access.'));
                } elseif (!$model->getId()) {
                    $this->messageManager->addError(__('This record no longer exists.'));
                } else {
                    $this->unsubscribe($model);
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addError(__($e->getMessage()));
                $isError = 1;
            } catch (\Exception $e) {
                $this->messageManager->addError(__($e->getMessage()));
                $isError = 1;
            }
        }
        if ($isError || $from == "manage") {
            $resultRedirect->setPath("recurring/subscription/manage");
            return $resultRedirect;
        }
        $resultRedirect->setPath("recurring/subscription/view", ["id" => $id]);
        return $resultRedirect;
    }
    
    /**
     * Unsubscribe profile
     *
     * @param object $model
     * @return void
     */
    private function unsubscribe($model)
    {
        $errorFlag = 0;
        if ($model->getRefProfileId() != "" && $model->getStripeCustomerId() != "") {
            if ($this->stripeHelper->cancelSubscriptions($model)) {
                $model->setStatus(false)->setId($model->getId())->save();
                $errorFlag = 0;
                $this->messageManager->addSuccess(
                    __('This record is Unsubscribed Successfully.')
                );
            }
        }
        if ($model->getRefProfileId() == "") {
            $model->setStatus(false)->setId($model->getId())->save();
            $errorFlag = 0;
            $this->messageManager->addSuccess(
                __('This record is Unsubscribed Successfully.')
            );
        } else {
            if ($this->paypalHelper->cancelSubscriptions($model)) {
                $model->setStatus(false)->setId($model->getId())->save();
                $errorFlag = 0;
                $this->messageManager->addSuccess(
                    __('This record is Unsubscribed Successfully.')
                );
            }
        }
        if ($errorFlag) {
            $this->messageManager->addError(
                __('Invalid profile status for cancel action.')
            );
            $this->messageManager->addNotice(
                __('Future Date Subscription(s).')
            );
        }
    }
}
