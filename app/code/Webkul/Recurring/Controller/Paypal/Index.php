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
class Index extends PaypalAbstract
{
    /**
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $isSandBox = $this->helper->getConfig(parent::SANDBOX);
        $userName  = $this->helper->getConfig(parent::USERNAME);
        $password  = $this->helper->getConfig(parent::PASSWORD);
        $signature = $this->helper->getConfig(parent::SIGNATURE);

        /** @var \Magento\Sale\Model\Order  */
        $order = $this->checkoutSession->getLastRealOrder();
        $grandTotal =  $order->getGrandTotal();
        /** @var \Magento\Quote\Model\Quote  */
        $quote = $this->quoteRepository->get($order->getQuoteId());
        $cartData = $quote->getAllItems();
        $currencyCode = $quote->getQuoteCurrencyCode();
        $decription = '';
        $decriptionPlanInfo = '';
        $itemNameArray = [];
        $startDate          = date("Y-m-d H:i:s");
        $subscriptionsAmt   = $initialFee = 0.0;
        foreach ($cartData as $item) {
            if ($additionalOptionsQuote = $item->getOptionByCode('custom_additional_options')) {
                $itemNameArray[] = $item->getName();
                $allOptions = $this->jsonHelper->jsonDecode(
                    $additionalOptionsQuote->getValue()
                );
                foreach ($allOptions as $key => $option) {
                    if ($option['label'] == 'Start Date') {
                        $startDate = $option['value'];
                    }
                    if ($option['label'] == 'Subscription Charge') {
                        $subscriptionsAmt = ((float)$subscriptionsAmt) + ($option['value'] * $item->getQty());
                    }
                    if ($option['label'] == 'Initial Fee') {
                        $initialFee = ((float)$initialFee) + $option['value'];
                    }
                }
            }
        }
        $decription = implode(', ', $itemNameArray);
        $decriptionPlanInfo .= 'Start Date: '.$startDate.', Initial Fee: '.$initialFee
                                .', Subscription Charge: '.$subscriptionsAmt.', ';
        $shippingAmt        = number_format((float)$order->getShippingAmount(), 2, ".", "");
        $taxAmt             = number_format((float)$order->getTaxAmount(), 2, ".", "");
        if ($shippingAmt > 0) {
            $decriptionPlanInfo .= 'Shipping: '.$shippingAmt;
        } else {
            $decriptionPlanInfo = rtrim($decriptionPlanInfo, ', ');
        }
        if ($taxAmt > 0) {
            $decriptionPlanInfo .= ', Tax: '.$taxAmt;
        }
        $discountAmount = number_format((float)$order->getDiscountAmount(), 2, ".", "");
        if ($discountAmount < 0) {
            $discountAmount = -$discountAmount;
        }
        if ($discountAmount > 0) {
            $decriptionPlanInfo .= ', Discount: '.$discountAmount;
        }
        $cancelUrl = $this->urlBulder->getUrl(parent::CANCEL_URL).'?orderId='.$order->getIncrementId();
        $returnUrl = $this->urlBulder->getUrl(parent::RETURN_URL).'?orderId='.$order->getIncrementId();
        $endPointUrl = parent::URL;
        $endPointUrl .= (($isSandBox) ? "sandbox." : "");
        $endPointUrl .=  parent::URL_COMPLETE;
        $postData = [
            "USER"          => $userName,
            "PWD"           => $password,
            "SIGNATURE"     => $signature,
            "METHOD"        => 'SetExpressCheckout',
            "VERSION"       => '86',
            "PAYMENTACTION" => "Authorization",
            "AMT"           =>  number_format($grandTotal, 2),
            "CURRENCYCODE"  => $currencyCode,
            "DESC"          => $decription,
            "L_BILLINGTYPE0"=> 'RecurringPayments',
            "L_BILLINGAGREEMENTDESCRIPTION0" => $decriptionPlanInfo,
            "cancelUrl"     => $cancelUrl,
            "returnUrl"     => $returnUrl
        ];
        $this->curl->post($endPointUrl, $postData);
        $response = $this->curl->getBody();
        $responseData = $this->helper->getParsedString($response);
        $token = '';
        if ($responseData['ACK'] == "Success" || $responseData['ACK'] == "SuccessWithWarning") {
            $token = $responseData['TOKEN'];
        } else {
            $order->cancel();
            $order->save();
            $this->messageManager->addError(__("Something went wrong with the payment."));
            $resultRedirect = $this->resultRedirect->create(
                $this->resultRedirect::TYPE_REDIRECT
            );
            $resultRedirect->setUrl(
                $this->urlBulder->getUrl("checkout/onepage/failure")
            );
            return $resultRedirect;
        }
        $redirecturl = $this->getExpressUrl($isSandBox).$token;
        $resultRedirect = $this->resultRedirect->create(
            $this->resultRedirect::TYPE_REDIRECT
        );
        $resultRedirect->setUrl($redirecturl);
        return $resultRedirect;
    }
}
