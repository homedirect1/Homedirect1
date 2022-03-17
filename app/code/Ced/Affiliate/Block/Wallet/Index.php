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

namespace Ced\Affiliate\Block\Wallet;

use Ced\Affiliate\Model\ResourceModel\AffiliateComission\Collection;

/**
 * Class Index
 * @package Ced\Affiliate\Block\Wallet
 */
class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Ced\Affiliate\Model\PaymentMethodsFactory
     */
    protected $paymentMethodsFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory
     */
    protected $comissionCollectionFactory;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * @var \Ced\Affiliate\Model\AffiliateWalletFactory
     */
    protected $affiliateWalletFactory;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * Index constructor.
     * @param \Ced\Affiliate\Model\PaymentMethodsFactory $paymentMethodsFactory
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Ced\Affiliate\Model\AffiliateWalletFactory $affiliateWalletFactory
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\PaymentMethodsFactory $paymentMethodsFactory,
        \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Ced\Affiliate\Model\AffiliateWalletFactory $affiliateWalletFactory,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    )
    {
        $this->_customerSession = $customerSession;
        $this->paymentMethodsFactory = $paymentMethodsFactory;
        $this->comissionCollectionFactory = $comissionCollectionFactory;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->affiliateWalletFactory = $affiliateWalletFactory;
        $this->affiliateHelper = $affiliateHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getActivePayment()
    {
        return $this->paymentMethodsFactory->create()
            ->getPaymentMethodsArray($this->_customerSession->getCustomer()->getId());
    }

    /**
     * @return mixed
     */
    public function getComisssion()
    {
        return $this->comissionCollectionFactory->create();
    }

    /**
     * @return mixed
     */
    public function getAffiliateId()
    {

        $model = $this->affiliateAccountFactory->create()
            ->load($this->_customerSession->getCustomer()->getId(), 'customer_id');
        return $model->getAffiliateId();
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {

        $model = $this->affiliateWalletFactory->create()
            ->load($this->_customerSession->getCustomer()->getId(), 'customer_id');
        return $model;
    }

    /**
     * @param $amount
     * @return mixed
     */
    public function getFormattedPrice($amount)
    {
        return $this->affiliateHelper->getFormattedPrice($amount);
    }
}