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
namespace Webkul\Recurring\Controller\Adminhtml\Subscriptions;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

/**
 * Recurring Adminhtml Plans massDelete Controller
 */
class MassDisable extends \Webkul\Recurring\Controller\Adminhtml\AbstractRecurring
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Webkul_Recurring::subscriptions';
    
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $subscriptions  = $this->subscriptions;
        $filterModel = $this->massFilter;
        $collection  = $filterModel->getCollection($subscriptions->getCollection());
        $errorFlag   = 1;
        foreach ($collection as $model) {
            if ($model->getRefProfileId() != "" && $model->getStripeCustomerId() != "") {
                if ($this->stripeHelper->cancelSubscriptions($model)) {
                    $this->setStatus($model, parent::DISABLE);
                    $errorFlag = 0;
                }
            }
            if ($model->getRefProfileId() == "") {
                $this->setStatus($model, parent::DISABLE);
                $errorFlag  = 0;
            } else {
                if ($this->paypalHelper->cancelSubscriptions($model)) {
                    $this->setStatus($model, parent::DISABLE);
                    $errorFlag  = 0;
                }
            }
        }
        if ($errorFlag) {
            $this->messageManager->addError(
                __('Invalid profile status for cancel action.')
            );
            $this->messageManager->addNotice(
                __('Future Date Subscription(s).')
            );
        } else {
            $this->messageManager->addSuccess(
                __('Subscription(s) Unsubscribed successfully.')
            );
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
