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
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Block\Vendor\Dashboard;

use Ced\CsMarketplace\Model\Session;
use Ced\CsMarketplace\Model\Vproducts;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Info
 * @package Ced\CsAdvTransaction\Block\Vendor\Dashboard
 */
class Info extends \Ced\CsMarketplace\Block\Vendor\Dashboard\Info
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Ced\CsAdvTransaction\Helper\Data
     */
    protected $advHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Ced\CsMarketplace\Helper\Report
     */
    protected $reportHelper;

    /**
     * @var \Ced\CsMarketplace\Model\Vproducts
     */
    protected $_vproducts;

    /**
     * Info constructor.
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Ced\CsAdvTransaction\Helper\Data $advHelper
     * @param \Ced\CsMarketplace\Helper\Report $reportHelper
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param Context $context
     * @param Session $customerSession
     * @param UrlFactory $urlFactory
     * @param Vproducts $_vproducts
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Ced\CsAdvTransaction\Helper\Data $advHelper,
        \Ced\CsMarketplace\Helper\Report $reportHelper,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        Context $context,
        Session $customerSession,
        UrlFactory $urlFactory,
        Vproducts $_vproducts,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory $vproductsCollection
    )
    {
        $this->priceCurrency = $priceCurrency;
        $this->advHelper = $advHelper;
        $this->storeManager = $context->getStoreManager();
        $this->reportHelper = $reportHelper;
        parent::__construct($vendorFactory, $customerFactory, $context, $customerSession, $urlFactory, $_vproducts, $objectManager, $vproductsCollection, $resourceConnection);
    }

    /**
     * Get vendor's pending amount data
     *
     * @return Array
     */
    public function getPendingAmount()
    {
        // Total Pending Amount
        $pendingAmount = 0;
        $data = array('total' => $pendingAmount, 'action' => '');
        $priceCurrency = $this->priceCurrency;
        if ($vendorId = $this->getVendorId()) {
            $pendingAmount = $this->advHelper->getPendingPayAmount();

            if ($pendingAmount > 1000000000000) {
                $pendingAmount = round($pendingAmount / 1000000000000, 4);
                $data['total'] = $this->_localeCurrency->getCurrency($this->storeManager->getStore(null)->getBaseCurrencyCode())->toCurrency($pendingAmount) . 'T';

            } elseif ($pendingAmount > 1000000000) {
                $pendingAmount = round($pendingAmount / 1000000000, 4);
                $data['total'] = $this->_localeCurrency->getCurrency($this->storeManager->getStore(null)->getBaseCurrencyCode())->toCurrency($pendingAmount) . 'B';

            } elseif ($pendingAmount > 1000000) {
                $pendingAmount = round($pendingAmount / 1000000, 4);
                $data['total'] = $this->_localeCurrency->getCurrency($this->storeManager->getStore(null)->getBaseCurrencyCode())->toCurrency($pendingAmount) . 'M';

            } elseif ($pendingAmount > 1000) {
                $pendingAmount = round($pendingAmount / 1000, 4);
                $data['total'] = $this->_localeCurrency->getCurrency($this->storeManager->getStore(null)->getBaseCurrencyCode())->toCurrency($pendingAmount) . 'K';

            } else {
                $data['total'] = $priceCurrency->format($pendingAmount);
            }

            $data['action'] = $this->getUrl('*/vorders/', array('_secure' => true, 'order_payment_state' => 2, 'payment_state' => 1));
        }
        return $data;
    }

    /**
     * Get vendor's Earned Amount data
     *
     * @return Array
     */
    public function getEarnedAmount()
    {

        // Total Earned Amount
        $data = array('total' => 0, 'action' => '');
        $priceCurrency = $this->priceCurrency;
        if ($vendorId = $this->getVendorId()) {
            $netAmount = $this->getAssociatedPayments()->getFirstItem()->getBaseBalance();

            if ($netAmount > 1000000000000) {
                $netAmount = round($netAmount / 1000000000000, 4);
                $data['total'] = $this->_localeCurrency->getCurrency($this->storeManager->getStore(null)->getBaseCurrencyCode())->toCurrency($netAmount) . 'T';

            } elseif ($netAmount > 1000000000) {
                $netAmount = round($netAmount / 1000000000, 4);
                $data['total'] = $this->_localeCurrency->getCurrency($this->storeManager->getStore(null)->getBaseCurrencyCode())->toCurrency($netAmount) . 'B';
            } elseif ($netAmount > 1000000) {
                $netAmount = round($netAmount / 1000000, 4);
                $data['total'] = $this->_localeCurrency->getCurrency($this->storeManager->getStore(null)->getBaseCurrencyCode())->toCurrency($netAmount) . 'M';

            } elseif ($netAmount > 1000) {
                $netAmount = round($netAmount / 1000, 4);
                $data['total'] = $this->_localeCurrency->getCurrency($this->storeManager->getStore(null)->getBaseCurrencyCode())->toCurrency($netAmount) . 'K';

            } else {


                $data['total'] = $priceCurrency->format($netAmount);
            }

            $data['action'] = $this->getUrl('*/vpayments/', array('_secure' => true));
        }
        return $data;
    }

    /**
     * Get vendor's Orders Placed data
     *
     * @return Array
     */
    public function getOrdersPlaced()
    {
        // Total Orders Placed
        $data = array('total' => 0, 'action' => '');
        if ($vendorId = $this->getVendorId()) {
            $ordersCollection = $this->getAssociatedOrders();
            $order_total = count($ordersCollection);

            if ($order_total > 1000000000000) {
                $data['total'] = round($order_total / 1000000000000, 1) . 'T';
            } elseif ($order_total > 1000000000) {
                $data['total'] = round($order_total / 1000000000, 1) . 'B';
            } elseif ($order_total > 1000000) {
                $data['total'] = round($order_total / 1000000, 1) . 'M';
            } elseif ($order_total > 1000) {
                $data['total'] = round($order_total / 1000, 1) . 'K';
            } else {
                $data['total'] = $order_total;
            }

            $data['action'] = $this->getUrl('*/vorders/', array('_secure' => true));
        }
        return $data;
    }

    /**
     * Get vendor's Products Sold data
     *
     * @return Array
     */
    public function getProductsSold()
    {
        // Total Products Sold
        $data = array('total' => 0, 'action' => '');
        if ($vendorId = $this->getVendorId()) {
            $productsSold = $this->reportHelper->getVproductsReportModel($this->getVendorId(), '', '', false)->getFirstItem()->getData('ordered_qty');
            if ($productsSold > 1000000000000) {
                $data['total'] = round($productsSold / 1000000000000, 1) . 'T';
            } elseif ($productsSold > 1000000000) {
                $data['total'] = round($productsSold / 1000000000, 1) . 'B';
            } elseif ($productsSold > 1000000) {
                $data['total'] = round($productsSold / 1000000, 1) . 'M';
            } elseif ($productsSold > 1000) {
                $data['total'] = round($productsSold / 1000, 1) . 'K';
            } else {
                $data['total'] = round($productsSold);
            }

            $data['action'] = $this->getUrl('*/vreports/vproducts', array('_secure' => true));
        }
        return $data;
    }

    /**
     * Get vendor's Products data
     *
     * @return Array
     */
    public function getVendorProductsData()
    {
        // Total Pending Products
        $data = array('total' => array(), 'action' => []);
        if ($vendorId = $this->getVendorId()) {
            $vproducts = $this->_vproducts;
            $pendingProducts = count($vproducts->getVendorProducts(\Ced\CsMarketplace\Model\Vproducts::PENDING_STATUS, $vendorId, 0, -1));
            $approvedProducts = count($vproducts->getVendorProducts(\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS, $vendorId, 0, -1));
            $disapprovedProducts = count($vproducts->getVendorProducts(\Ced\CsMarketplace\Model\Vproducts::NOT_APPROVED_STATUS, $vendorId, 0, -1));

            if ($pendingProducts > 1000000000000) {
                $data['total'][] = round($pendingProducts / 1000000000000, 1) . 'T';
            } elseif ($pendingProducts > 1000000000) {
                $data['total'][] = round($pendingProducts / 1000000000, 1) . 'B';
            } elseif ($pendingProducts > 1000000) {
                $data['total'][] = round($pendingProducts / 1000000, 1) . 'M';
            } elseif ($pendingProducts > 1000) {
                $data['total'][] = round($pendingProducts / 1000, 1) . 'K';
            } else {
                $data['total'][] = round($pendingProducts);
            }
            $data['action'][] = $this->getUrl('*/vproducts/', array('_secure' => true, 'check_status' => 2));


            if ($approvedProducts > 1000000000000) {
                $data['total'][] = round($approvedProducts / 1000000000000, 1) . 'T';
            } elseif ($approvedProducts > 1000000000) {
                $data['total'][] = round($approvedProducts / 1000000000, 1) . 'B';
            } elseif ($approvedProducts > 1000000) {
                $data['total'][] = round($approvedProducts / 1000000, 1) . 'M';
            } elseif ($approvedProducts > 1000) {
                $data['total'][] = round($approvedProducts / 1000, 1) . 'K';
            } else {
                $data['total'][] = round($approvedProducts);
            }
            $data['action'][] = $this->getUrl('*/vproducts/', array('_secure' => true, 'check_status' => 1));

            if ($disapprovedProducts > 1000000000000) {
                $data['total'][] = round($disapprovedProducts / 1000000000000, 1) . 'T';
            } elseif ($disapprovedProducts > 1000000000) {
                $data['total'][] = round($disapprovedProducts / 1000000000, 1) . 'B';
            } elseif ($disapprovedProducts > 1000000) {
                $data['total'][] = round($disapprovedProducts / 1000000, 1) . 'M';
            } elseif ($disapprovedProducts > 1000) {
                $data['total'][] = round($disapprovedProducts / 1000, 1) . 'K';
            } else {
                $data['total'][] = round($disapprovedProducts);
            }

            $data['action'][] = $this->getUrl('*/vproducts/', array('_secure' => true, 'check_status' => 0));

        }
        return $data;
    }
}
