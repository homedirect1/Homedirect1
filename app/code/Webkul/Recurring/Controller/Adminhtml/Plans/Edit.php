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
namespace Webkul\Recurring\Controller\Adminhtml\Plans;

use Webkul\Recurring\Controller\Adminhtml\AbstractRecurring as PlansController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

/**
 * Recurring Adminhtml Plans Edit Controller
 */
class Edit extends PlansController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $params     = $this->getRequest()->getParams();
        $plansModel = $this->plans;
        $collection = $this->terms->getCollection()->addFieldToFilter('status', true);
        $data = $this->backendSession->getFormData(true);

        if (isset($params['id']) && $params['id']) {
            $plansModel->load($params['id']);
        }
        if (!empty($data)) {
            $plansModel->setData($data);
        }
        /* Terms data */
        $this->registry->register('terms_data', $collection->getData());
        /* Plans data */
        $this->registry->register('recurring_data', $plansModel);

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Plans'));
        $resultPage->getConfig()->getTitle()->prepend(
            $plansModel->getId() ? $plansModel->getTitle() : __('New Subscription Type')
        );

        $resultPage->addBreadcrumb(
            __('Manage Subscription Type'),
            __('Manage Subscription Type')
        );
        
        $content = $resultPage->getLayout()->createBlock(
            \Webkul\Recurring\Block\Adminhtml\Plans\Edit::class
        );
        $resultPage->addContent($content);
        $content = $resultPage->getLayout()->createBlock(
            \Webkul\Recurring\Block\Adminhtml\Plans\Edit\Tabs::class
        );
        $resultPage->addLeft($content);

        return $resultPage;
    }
}
