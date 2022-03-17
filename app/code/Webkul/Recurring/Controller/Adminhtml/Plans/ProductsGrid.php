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

class ProductsGrid extends \Webkul\Recurring\Controller\Adminhtml\AbstractRecurring
{
    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $plansModel = $this->plans;
        if ($this->getRequest()->getParam('id')) {
            $plansModel->load($this->getRequest()->getParam('id'));
        }
        $data = $this->backendSession->getFormData(true);
        if (!empty($data)) {
            $plansModel->setData($data);
        }
        $this->registry->register('recurring_data', $plansModel);
        $resultLayout = $this->resultPageFactory->create();
        $resultLayout->getLayout()->getBlock('recurring.plans.edit.tab.products');
        return $resultLayout;
    }
}
