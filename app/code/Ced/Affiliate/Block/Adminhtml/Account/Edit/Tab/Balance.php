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

namespace Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab;

/**
 * Class Balance
 * @package Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab
 */
class Balance extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Ced\Affiliate\Model\PaymentMethodsFactory
     */
    protected $paymentMethodsFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory
     */
    protected $withdrawlCollectionFactory;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * Balance constructor.
     * @param \Ced\Affiliate\Model\PaymentMethodsFactory $paymentMethodsFactory
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory $withdrawlCollectionFactory
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\PaymentMethodsFactory $paymentMethodsFactory,
        \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory $withdrawlCollectionFactory,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    )
    {
        $this->paymentMethodsFactory = $paymentMethodsFactory;
        $this->withdrawlCollectionFactory = $withdrawlCollectionFactory;
        $this->affiliateHelper = $affiliateHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return mixed
     */
    public function getActivePayment()
    {
        return $this->paymentMethodsFactory->create()
            ->getPaymentMethodsArray($this->_coreRegistry->registry('current_account')->getCustomerId());
    }

    /**
     * @return mixed
     */
    public function getAmountSummary()
    {

        $amountSummary = $this->withdrawlCollectionFactory->create()
            ->addFieldToFilter('affiliate_id', $this->_coreRegistry->registry('current_account')->getAffiliateId())
            ->addFieldToFilter('status', '1')->addFieldToFilter('payment_mode', array('neq' => 'storecredit'));
        $amountSummary->getSelect()->reset('columns')->columns(['earned_amount' => 'SUM(request_amount)']);
        return $amountSummary->getData();
    }

    /**
     * @param $amount
     * @return mixed
     */
    public function getFormattedPrice($amount)
    {
        return $this->affiliateHelper->getFormattedPrice($amount);
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->affiliateHelper->getAmount($this->_coreRegistry->registry('current_account')
            ->getAffiliateId());
    }

    /**
     * @return mixed
     */
    public function getAmountHistory()
    {
        return $this->affiliateHelper->getAmountHistory($this->_coreRegistry->registry('current_account')
            ->getCustomerId());
    }

}
