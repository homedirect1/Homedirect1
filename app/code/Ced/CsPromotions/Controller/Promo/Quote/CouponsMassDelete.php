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
 * @package     Ced_CsPromotions
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsPromotions\Controller\Promo\Quote;

use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;

/**
 * Class CouponsMassDelete
 * @package Ced\CsPromotions\Controller\Promo\Quote
 */
class CouponsMassDelete extends \Ced\CsPromotions\Controller\Promo\Quote
{
    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory
     */
    protected $salesruleCollectionFactory;

    public function __construct(
        \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory $salesruleCollectionFactory,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Session $customerSession,
        UrlFactory $urlFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        \Ced\CsMarketplace\Helper\Acl $aclHelper,
        \Ced\CsMarketplace\Model\VendorFactory $vendor
    )
    {
        $this->salesruleCollectionFactory = $salesruleCollectionFactory;

        parent::__construct(
            $ruleFactory,
            $context,
            $resultPageFactory,
            $customerSession,
            $urlFactory,
            $registry,
            $jsonFactory,
            $csmarketplaceHelper,
            $aclHelper,
            $vendor
        );
    }

    /**
     * Coupons mass delete action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initRule();
        $rule = $this->_coreRegistry->registry('current_promo_quote_rule');

        if (!$rule->getId()) {
            $this->_forward('noroute');
        }

        $codesIds = $this->getRequest()->getParam('ids');
        $codesIds = explode(',', $codesIds);

        if (is_array($codesIds)) {
            $couponsCollection = $this->salesruleCollectionFactory->create()
                ->addFieldToFilter(
                    'coupon_id',
                    ['in' => $codesIds]
                );

            foreach ($couponsCollection as $coupon) {
                $coupon->delete();
            }
        }
    }
}
