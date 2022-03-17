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
 * @category  Ced
 * @package   Ced_CsStorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsStorePickup\Helper;

use Ced\StorePickup\Model\StoreFactory;
use Magento\Customer\Model\Session;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;
use Ced\CsMarketplace\Model\ResourceModel\Vorders;
use Ced\CsMarketplace\Model\VordersFactory;

/**
 * Class Data
 * @package Ced\CsStorePickup\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var StoreFactory
     */
    protected $storesFactory;

    /**
     * @var CountryFactory
     */
    protected $countryFactory;
    /**
     * @var Vorders
     */
    protected $vOrders;
    /**
     * @var VordersFactory
     */
    protected $vOrdersFactory;

    /**
     * Data constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param Registry $registry
     * @param StoreFactory $storesFactory
     * @param CountryFactory $countryFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Registry $registry,
        StoreFactory $storesFactory,
        CountryFactory $countryFactory,
        VordersFactory $vOrdersFactory,
        Vorders $vorders
    ) {
        $this->vOrders = $vorders;
        $this->vOrdersFactory = $vOrdersFactory;
        $this->_customerSession = $customerSession;
        $this->scopeConfig = $context->getScopeConfig();
        $this->registry = $registry;
        $this->storesFactory = $storesFactory;
        $this->countryFactory = $countryFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function isEnable()
    {
        $configvalue = $this->scopeConfig;
        $value = $configvalue->getValue('ced_csstorepickup/general/active');
        return $value;
    }

    /**
     * @return mixed
     */
    public function getCurrentVendorOrder()
    {
        $register = $this->registry;
        return $register->registry('current_vorder');
    }

    /**
     * @param $country
     * @return string
     */
    public function getCountryName($country)
    {
        return $this->countryFactory->create()->load($country)->getName();
    }

    /**
     * @param $order
     * @return array
     */
    public function getStorePickupData($order)
    {
        $vendorId = $this->_customerSession->getVendorId();
        if ($vendorId == null) {
            $vendorId = $this->getVendorIdByOrderId($order->getIncrementId());
        }

        $storedata = $order->getStorePickupData();
        if ($storedata) {
            $storedata = explode('#', $storedata);
            $storeData = array_filter($storedata);
            foreach ($storeData as $_storedata) {
                $data = explode(':', $_storedata);
                if (count($data) > 2) {
                    if ($data[0] == $vendorId) {
                        $storepickupdata['vendor_id'] = $data[0];
                        $storepickupdata['pickup_id'] = $data[1];
                        $storepickupdata['pickup_date'] = $data[2];
                        return $storepickupdata;
                    }
                }
            }
        }
    }

    /**
     * @param $orderId
     * @return mixed
     */
    public function getVendorIdByOrderId($orderId)
    {
        $vendorOrder = $this->vOrdersFactory->create();
        if ($vendorOrder) {
            $this->vOrders->load($vendorOrder, $orderId, 'order_id');
        }
        return $vendorOrder->getVendorId();
    }
}
