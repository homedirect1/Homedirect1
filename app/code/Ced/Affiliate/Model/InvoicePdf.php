<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\PurchaseOrder\Model;

use Magento\Sales\Model\Order\Pdf\Config;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;

/**
 * Class InvoicePdf
 * @package Ced\PurchaseOrder\Model
 */
class InvoicePdf extends \Magento\Sales\Model\Order\Pdf\Invoice
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * InvoicePdf constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param Config $pdfConfig
     * @param \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory
     * @param \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Store\Model\App\Emulation $appEmulation
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\App\Emulation $appEmulation,
        array $data = []
    )
    {
        $this->registry = $registry;
        $this->dateTime = $dateTime;
        $this->request = $request;
        parent::__construct($paymentData, $string, $scopeConfig, $filesystem, $pdfConfig, $pdfTotalFactory, $pdfItemsFactory, $localeDate, $inlineTranslation, $addressRenderer, $storeManager, $appEmulation, $data);
    }

    /**
     * Return PDF document
     *
     * @param array|Collection $invoices
     * @return \Zend_Pdf
     */
    public function getInvoice($data, $id)
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $this->registry->register('postdata', $data);

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $x = 35;
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;
        $address = $this->_scopeConfig->getValue('sales/identity/address',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());

        $page = $this->newPage();

        $this->_setFontBold($page, 15);
        $this->_setFontBold($page, 10);
        $page->drawRectangle(50, $this->y - 110, $page->getWidth() - 40, $this->y - 7.5, \Zend_Pdf_Page::SHAPE_DRAW_STROKE);

        $page->drawLine($x + 160, $this->y - 110, $x + 160, $this->y - 7);
        $page->drawLine($x + 360, $this->y - 110, $x + 360, $this->y - 7);

        foreach ($this->_formatAddress($address) as $value) {

            $text = array();
            foreach (str_split($value, 20) as $_value) {
                $text[] = $_value;
            }
            foreach ($text as $part) {
                $page->drawText(strip_tags(ltrim($part)), $x + 25, $this->y - 40, 'UTF-8');
                $this->y -= 11;
            }
        }
        $storeName = $this->_scopeConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        $phone = $this->_scopeConfig->getValue('general/store_information/phone', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        $this->_setFontBold($page, 13);
        $page->drawText($storeName, $x + 200, $this->y - 10, 'UTF-8');
        $this->_setFontBold($page, 10);
        $page->drawText('TEL::', $x + 375, $this->y - 2, 'UTF-8');
        $page->drawText($phone, $x + 405, $this->y - 2, 'UTF-8');
        $page->drawText('FAX::', $x + 375, $this->y - 15, 'UTF-8');
        $page->drawText($phone, $x + 405, $this->y - 15, 'UTF-8');
        $page->drawText(str_replace('http://', '', $this->_storeManager->getStore()->getBaseUrl()), $x + 372, $this->y - 30, 'UTF-8');
        $this->_setFontBold($page, 12);
        $page->drawText('OFFER SHEET.', $x + 220, $this->y - 105, 'UTF-8');
        $page->drawLine($x + 219, $this->y - 107, $x + 295, $this->y - 107, 'UTF-8');
        $this->_setFontBold($page, 10.5);
        $page->drawText('Messr.', $x + 20, $this->y - 110, 'UTF-8');

        $page->drawText('Offer No.', $x + 375, $this->y - 125, 'UTF-8');
        $page->drawText($id . '000' . $id, $x + 420, $this->y - 125, 'UTF-8');
        $page->drawText('Date.', $x + 375, $this->y - 140, 'UTF-8');
        $page->drawText($this->dateTime->gmtDate(), $x + 400, $this->y - 140, 'UTF-8');
        $page->drawText('Ref No.', $x + 375, $this->y - 155, 'UTF-8');
        $page->drawText($id . '00000' . $id, $x + 415, $this->y - 155, 'UTF-8');

        $desc = "We are pleased to offer the under-mentioning article(s) as per conditions and details described as follows";
        $page->drawText($desc, $x + 30, $this->y - 182, 'UTF-8');
        $desc2 = "based on the General Agreement between you and us.";
        $page->drawText($desc2, $x + 20, $this->y - 195, 'UTF-8');

        $this->_drawHeader($page);

        $this->_afterGetPdf();
        return $pdf;
    }


    /**
     * @param \Zend_Pdf_Page $page
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Pdf_Exception
     */
    protected function _drawHeader(\Zend_Pdf_Page $page)
    {

        $itemdata = $this->registry->registry('postdata');

        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(1);
        $page->drawRectangle(35, $this->y - 210, 550, $this->y - 230);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_RGB(0, 0, 0));

        $x = 50;


        $page->drawLine($x + 50, $this->y - 200, $x + 50, $this->y - 220);
        $page->drawLine($x + 210, $this->y - 220, $x + 210, $this->y - 200);
        $page->drawLine($x + 300, $this->y - 220, $x + 300, $this->y - 200);
        $page->drawLine($x + 400, $this->y - 220, $x + 400, $this->y - 200);

        $this->_setFontBold($page, 10);
        $page->drawText('S.NO.', $x - 5, $this->y - 213, 'UTF-8');
        $page->drawText('Description.', $x + 100, $this->y - 213, 'UTF-8');
        $page->drawText('Qty', $x + 240, $this->y - 213, 'UTF-8');
        $page->drawText('Unit Price', $x + 335, $this->y - 213, 'UTF-8');
        $page->drawText('Amount', $x + 430, $this->y - 213, 'UTF-8');


        $page->drawText('1.', $x - 5, $this->y - 240, 'UTF-8');


        foreach ($this->_formatAddress($this->request->getParam('product_name')) as $value) {
            $text = array();
            foreach (str_split($value, 20) as $_value) {
                $text[] = $_value;
            }
            foreach ($text as $part) {
                $page->drawText(strip_tags(ltrim($part)), $x + 65, $this->y - 250, 'UTF-8');
                $this->y -= 11;
            }
        }

        if ($itemdata['nqty'] != '') {
            $qty = $itemdata['nqty'];
            $page->drawText($itemdata['nqty'], $x + 230, $this->y - 240, 'UTF-8');

        } else {
            $qty = $itemdata['qty'];
            $page->drawText($itemdata['qty'], $x + 230, $this->y - 240, 'UTF-8');
        }
        if ($itemdata['nprice'] != '') {
            $price = $itemdata['nprice'];
            $page->drawText($itemdata['nprice'], $x + 330, $this->y - 240, 'UTF-8');
        } else {
            $price = $itemdata['price'];
            $page->drawText($itemdata['price'], $x + 330, $this->y - 240, 'UTF-8');
        }

        $page->drawText($qty * $price, $x + 420, $this->y - 240, 'UTF-8');

        $page->drawRectangle(35, $this->y - 220, 550, $this->y - 300, \Zend_Pdf_Page::SHAPE_DRAW_STROKE);

        $page->drawLine($x + 50, $this->y - 220, $x + 50, $this->y - 300);
        $page->drawLine($x + 210, $this->y - 220, $x + 210, $this->y - 300);
        $page->drawLine($x + 300, $this->y - 220, $x + 300, $this->y - 300);
        $page->drawLine($x + 400, $this->y - 220, $x + 400, $this->y - 300);

        $this->_setFontBold($page, 10);

        $page->drawText('Origin:', $x - 5, $this->y - 320, 'UTF-8');
        $page->drawText($itemdata['origin'], $x + 37, $this->y - 320, 'UTF-8');
        $page->drawText('Quality:', $x - 5, $this->y - 340, 'UTF-8');
        $page->drawText($itemdata['quality'], $x + 37, $this->y - 340, 'UTF-8');
        $page->drawText('Packing:', $x - 5, $this->y - 360, 'UTF-8');
        $page->drawText($itemdata['packing'], $x + 37, $this->y - 360, 'UTF-8');
        $page->drawText('Validity:', $x - 5, $this->y - 420, 'UTF-8');
        $page->drawText($itemdata['validity'], $x + 37, $this->y - 420, 'UTF-8');
        $page->drawText('Remarks:', $x - 5, $this->y - 440, 'UTF-8');
        $page->drawText($itemdata['remarks'], $x + 37, $this->y - 440, 'UTF-8');

        $description = "Looking forward for your valued order for the above offer,";

        $page->drawText($description, $x - 5, $this->y - 470, 'UTF-8');

        $page->drawText("Yours Faithfully", $x + 240, $this->y - 520, 'UTF-8');

        $storeName = $this->_scopeConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        $page->drawText($storeName, $x + 150, $this->y - 540, 'UTF-8');
        $this->_setFontBold($page, 12);
        $page->drawText($storeName, $x + 225, $this->y - 570, 'UTF-8');

    }

    /**
     * Insert order to pdf page
     *
     * @param \Zend_Pdf_Page             &$page
     * @param \Magento\Sales\Model\Order $obj
     * @param bool $putOrderId
     * @return                                        void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function insertOrder(&$page, $obj, $putOrderId = true)
    {
        $vorder = $this->registry->registry('current_vorder');

        if ($obj instanceof \Magento\Sales\Model\Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof \Magento\Sales\Model\Order\Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }

        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->drawRectangle(25, $top, 570, $top - 55);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $this->setDocHeaderCoordinates([25, $top, 570, $top - 55]);
        $this->_setFontRegular($page, 10);

        if ($putOrderId) {
            $page->drawText(__('Order # ') . $order->getRealOrderId(), 35, $top -= 30, 'UTF-8');
        }
        $page->drawText(
            __('Order Date: ') .
            $this->_localeDate->formatDate(
                $this->_localeDate->scopeDate(
                    $order->getStore(),
                    $order->getCreatedAt(),
                    true
                ),
                \IntlDateFormatter::MEDIUM,
                false
            ),
            35,
            $top -= 15,
            'UTF-8'
        );

        $top -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $top, 275, $top - 25);
        $page->drawRectangle(275, $top, 570, $top - 25);

        /* Calculate blocks info */

        /* Billing Address */
        $billingAddress = $this->_formatAddress($this->addressRenderer->format($order->getBillingAddress(), 'pdf'));

        /* Payment */
        $paymentInfo = $this->_paymentData->getInfoBlock($order->getPayment())->setArea('adminhtml')->setIsSecureMode(true)->toPdf();

        $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key => $value) {
            if (strip_tags(trim($value)) == '') {
                unset($payment[$key]);
            }
        }
        reset($payment);

        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress($this->addressRenderer->format($order->getShippingAddress(), 'pdf'));
            $shippingMethod = $order->getShippingDescription();
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);
        $page->drawText(__('Sold to:'), 35, $top - 15, 'UTF-8');

        if (!$order->getIsVirtual()) {
            $page->drawText(__('Ship to:'), 285, $top - 15, 'UTF-8');
        } else {
            $page->drawText(__('Payment Method:'), 285, $top - 15, 'UTF-8');
        }

        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, $top - 25, 570, $top - 33 - $addressesHeight);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $this->y = $top - 40;
        $addressesStartY = $this->y;

        foreach ($billingAddress as $value) {
            if ($value !== '') {
                $text = [];
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
                    $this->y -= 15;
                }
            }
        }

        $addressesEndY = $this->y;

        if (!$order->getIsVirtual()) {
            $this->y = $addressesStartY;
            foreach ($shippingAddress as $value) {
                if ($value !== '') {
                    $text = [];
                    foreach ($this->string->split($value, 45, true, true) as $_value) {
                        $text[] = $_value;
                    }
                    foreach ($text as $part) {
                        $page->drawText(strip_tags(ltrim($part)), 285, $this->y, 'UTF-8');
                        $this->y -= 15;
                    }
                }
            }

            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY;

            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 275, $this->y - 25);
            $page->drawRectangle(275, $this->y, 570, $this->y - 25);

            $this->y -= 15;
            $this->_setFontBold($page, 12);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $page->drawText(__('Payment Method'), 35, $this->y, 'UTF-8');
            $page->drawText(__('Shipping Method:'), 285, $this->y, 'UTF-8');

            $this->y -= 10;
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page, 10);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments = $this->y - 15;
        } else {
            $yPayments = $addressesStartY;
            $paymentLeft = 285;
        }

        foreach ($payment as $value) {
            if (trim($value) != '') {
                //Printing "Payment Method" lines
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                    $yPayments -= 15;
                }
            }
        }

        if ($order->getIsVirtual()) {
            // replacement of Shipments-Payments rectangle block
            $yPayments = min($addressesEndY, $yPayments);
            $page->drawLine(25, $top - 25, 25, $yPayments);
            $page->drawLine(570, $top - 25, 570, $yPayments);
            $page->drawLine(25, $yPayments, 570, $yPayments);

            $this->y = $yPayments - 15;
        } else {


            $topMargin = 15;
            $methodStartY = $this->y;
            $this->y -= 15;
            $yShipments = $this->y;
            if ($vorder->getCode() != null) {

                foreach ($this->string->split($shippingMethod, 45, true, true) as $_value) {
                    $page->drawText(strip_tags(trim($_value)), 285, $this->y, 'UTF-8');
                    $this->y -= 15;
                }

                $yShipments = $this->y;
                $totalShippingChargesText = "(" . __(
                        'Total Shipping Charges'
                    ) . " " . $order->formatPriceTxt(
                        $order->getShippingAmount()
                    ) . ")";

                $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, 'UTF-8');

            }
            $yShipments -= $topMargin + 10;

            $tracks = [];
            if ($shipment) {
                $tracks = $shipment->getAllTracks();
            }
            if (count($tracks)) {
                $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
                $page->drawLine(400, $yShipments, 400, $yShipments - 10);

                $this->_setFontRegular($page, 9);
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
                $page->drawText(__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(__('Number'), 410, $yShipments - 7, 'UTF-8');

                $yShipments -= 20;
                $this->_setFontRegular($page, 8);
                foreach ($tracks as $track) {
                    $maxTitleLen = 45;
                    $endOfTitle = strlen($track->getTitle()) > $maxTitleLen ? '...' : '';
                    $truncatedTitle = substr($track->getTitle(), 0, $maxTitleLen) . $endOfTitle;
                    $page->drawText($truncatedTitle, 292, $yShipments, 'UTF-8');
                    $page->drawText($track->getNumber(), 410, $yShipments, 'UTF-8');
                    $yShipments -= $topMargin - 5;
                }
            } else {
                $yShipments -= $topMargin - 5;
            }

            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            $page->drawLine(25, $methodStartY, 25, $currentY);
            //left
            $page->drawLine(25, $currentY, 570, $currentY);
            //bottom
            $page->drawLine(570, $currentY, 570, $methodStartY);
            //right

            $this->y = $currentY;
            $this->y -= 15;
        }
    }


}
