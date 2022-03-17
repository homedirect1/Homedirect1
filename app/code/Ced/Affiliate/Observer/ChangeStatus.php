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
 * Class ChangeStatus
 * @package Ced\Affiliate\Observer
 */
Class ChangeStatus implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Ced\Affiliate\Model\AffiliateComissionFactory
     */
    protected $affiliateComissionFactory;

    /**
     * @var \Ced\Affiliate\Model\AffiliateWalletFactory
     */
    protected $affiliateWalletFactory;

    /**
     * ChangeStatus constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Ced\Affiliate\Model\AffiliateComissionFactory $affiliateComissionFactory
     * @param \Ced\Affiliate\Model\AffiliateWalletFactory $affiliateWalletFactory
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ced\Affiliate\Model\AffiliateComissionFactory $affiliateComissionFactory,
        \Ced\Affiliate\Model\AffiliateWalletFactory $affiliateWalletFactory
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->affiliateComissionFactory = $affiliateComissionFactory;
        $this->affiliateWalletFactory = $affiliateWalletFactory;
    }

    /**
     *Product Assignment Tab
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderData = $observer->getEvent()->getOrder();
        $orderStatus = $this->_scopeConfig->getValue('affiliate/comission/add_comission_when',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $model = $this->affiliateComissionFactory->create()->load($orderData->getIncrementId(), 'increment_id');
        if ($model->getData()):

            $model->setStatus($orderData->getStatus());
            $model->save();
        endif;

        if ($orderData->getPayment()->getMethodInstance()->getCode() == "storecredit") {
            if (!$orderData->getCustomerIsGuest()) {
                $model = $this->affiliateWalletFactory->create()->load($orderData->getCustomerId(), 'customer_id');
                if (!empty($model->getData())) {
                    $model->setUsedAmount($model->getUsedAmount() + $orderData->getGrandTotal());
                    $model->setRemainingAmount($model->getRemainingAmount() - $orderData->getGrandTotal());
                    $model->save();
                }
            }
        }
        return $this;
    }
}    

