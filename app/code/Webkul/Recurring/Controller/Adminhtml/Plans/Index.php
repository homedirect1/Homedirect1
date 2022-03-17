<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul Software Private Limited
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Controller\Adminhtml\Plans;

use Webkul\Recurring\Controller\Adminhtml\AbstractRecurring;

class Index extends AbstractRecurring
{
    /**
     * Plans list
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $pageLabel = __("Subscription Type");
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__($pageLabel));
        return $resultPage;
    }
}
