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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

/**
 * Recurring Adminhtml Plans massDisable Controller
 */
class MassDisable extends \Webkul\Recurring\Controller\Adminhtml\AbstractRecurring
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $plansModel  = $this->plans;
        $filterModel = $this->massFilter;
        $collection  = $filterModel->getCollection($plansModel->getCollection());
        foreach ($collection as $model) {
            $this->setStatus($model, parent::DISABLE);
        }
        $this->messageManager->addSuccess(
            __('Subscription Type(s) Disabled successfully.')
        );
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
