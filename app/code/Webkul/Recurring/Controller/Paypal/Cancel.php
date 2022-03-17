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
namespace Webkul\Recurring\Controller\Paypal;

/**
 * Webkul Recurring Landing page Index Controller.
 */
class Cancel extends PaypalAbstract
{
    /**
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $paramsData = $this->getRequest()->getParams();
        $orderIncrementId = $paramsData['orderId'] ?? '';
        $order = $this->orderModel->loadByIncrementId($orderIncrementId);
        try {
            $order->cancel();
            $order->save();
        } catch (\Exception $e) {
             throw new \Magento\Framework\Exception\LocalizedException(__($e));
        }
        $this->messageManager->addError(__("Something went wrong with the payment."));
        $resultRedirect = $this->resultRedirect->create(
            $this->resultRedirect::TYPE_REDIRECT
        );
        $resultRedirect->setUrl(
            $this->urlBulder->getUrl("checkout/onepage/failure")
        );
        return $resultRedirect;
    }
}
