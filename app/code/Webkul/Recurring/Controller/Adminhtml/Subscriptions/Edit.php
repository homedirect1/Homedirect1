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

use Webkul\Recurring\Controller\Adminhtml\AbstractRecurring as SubscriptionsController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

/**
 * Recurring Adminhtml Subscriptions Edit Controller
 */
class Edit extends SubscriptionsController
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Webkul_Recurring::subscriptions';
    
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $params     = $this->getRequest()->getParams();
        $model = $this->subscriptions;
        $data = $this->backendSession->getFormData(true);

        if (isset($params['id']) && $params['id']) {
            $model->load($params['id']);
        }
        if (!empty($data)) {
            $model->setData($data);
        }

        /* Subscriptions data */
        $this->registry->register('subscriptions_data', $model);

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Subscription Details'));
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getTitle() : __('Subscription Details')
        );

        $resultPage->addBreadcrumb(
            __('Subscription Details'),
            __('Subscription Details')
        );
        return $resultPage;
    }
}
