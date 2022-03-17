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

namespace Ced\Affiliate\Block\Referral\Payout;

use Magento\Framework\View\Element\Template\Context;

/**
 * Class Discount
 * @package Ced\Affiliate\Block\Referral\Payout
 */
class Discount extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_getSession;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\Coupon\CollectionFactory
     */
    protected $couponCollectionFactory;

    /**
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $couponFactory;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * Discount constructor.
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Ced\Affiliate\Model\ResourceModel\Coupon\CollectionFactory $couponCollectionFactory
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Ced\Affiliate\Model\ResourceModel\Coupon\CollectionFactory $couponCollectionFactory,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Ced\Affiliate\Helper\Data $affiliateHelper
    )
    {
        $this->_getSession = $customerSession;
        $this->couponCollectionFactory = $couponCollectionFactory;
        $this->couponFactory = $couponFactory;
        $this->affiliateHelper = $affiliateHelper;
        parent::__construct($context);
    }

    /**
     *
     */
    public function _construct()
    {
        $customer = $this->_getSession->getCustomer();
        $customer_Id = $customer->getId();
        $coupons = $this->couponCollectionFactory->create()->addFieldtoFilter('customer_id', [
            'customer_id' => $customer_Id
        ]);
        $this->setCollection($coupons);
    }

    /**
     * @param $coupon_code
     * @return int
     */
    public function getDiscountCouponStatus($coupon_code)
    {
        $coupon = $this->couponFactory->create()->load($coupon_code, 'code');
        if ($coupon->getId()) {
            $timesUsed = $coupon->getTimesUsed();
            if ($timesUsed > 0) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    /**
     * Prepare Pager Layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCollection()) {
            $pager = $this->getLayout()
                ->createBlock('Magento\Theme\Block\Html\Pager', 'my.custom.pager')
                ->setLimit(5)->setCollection($this->getCollection());
            $this->setChild('pager', $pager);
        }
        $this->pageConfig->getTitle()->set("Your Discount Coupons");
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
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