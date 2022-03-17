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
class ReturnAction extends PaypalAbstract
{
    /**
     * This function is used to return the paypal credentials
     *
     * @return array
     */
    private function getCredentials()
    {
        $isSandBox          = $this->helper->getConfig(parent::SANDBOX);
        $userName           = $this->helper->getConfig(parent::USERNAME);
        $password           = $this->helper->getConfig(parent::PASSWORD);
        $signature          = $this->helper->getConfig(parent::SIGNATURE);
        return [
            $isSandBox, $userName, $password, $signature
        ];
    }

    /**
     * Get the end point of paypal (url)
     *
     * @param bool $isSandBox
     * @return string
     */
    private function getEndPoint($isSandBox)
    {
        $endPointUrl        = parent::URL;
        $endPointUrl       .= (($isSandBox) ? "sandbox." : "");
        $endPointUrl       .=  parent::URL_COMPLETE;
        return $endPointUrl;
    }

    /**
     * This function is used to get the quotedata
     *
     * @param object $cartData
     * @return array
     */
    private function getQuoteData($cartData)
    {
        $decriptionPlanInfo = $decription =   '';
        $itemNameArray = [];
        $startDate          = date("Y-m-d H:i:s");
        $subscriptionsAmt   = $initialFee = 0.0;
        $planId             = $duration = 0;
        foreach ($cartData as $item) {
            if ($additionalOptionsQuote =   $item->getOptionByCode('custom_additional_options')) {
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
                    if ($option['label'] == 'Plan Id') {
                        $planId = $option['value'];
                    }
                }
            }
        }
        $decription = implode(', ', $itemNameArray);
        $decriptionPlanInfo .= 'Start Date: '.$startDate.', Initial Fee: '.$initialFee
                                .', Subscription Charge: '.$subscriptionsAmt.', ';
        return [$decription, $startDate, $subscriptionsAmt, $initialFee, $planId, $decriptionPlanInfo];
    }

    /**
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $paramsData         =  $this->getRequest()->getParams();
        $token              = $paramsData['token'];
        $payerId            = isset($paramsData['PayerID']) ? $paramsData['PayerID'] : '';
        $orderIncrementId   = isset($paramsData['orderId']) ? $paramsData['orderId'] : '';
        list($isSandBox, $userName, $password, $signature) = $this->getCredentials();
        $endPointUrl =  $this->getEndPoint($isSandBox);
        $postData   = [
                    "USER" => $userName,
                    "PWD" => $password,
                    "SIGNATURE" => $signature,
                    "METHOD" => 'GetExpressCheckoutDetails',
                    "VERSION" => '86',
                    "TOKEN" => $token
                ];
        $this->curl->post($endPointUrl, $postData);
        $response = $this->curl->getBody();
        $responseData = $this->helper->getParsedString($response);
        $detailsResponse = $responseData;
        $order =  $this->orderModel->loadByIncrementId($orderIncrementId);
        /** @var \Magento\Quote\Model\Quote  */
        $quote              = $this->quoteRepository->get($order->getQuoteId());
        $cartData           = $quote->getAllItems();
        $decriptionPlanInfo = $decription =  $period = '';
        $startDate          = date("Y-m-d H:i:s");
        $subscriptionsAmt   = $initialFee = 0.0;
        $planId             = $duration = 0;
        $grandTotal         = $order->getGrandTotal();
        list(
            $decription, $startDate, $subscriptionsAmt, $initialFee, $planId, $decriptionPlanInfo
        ) = $this->getQuoteData($cartData);
        
        if ($planId) {
            $result         = $this->getFrequency($planId);
            if ($result['frequency'] != 0) {
                $duration = $result['frequency'];
                $period = $result['peroid'];
            }
        }

        $grandTotal         = number_format((float)$grandTotal, 2, ".", "");
        $initialFee         = number_format((float)$initialFee, 2, ".", "");
        $subscriptionsAmt   = $grandTotal - $initialFee;
        $tmstamp            = strtotime($startDate);
        $token              = $responseData['TOKEN'];
        $payerId            = $responseData['PAYERID'];
        $shippingAmt        = number_format((float)$order->getShippingAmount(), 2, ".", "");
        $taxAmt             = number_format((float)$order->getTaxAmount(), 2, ".", "");

        if ($shippingAmt > 0) {
            $subscriptionsAmt = $subscriptionsAmt - $shippingAmt;
            $decriptionPlanInfo .= 'Shipping: '.$shippingAmt;
        } else {
            $decriptionPlanInfo = rtrim($decriptionPlanInfo, ', ');
        }
        if ($taxAmt > 0) {
            $subscriptionsAmt = $subscriptionsAmt - $taxAmt;
            $decriptionPlanInfo .= ', Tax: '.$taxAmt;
        }
        $discountAmount     = number_format((float)$order->getDiscountAmount(), 2, ".", "");
        if ($discountAmount < 0) {
            $discountAmount = -$discountAmount;
        }
        $discountedSubscriptionAmt = $subscriptionsAmt - $discountAmount;
        if ($discountAmount > 0) {
            $decriptionPlanInfo .= ', Discount: '.$discountAmount;
        }
        //create recurring profile
        $postData = [
            "USER"                  => $userName,
            "PWD"                   => $password,
            "SIGNATURE"             => $signature,
            "METHOD"                => 'CreateRecurringPaymentsProfile',
            "VERSION"               => '86',
            "TOKEN"                 => $token,
            "PAYERID"               => $payerId,
            "PROFILESTARTDATE"      => date("Y-m-d H:i:s", $tmstamp),
            "DESC"                  => $decriptionPlanInfo,
            "BILLINGPERIOD"         => $period,
            "BILLINGFREQUENCY"      => $duration,
            "AMT"                   => $discountedSubscriptionAmt,
            "INITAMT"               => $initialFee,
            "FAILEDINITAMTACTION"   =>  'ContinueOnFailure',
            "CURRENCYCODE"          => $responseData["CURRENCYCODE"],
            "SHIPPINGAMT"           => $shippingAmt,
            "TAXAMT"                => $taxAmt,
            "COUNTRYCODE"           => $responseData["COUNTRYCODE"],
            "MAXFAILEDPAYMENTS"     => "3",
        ];
        
        $this->curl->post($endPointUrl, $postData);
        $response = $this->curl->getBody();
        $responseData = $this->helper->getParsedString($response);
        if ($responseData['ACK'] == 'Success') {
            $this->coreSession->setData(
                'ref_profile_id',
                $responseData['PROFILEID']
            );
            $collection = $this->subscriptions->getCollection();
            $collection->addFieldToFilter('order_id', $order->getId());
            foreach ($collection as $model) {
                if ($model->getId()) {
                    $this->saveRef($model, $responseData['PROFILEID']);
                }
            }
        } else {
            $order->cancel()->save();
            $resultRedirect = $this->resultRedirect->create(
                $this->resultRedirect::TYPE_REDIRECT
            );
            return $resultRedirect->setUrl(
                $this->urlBulder->getUrl("checkout/onepage/failure")
            );
        }
        $this->createInvoice($payerId, $order, $responseData, $detailsResponse);
        $resultRedirect = $this->resultRedirect->create(
            $this->resultRedirect::TYPE_REDIRECT
        );
        return $resultRedirect->setUrl(
            $this->urlBulder->getUrl("checkout/onepage/success")
        );
    }

    /**
     * This function is used to save the paypal reference subscription id
     *
     * @param object $model
     * @param integer $profileId
     * @return void
     */
    private function saveRef($model, $profileId)
    {
        $model->setData('ref_profile_id', $profileId);
        $model->setId($model->getId());
        $model->save();
    }

    /**
     * This function return the duration of the plan
     *
     * @param integer $planId
     * @return integer
     */
    private function getFrequency($planId)
    {
        $typeId         = $this->plans->load($planId)->getType();
        $termDuration   = $this->term->load($typeId)->getDuration();
        $result         = $this->helper->calculateDuration($termDuration);
        return $result;
    }

    /**
     * This function is used to invoice the order
     *
     * @param integer $payerId
     * @param object $order
     * @param array $responseData
     * @param array $detailsResponse
     * @return void
     */
    private function createInvoice($payerId, $order, $responseData, $detailsResponse)
    {
        $resultData = $this->convertValuesToJson(
            array_merge($responseData, $detailsResponse)
        );
        $payment = $order->getPayment();
        $payment->setTransactionId($payerId);
        $payment->setAdditionalInformation(
            [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $resultData]
        );
        $trans = $this->transactionBuilder;
        $transaction = $trans->setPayment($payment)
            ->setOrder($order)
            ->setTransactionId($payerId)
            ->setAdditionalInformation(
                [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $resultData]
            )
            ->setFailSafe(true)
            ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);
        $payment->setParentTransactionId(null);
        $payment->save();
        $transaction->save();
        $history = $order->addStatusHistoryComment(
            $this->jsonHelper->jsonEncode($responseData)
        );
        $history->setIsCustomerNotified(true);
        try {
            if (!$order->canInvoice()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Cannot create an invoice.')
                );
            }
            $invoice = $this->invoiceService->prepareInvoice($order);
            if (!$invoice->getTotalQty()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Cannot create an invoice without products.')
                );
            }
            $invoice->setTransactionId($payerId);
            $invoice->setRequestedCaptureCase(
                \Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE
            );
            $invoice->register();
            $invoice->save();
            $transactionSave = $this->transaction
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
            $transactionSave->save();
            $this->invoiceSender->send($invoice);
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
                ->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING)
                ->save();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e));
        }
    }

    /**
     * Convert values of array to json
     *
     * @param array $responseDataArray
     * @return array
     */
    public function convertValuesToJson($responseDataArray)
    {
        foreach ($responseDataArray as $key => $value) {
            $responseDataArray[$key] = $this->jsonHelper->jsonEncode($value);
        }
        return $responseDataArray;
    }
}
