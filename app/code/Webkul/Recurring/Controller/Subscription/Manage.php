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

/**
 * Webkul MpSpecialPromotions Landing page Index Controller.
 */
class Manage extends SubscriptionAbstract
{
    /**
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $subscriptionsLabel = __('My Subscriptions');
       
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__($subscriptionsLabel));

        return $resultPage;
    }
}
