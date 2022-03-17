<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_Recurring
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Plugin;

use Magento\Framework\App\Response\Http as responseHttp;
use Magento\Framework\UrlInterface;
use Magento\Framework\Exception\LocalizedException;

class TotalsCollector
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var responseHttp
     */
    private $response;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var \Magento\Quote\Model\Quote\Item
     */
    private $itemModel;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param responseHttp $response
     * @param \Magento\Quote\Model\Quote\Item $itemModel
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param UrlInterface $url
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        responseHttp $response,
        \Magento\Quote\Model\Quote\Item $itemModel,
        \Magento\Checkout\Model\Session $checkoutSession,
        UrlInterface $url
    ) {
        $this->messageManager   = $messageManager;
        $this->checkoutSession  = $checkoutSession;
        $this->itemModel        = $itemModel;
        $this->request          = $request;
        $this->jsonHelper       = $jsonHelper;
        $this->response         = $response;
        $this->url              = $url;
    }

    /**
     * Plugin executes before collect rates
     *
     * @param \Magento\Quote\Model\Quote\TotalsCollector $subject
     * @param \Magento\Quote\Model\Quote $quote
     * @return array
     */
    public function beforeCollect(
        \Magento\Quote\Model\Quote\TotalsCollector $subject,
        \Magento\Quote\Model\Quote $quote
    ) {
        $cartData = $quote->getAllVisibleItems();
        $startDate = '';
        $proUrl = '';
        $itemId = 0;
        try {
            foreach ($cartData as $item) {
                $proUrl = $item->getProduct()->getProductUrl();
                if ($customAdditionalOptionsQuote = $item->getOptionByCode('custom_additional_options')) {
                    $allOptions = $this->jsonHelper->jsonDecode(
                        $customAdditionalOptionsQuote->getValue()
                    );
                    foreach ($allOptions as $option) {
                        $itemId = $item->getItemId();
                        if ($option['label'] == 'Start Date') {
                            $startDate = $option['value'];
                        }
                    }
                }
            }
            if ($startDate != "") {
                if (!$this->isValid($startDate)) {
                    if ($itemId) {
                        $quoteItem = $this->itemModel->load($itemId);
                    }
                    throw new LocalizedException(
                        __('Start Date is Invalid for the Subscription. Please Add Again')
                    );
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->response->setRedirect($proUrl);
        }
        return [$quote];
    }

    /**
     * Check the date is invalid or not
     *
     * @param string $date
     * @return bool
     */
    private function isValid($date)
    {
        return (strtotime($date) >= strtotime('today'));
    }
}
