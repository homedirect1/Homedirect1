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
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class DeleteAffiliate
 * @package Ced\Affiliate\Observer
 */
class DeleteAffiliate implements ObserverInterface
{
    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * DeleteAffiliate constructor.
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     */
    public function __construct(\Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
    )
    {
        $this->affiliateAccountFactory = $affiliateAccountFactory;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customerData = $observer->getEvent()->getCustomer();
        $customerId = $customerData->getId();
        if ($customerId) {
            $affiliate = $this->affiliateAccountFactory->create()->load($customerId, 'customer_id');

            if ($affiliate && $affiliate->getId()) {
                $affiliate->delete();
            }
        }
    }
}
