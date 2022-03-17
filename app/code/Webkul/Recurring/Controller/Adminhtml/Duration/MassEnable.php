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
namespace Webkul\Recurring\Controller\Adminhtml\Duration;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

/**
 * Recurring Adminhtml Plans massDelete Controller
 */
class MassEnable extends \Webkul\Recurring\Controller\Adminhtml\AbstractRecurring
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Webkul_Recurring::term';

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $plansModel  = $this->terms;
        $filterModel = $this->massFilter;
        $collection  = $filterModel->getCollection($plansModel->getCollection());
        foreach ($collection as $model) {
            $this->setStatus($model, parent::ENABLE);
        }
        $this->messageManager->addSuccess(
            __('Duration Type(s) Enabled successfully.')
        );
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
