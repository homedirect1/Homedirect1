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

namespace Ced\Affiliate\Model\Api\Affiliate;


/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ComissionInformation extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * @var \Ced\Affiliate\Model\AffiliateComissionFactory
     */
    protected $affiliateComissionFactory;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * @var \Ced\Affiliate\Model\PaymentMethodsFactory
     */
    protected $paymentMethodsFactory;

    /**
     * ComissionInformation constructor.
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Ced\Affiliate\Model\AffiliateComissionFactory $affiliateComissionFactory
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Ced\Affiliate\Model\PaymentMethodsFactory $paymentMethodsFactory
     */
    public function __construct(
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Ced\Affiliate\Model\AffiliateComissionFactory $affiliateComissionFactory,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Ced\Affiliate\Model\PaymentMethodsFactory $paymentMethodsFactory

    )
    {
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->affiliateComissionFactory = $affiliateComissionFactory;
        $this->affiliateHelper = $affiliateHelper;
        $this->paymentMethodsFactory = $paymentMethodsFactory;
    }

    /**
     * @param $customerId
     * @return array
     */
    public function getComissionInformation($customerId)
    {

        $affiliateData = [];
        $affiliate = $this->affiliateAccountFactory->create()->load($customerId, 'customer_id');
        $affiliateId = $affiliate->getAffiliateId();
        $affiliateOrders = $this->affiliateComissionFactory->create()
            ->getCollection()->addFieldToFilter('affiliate_id', $affiliateId);
        if ($affiliateOrders->getData()) {
            $affiliateData['affiliate_order'] = $affiliateOrders->getData();
        }
        $totalAmount = $this->affiliateHelper->getAmount($affiliateId);
        $earnedAmount = $this->affiliateHelper->getAmountHistory($customerId);
        $affiliateBalance['total_amount'] = $this->affiliateHelper->getFormattedPrice($totalAmount[0]['total_amount']);
        $affiliateBalance['earned_amount'] = $this->affiliateHelper->getFormattedPrice($earnedAmount[0]['earned_amount']);
        $affiliateBalance['remaining_amount'] = $this->affiliateHelper->getFormattedPrice((float)$totalAmount[0]['total_amount'] - (float)$earnedAmount[0]['earned_amount']);
        $affiliateBalance['toatlamount'] = (float)$totalAmount[0]['total_amount'] - (float)$earnedAmount[0]['earned_amount'];

        $affiliateData['balance_history'] = $affiliateBalance;
        $affiliateData['payment_method'] = $this->paymentMethodsFactory->create()->getPaymentMethodsArray($customerId);
        return ["data" => $affiliateData];
    }
}
