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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class SetComission
 * @package Ced\Affiliate\Observer
 */
class SetComission implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory
     */
    protected $comissionCollectionFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Ced\Affiliate\Model\AffiliateComissionFactory
     */
    protected $affiliateComissionFactory;

    /**
     * SetComission constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Ced\Affiliate\Model\AffiliateComissionFactory $affiliateComissionFactory
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Ced\Affiliate\Model\AffiliateComissionFactory $affiliateComissionFactory
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->affiliateHelper = $affiliateHelper;
        $this->checkoutSession = $checkoutSession;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->comissionCollectionFactory = $comissionCollectionFactory;
        $this->orderFactory = $orderFactory;
        $this->affiliateComissionFactory = $affiliateComissionFactory;
    }

    /**
     *Product Assignment Tab
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->affiliateHelper->isAffiliateEnable()) {

            return $this;
        }

        $affiliateId = $this->checkoutSession->getAffiliateId();

        $affiliateEnable = $this->affiliateAccountFactory->create()->load($affiliateId, 'affiliate_id');

        if ($affiliateEnable->getStatus() != 1) {
            return $this;
        }

        $orderDataid = $observer->getEvent()->getOrderIds();

        foreach ($orderDataid as $_orderId) {
            if ($affiliateId) {
                $orderData = $this->orderFactory->create()->load($_orderId);
                $mode = $this->_scopeConfig->getValue('affiliate/comission/mode',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);


                if ($mode == 'fixed') {
                    $comission = $this->_scopeConfig->getValue('affiliate/comission/charges',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    if (!$comission)
                        $comission = '0.0';
                } else {
                    $baserGrandTotal = $orderData->getBaseGrandTotal();

                    $comission = $this->_scopeConfig->getValue('affiliate/comission/charges',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    if (!$comission)
                        $comission = '0.0';
                    $comission = ($baserGrandTotal * $comission) / 100;
                }
                if ($orderData->getCustomerIsGuest()) {
                    $customerId = 0;
                    $type = 'guest';
                } else {
                    $customerId = $orderData->getCustomerId();
                    $type = 'registered';
                }
                $_usertype = $this->_scopeConfig->getValue('affiliate/comission/differnt_comission_user',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                $_allowSecondComission = $this->_scopeConfig->getValue('affiliate/comission/differnet_comission',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                if ($orderData->getCustomerIsGuest()) {


                    $affiliateCheck = $this->affiliateAccountFactory->create()->load($affiliateId, 'affiliate_id');
                    if ($affiliateCheck->getCustomerEmail() == $orderData->getCustomerEmail()) {
                        $this->checkoutSession->unsAffiliateId();
                        return false;
                    }
                    if ($_usertype) {


                        $_allowSecondComission = $this->_scopeConfig
                            ->getValue('affiliate/comission/differnet_comission',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                        if ($_allowSecondComission) {

                            $model = $this->comissionCollectionFactory->create()
                                ->addFieldToFilter('customer_email', $orderData->getCustomerEmail())
                                ->addFieldToFilter('affiliate_id', $affiliateId);
                            if ($model->getData()) {

                                $mode = $this->_scopeConfig->getValue('affiliate/comission/second_order_mode',
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                                if ($mode == 'fixed') {
                                    $comission = $this->_scopeConfig->getValue('affiliate/comission/second_charges',
                                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                                    if (!$comission)
                                        $comission = '0.0';

                                } else {
                                    $baserGrandTotal = $orderData->getBaseGrandTotal();

                                    $comission = $this->_scopeConfig->getValue('affiliate/comission/second_charges',
                                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                                    if (!$comission)
                                        $comission = '0.0';
                                    $comission = ($baserGrandTotal * $comission) / 100;
                                }

                            }
                        } else {

                            if ($mode == 'fixed') {

                                $comission = $this->_scopeConfig->getValue('affiliate/comission/charges',
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                                if (!$comission)
                                    $comission = '0.0';
                            } else {
                                $baserGrandTotal = $orderData->getBaseGrandTotal();

                                $comission = $this->_scopeConfig->getValue('affiliate/comission/charges',
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                                if (!$comission)
                                    $comission = '0.0';
                                $comission = ($baserGrandTotal * $comission) / 100;
                            }
                        }

                    } else {


                        if ($mode == 'fixed') {

                            $comission = $this->_scopeConfig->getValue('affiliate/comission/charges',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            if (!$comission)
                                $comission = '0.0';
                        } else {
                            $baserGrandTotal = $orderData->getBaseGrandTotal();

                            $comission = $this->_scopeConfig->getValue('affiliate/comission/charges',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            if (!$comission)
                                $comission = '0.0';
                            $comission = ($baserGrandTotal * $comission) / 100;
                        }

                    }

                } else {
                    $affiliateCheck = $this->affiliateAccountFactory->create()->load($affiliateId, 'affiliate_id');
                    if ($affiliateCheck->getCustomerEmail() == $orderData->getCustomerEmail()) {
                        $this->checkoutSession->unsAffiliateId();
                        return false;
                    }
                    if ($_allowSecondComission) {

                        $model = $this->comissionCollectionFactory->create()
                            ->addFieldToFilter('customer_email', $orderData->getCustomerEmail())
                            ->addFieldToFilter('affiliate_id', $affiliateId);
                        if ($model->getData()) {
                            $mode = $this->_scopeConfig->getValue('affiliate/comission/second_order_mode',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            if ($mode == 'fixed') {
                                $comission = $this->_scopeConfig->getValue('affiliate/comission/second_charges',
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                                if (!$comission)
                                    $comission = '0.0';

                            } else {
                                $baserGrandTotal = $orderData->getBaseGrandTotal();

                                $comission = $this->_scopeConfig->getValue('affiliate/comission/second_charges',
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                                if (!$comission)
                                    $comission = '0.0';
                                $comission = ($baserGrandTotal * $comission) / 100;
                            }

                        }
                    } else {
                        if ($mode == 'fixed') {

                            $comission = $this->_scopeConfig->getValue('affiliate/comission/charges',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            if (!$comission)
                                $comission = '0.0';
                        } else {
                            $baserGrandTotal = $orderData->getBaseGrandTotal();

                            $comission = $this->_scopeConfig->getValue('affiliate/comission/charges',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            if (!$comission)
                                $comission = '0.0';
                            $comission = ($baserGrandTotal * $comission) / 100;
                        }

                    }
                }
                $orderStatus = $this->_scopeConfig->getValue('affiliate/comission/add_comission_when',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                $lifetimeSales = $this->_scopeConfig->getValue('affiliate/comission/lifetime_sales_comission',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                if ($lifetimeSales) {

                    $model = $this->affiliateComissionFactory->create()
                        ->load($orderData->getCustomerEmail(), 'customer_email');
                    if ($model->getData()) {

                        $affiliateId = $model->getAffiliateId();
                    }
                }


                $affiliateComission = $this->affiliateComissionFactory->create();
                $affiliateComission->setIncrementId($orderData->getIncrementId());
                $affiliateComission->setComission($comission);
                $affiliateComission->setStatus($orderData->getStatus());
                $affiliateComission->setComissionMode($mode);
                $affiliateComission->setUserType($type);
                $affiliateComission->setCustomerId($customerId);
                $affiliateComission->setCustomerEmail($orderData->getCustomerEmail());
                $affiliateComission->setCustomerName($orderData->getBillingAddress()
                        ->getFirstname() . ' ' . $orderData->getBillingAddress()->getLastname());
                $affiliateComission->setCreateAt(time());
                $affiliateComission->setAffiliateId($affiliateId);
                $affiliateComission->setTotalAmount($orderData->getGrandTotal());
                $affiliateComission->setComissionGiveawayStatus($orderStatus);
                $affiliateComission->save();
            } else {


                $orderData = $this->orderFactory->create()->load($_orderId);
                $mode = $this->_scopeConfig->getValue('affiliate/comission/mode',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);


                if ($mode == 'fixed') {

                    $comission = $this->_scopeConfig->getValue('affiliate/comission/charges',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    if (!$comission)
                        $comission = '0.0';
                } else {
                    $baserGrandTotal = $orderData->getBaseGrandTotal();

                    $comission = $this->_scopeConfig->getValue('affiliate/comission/charges',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    if (!$comission)
                        $comission = '0.0';
                    $comission = ($baserGrandTotal * $comission) / 100;
                }
                if ($orderData->getCustomerIsGuest()) {
                    $customerId = 0;
                    $type = 'guest';
                } else {
                    $customerId = $orderData->getCustomerId();
                    $type = 'registered';
                }
                $_usertype = $this->_scopeConfig->getValue('affiliate/comission/differnt_comission_user',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                $_allowSecondComission = $this->_scopeConfig->getValue('affiliate/comission/differnet_comission',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                if ($orderData->getCustomerIsGuest()) {

                    $affiliateCheck = $this->affiliateAccountFactory->create()
                        ->load($orderData->getCustomerEmail(), 'customer_email');
                    if ($affiliateCheck->getData()) {
                        $this->checkoutSession->unsAffiliateId();
                        return false;
                    }

                    if ($_usertype) {
                        $_allowSecondComission = $this->_scopeConfig
                            ->getValue('affiliate/comission/differnet_comission',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                        if ($_allowSecondComission) {
                            $model = $this->comissionCollectionFactory->create()
                                ->addFieldToFilter('customer_email', $orderData->getCustomerEmail())
                                ->addFieldToFilter('affiliate_id', $affiliateId);
                            if ($model->getData()) {

                                $mode = $this->_scopeConfig->getValue('affiliate/comission/second_order_mode',
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                                if ($mode == 'fixed') {
                                    $comission = $this->_scopeConfig->getValue('affiliate/comission/second_charges',
                                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                                    if (!$comission)
                                        $comission = '0.0';

                                } else {
                                    $baserGrandTotal = $orderData->getBaseGrandTotal();

                                    $comission = $this->_scopeConfig->getValue('affiliate/comission/second_charges',
                                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                                    if (!$comission)
                                        $comission = '0.0';
                                    $comission = ($baserGrandTotal * $comission) / 100;
                                }

                            }
                        } else {

                            if ($mode == 'fixed') {

                                $comission = $this->_scopeConfig->getValue('affiliate/comission/charges',
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                                if (!$comission)
                                    $comission = '0.0';
                            } else {
                                $baserGrandTotal = $orderData->getBaseGrandTotal();

                                $comission = $this->_scopeConfig->getValue('affiliate/comission/charges',
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                                if (!$comission)
                                    $comission = '0.0';
                                $comission = ($baserGrandTotal * $comission) / 100;
                            }
                        }

                    } else {

                        if ($mode == 'fixed') {

                            $comission = $this->_scopeConfig->getValue('affiliate/comission/charges',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            if (!$comission)
                                $comission = '0.0';
                        } else {
                            $baserGrandTotal = $orderData->getBaseGrandTotal();

                            $comission = $this->_scopeConfig->getValue('affiliate/comission/charges',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            if (!$comission)
                                $comission = '0.0';
                            $comission = ($baserGrandTotal * $comission) / 100;
                        }

                    }

                } else {
                    $affiliateCheck = $this->affiliateAccountFactory->create()
                        ->load($orderData->getCustomerEmail(), 'customer_email');
                    if ($affiliateCheck->getData()) {
                        $this->checkoutSession->unsAffiliateId();
                        return false;
                    }
                    if ($_allowSecondComission) {

                        $model = $this->comissionCollectionFactory->create()
                            ->addFieldToFilter('customer_email', $orderData->getCustomerEmail())
                            ->addFieldToFilter('affiliate_id', $affiliateId);
                        if ($model->getData()) {
                            $mode = $this->_scopeConfig->getValue('affiliate/comission/second_order_mode',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            if ($mode == 'fixed') {
                                $comission = $this->_scopeConfig->getValue('affiliate/comission/second_charges',
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                                if (!$comission)
                                    $comission = '0.0';

                            } else {
                                $baserGrandTotal = $orderData->getBaseGrandTotal();

                                $comission = $this->_scopeConfig->getValue('affiliate/comission/second_charges',
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                                if (!$comission)
                                    $comission = '0.0';
                                $comission = ($baserGrandTotal * $comission) / 100;
                            }
                        }
                    } else {
                        if ($mode == 'fixed') {

                            $comission = $this->_scopeConfig->getValue('affiliate/comission/charges',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            if (!$comission)
                                $comission = '0.0';
                        } else {
                            $baserGrandTotal = $orderData->getBaseGrandTotal();

                            $comission = $this->_scopeConfig->getValue('affiliate/comission/charges',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            if (!$comission)
                                $comission = '0.0';
                            $comission = ($baserGrandTotal * $comission) / 100;
                        }

                    }
                }
                $orderStatus = $this->_scopeConfig->getValue('affiliate/comission/add_comission_when',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                $lifetimeSales = $this->_scopeConfig
                    ->getValue('affiliate/comission/lifetime_sales_comission',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                if ($lifetimeSales) {
                    $model = $this->affiliateComissionFactory->create()
                        ->load($orderData->getCustomerEmail(), 'customer_email');
                    if ($model->getData()) {

                        $affiliateId = $model->getAffiliateId();
                    }
                }
                $affiliateComission = $this->affiliateComissionFactory->create();
                $affiliateComission->setIncrementId($orderData->getIncrementId());
                $affiliateComission->setComission($comission);
                $affiliateComission->setStatus($orderData->getStatus());
                $affiliateComission->setComissionMode($mode);
                $affiliateComission->setUserType($type);
                $affiliateComission->setCustomerId($customerId);
                $affiliateComission->setCustomerName($orderData
                        ->getBillingAddress()->getFirstname() . ' ' . $orderData->getBillingAddress()->getLastname());
                $affiliateComission->setCustomerEmail($orderData->getCustomerEmail());
                $affiliateComission->setCreateAt(time());
                $affiliateComission->setAffiliateId($affiliateId);
                $affiliateComission->setTotalAmount($orderData->getGrandTotal());
                $affiliateComission->setComissionGiveawayStatus($orderStatus);
                $affiliateComission->save();
            }
        }
        $this->checkoutSession->unsAffiliateId();
    }

}    

