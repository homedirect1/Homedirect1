<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsAdvTransaction
 * @author     CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Block\Payments;

use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Details
 * @package Ced\CsAdvTransaction\Block\Payments
 */
class Details extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \Ced\CsMarketplace\Model\VpaymentFactory
     */
    protected $vpaymentFactory;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Ced\CsAdvTransaction\Helper\Data
     */
    protected $advHelper;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vorders\CollectionFactory
     */
    protected $vordersCollection;

    /**
     * @var \Ced\CsAdvTransaction\Model\ResourceModel\Fee\CollectionFactory
     */
    protected $feeCollection;

    /**
     * Details constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ced\CsMarketplace\Model\VpaymentFactory $vpaymentFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Ced\CsAdvTransaction\Helper\Data $advHelper
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vorders\CollectionFactory $vordersCollection
     * @param \Ced\CsAdvTransaction\Model\ResourceModel\Fee\CollectionFactory $feeCollection
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param Context $context
     * @param \Ced\CsMarketplace\Model\Session $customerSession
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ced\CsMarketplace\Model\VpaymentFactory $vpaymentFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Ced\CsAdvTransaction\Helper\Data $advHelper,
        \Ced\CsMarketplace\Model\ResourceModel\Vorders\CollectionFactory $vordersCollection,
        \Ced\CsAdvTransaction\Model\ResourceModel\Fee\CollectionFactory $feeCollection,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        Context $context,
        \Ced\CsMarketplace\Model\Session $customerSession,
        UrlFactory $urlFactory
    )
    {
        $this->storeManager = $storeManager;
        $this->vpaymentFactory = $vpaymentFactory;
        $this->priceCurrency = $priceCurrency;
        $this->advHelper = $advHelper;
        $this->vordersCollection = $vordersCollection;
        $this->feeCollection = $feeCollection;
        parent::__construct($vendorFactory, $customerFactory, $context, $customerSession, $urlFactory);
    }

    /**
     * @return int|mixed
     */
    public function getVendorId()
    {
        $vid = $this->getRequest()->getParam('vendor_id');
        return $vid;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDetails()
    {
        $param = $this->getRequest()->getParams();
        $vendorId = $param['vendor_id'];
        $orderId = $param['order_id'];
        $vpaymentId = $param['vpayment_id'];
        $currencyCode = $this->storeManager->getStore(null)->getBaseCurrencyCode();
        $row = $this->vpaymentFactory->create()->load($vpaymentId)->getData();
        $description = json_decode($row['amount_desc'], true);
        $keys = [];

        foreach ($description as $k => $v) {
            foreach ($v as $k1 => $v1) {

                if ($v['order_id'] == $orderId) {

                    $PaytoVendor = $v['vendor_payment'];

                    $keys[] = $k1;
                    $val[$k1] = $this->priceCurrency->format($v1, false, 2, null, $currencyCode);
                }
            }


        }


        $val['order_id'] = $orderId;
        $payMode = $this->advHelper->getOrderPaymentType($orderId);
        $val['pay_mode'] = $payMode;

        $orders = $this->vordersCollection->create()
            ->addFieldToFilter('vendor_id', $vendorId)
            ->addFieldToFilter('order_id', $orderId)->getFirstItem()->getData();

        $postPaiddetails = $this->advHelper->getPostPaidAmount($orders);

        $grandTotal = $orders['order_total'] + $postPaiddetails['ship_amount'];
        $shipFee = $postPaiddetails['ship_amount'];
        $val['grand_total'] = $this->priceCurrency->format($grandTotal, false, 2, null, $currencyCode);
        $val['shipping_fee'] = $this->priceCurrency->format($shipFee, false, 2, null, $currencyCode);

        $keys = array_unique($keys);

        return ['key' => $keys, 'value' => $val];

    }


    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFees()
    {

        $details = $this->getDetails();

        $keys = $details['key'];

        $fees = $this->feeCollection->create()
            ->addFieldToFilter('field_code', ['in' => $keys])
            ->addFieldToFilter('status', 1)->getData();

        return $fees;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getVendorname($id)
    {
        $vendor = $this->_vendorFactory->create()->load($id);
        return $vendor->getName();
    }

}
