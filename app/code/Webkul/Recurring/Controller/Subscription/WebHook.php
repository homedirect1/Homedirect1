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

use Magento\Framework\Exception\LocalizedException;

/**
 * Webkul Recurring Landing page Index Controller.
 */
class WebHook extends WebhookAbstract
{
    /**
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $this->printLog('stripeRecurring');
            \Stripe\Stripe::setApiKey(
                $this->stripeHelper->getConfigValue('api_secret_key')
            );
            $input = $this->fileDriver->fileGetContents("php://input");
            if ($input) {
                $response = $this->jsonHelper->jsonDecode($input);
                $webhookType = $response['type'];
                $this->printLog('webhookType '.$webhookType);
                $this->printLog($response);

                switch ($webhookType) {
                    case "checkout.session.completed":
                        if ($response["data"]["object"]["mode"] == 'subscription') {
                            $this->saveSubscriptionData($response);
                        } else {
                            http_response_code(200);
                        }
                        break;
                    case "invoice.payment_succeeded":
                        if (isset($response["data"]["object"]["subscription"])) {
                            $this->processSubscription($response);
                        } else {
                            http_response_code(200);
                        }
                        break;
                }
            }
        } catch (\Stripe\Error $e) {
            $this->printLog('Controller_Subscription_Webhook : '.$e->getMessage());
        } catch (\Exception $e) {
            $this->printLog('Controller_Subscription_Webhook : '.$e->getMessage());
        }
        http_response_code(200);
    }

    private function processSubscription($response)
    {
        $subscriptionsCollection = $this->subscription->create()
            ->getCollection()
            ->addFieldToFilter(
                "ref_profile_id",
                $response["data"]["object"]["subscription"]
            )
            ->addFieldToFilter(
                "stripe_customer_id",
                $response["data"]["object"]["customer"]
            );
        $planId = $subscriptionId = 0;
        $chargeId = $response["data"]["object"]["charge"];
        $charge = \Stripe\Charge::retrieve($chargeId);
        
        if (($charge["status"] == "paid" || "succeeded")) {
            foreach ($subscriptionsCollection as $subscription) {
                $subscriptionId = $subscription->getId();
                $planId         = $subscription->getPlanId();
                $createdAt      = $subscription->getCreatedAt();
            }
            $todayDate = date('Y-m-d');
            $txnId = $response["data"]["object"]["charge"];
            if ($planId && strpos($createdAt, $todayDate) === false) {
                $incrementId = $response["data"]["object"]["lines"]["data"][0]["plan"]["nickname"];
                $order = $this->orderModel->create()->loadByIncrementId($incrementId);
                $this->createOrder($planId, $order, $subscriptionId, $txnId, $response);
            } elseif ($planId) {
                $incrementId = $response["data"]["object"]["lines"]["data"][1]["plan"]["nickname"];
                $order = $this->orderModel->create()->loadByIncrementId($incrementId);
                $order->setTotalPaid($order->getGrandTotal())
                ->setBaseTotalPaid($order->getBaseGrandTotal())
                ->save();
                $this->createInvoice($order, $txnId, $response);
            }
        }
    }

    /**
     * Create order in magento for stripe recurring subscription
     *
     * @param integer $planId
     * @param integer $order
     * @param integer $subscriptionId
     * @param string $txnId
     * @param array $response
     * @return void
     */
    private function createOrder($planId, $order, $subscriptionId, $txnId, $response)
    {
        try {
            $plan = $this->cron->getSubscriptionType($planId);
            $result = $this->orderHelper->createMageOrder($order, $plan['name']);
            
            if (isset($result['error']) && $result['error'] == 0) {
                $this->cron->saveMapping($planId, $result['id'], $subscriptionId);
                $this->createTransaction($result['id'], $txnId, $response);
            }
        } catch (\Exception $e) {
            $this->cron->printLog('Controller_Subscription_Webhook : '.$e->getMessage());
        }
    }

    /**
     * Create transaction
     *
     * @param integer $id
     * @param integer $txnId
     * @param array $responseData
     * @return void
     */
    private function createTransaction($id, $txnId, $responseData)
    {
        $order = $this->orderModel->create()->load($id);
        $payment = $order->getPayment();
        $payment->setTransactionId($txnId);
        $transaction = $payment->addTransaction(
            \Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE
        );
        $payment->setIsTransactionClosed(1);
         
        $payment->setTransactionAdditionalInfo(
            \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
            ['description' => $this->jsonHelper->jsonEncode($responseData)]
        );
        $transaction->setIsTransactionClosed(1);
        $transaction->save();
        $comment = $this->jsonHelper->jsonEncode($responseData);
        $history = $order->addStatusHistoryComment($comment, false);
        $history->setIsCustomerNotified(true);
        $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
        $order->save();
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
            $invoice->setRequestedCaptureCase(
                \Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE
            );
            $invoice->register();
            $transactionSave = $this->transaction
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
            $transactionSave->save();
            $this->invoiceSender->send($invoice);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->printLog('Controller_Subscription_Webhook : '.$e->getMessage());
        } catch (\Exception $e) {
            $this->printLog('Controller_Subscription_Webhook : '.$e->getMessage());
        }
    }

    /**
     * Create invoice
     * @param object $order
     * @param integer $txnId
     * @param array $responseData
     * @return void
     */
    private function createInvoice($order, $txnId, $responseData)
    {
        $resultData = $this->convertValuesToJson(
            $responseData
        );
        $payment = $order->getPayment();
        $payment->setTransactionId($txnId);
        $payment->setAdditionalInformation(
            [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $resultData]
        );
        $trans = $this->transactionBuilder;
        $transaction = $trans->setPayment($payment)
            ->setOrder($order)
            ->setTransactionId($txnId)
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
            $invoice->setTransactionId($txnId);
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
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->printLog('Controller_Subscription_Webhook : '.$e->getMessage());
        } catch (\Exception $e) {
            $this->printLog('Controller_Subscription_Webhook : '.$e->getMessage());
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

    private function saveSubscriptionData($response)
    {
        $subscriptionsCollection = $this->subscription->create()
            ->getCollection()
            ->addFieldToFilter(
                "order_id",
                $response["data"]["object"]["client_reference_id"]
            );
        foreach ($subscriptionsCollection as $subscription) {
            $this->saveSubscription(
                $subscription,
                $response["data"]["object"]["subscription"],
                $response["data"]["object"]["customer"]
            );
        }
    }

    /**
     * This function is used to save the stripe subscription id
     *
     * @param object $model
     * @param integer $profileId
     * @return void
     */
    private function saveSubscription($model, $profileId, $stripeCustomerId)
    {
        $model->setData('ref_profile_id', $profileId);
        $model->setData('stripe_customer_id', $stripeCustomerId);
        $model->setId($model->getId());
        $model->save();
    }
}
