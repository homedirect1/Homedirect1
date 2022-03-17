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

namespace Ced\Affiliate\Block\Referral;

use Magento\Framework\View\Element\Template\Context;

/**
 * Class Lists
 * @package Ced\Affiliate\Block\Referral
 */
class Lists extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_getSession;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateReferral\CollectionFactory
     */
    protected $referralCollectionFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateTraffic\CollectionFactory
     */
    protected $trafficCollectionFactory;

    /**
     * Lists constructor.
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateReferral\CollectionFactory $referralCollectionFactory
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateTraffic\CollectionFactory $trafficCollectionFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Ced\Affiliate\Model\ResourceModel\AffiliateReferral\CollectionFactory $referralCollectionFactory,
        \Ced\Affiliate\Model\ResourceModel\AffiliateTraffic\CollectionFactory $trafficCollectionFactory
    )
    {
        $this->_getSession = $customerSession;
        $this->referralCollectionFactory = $referralCollectionFactory;
        $this->trafficCollectionFactory = $trafficCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function getReferralList()
    {
        $customer = $this->_getSession->getCustomer();
        $customer_Id = $customer->getId();
        $productModel = $this->referralCollectionFactory->create()->addFieldtoFilter('customer_id', [
            'customer_id' => $customer_Id
        ]);
        return $productModel;
    }

    /**
     * Prepare Pager Layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getTarfficHistory()) {
            $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'my.custom.pager')
                ->setLimit(5)->setCollection($this->getReferralList());
            $this->setChild('pager', $pager);
        }
        $this->pageConfig->getTitle()->set("Referral Report");
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
     * @return int|mixed
     */
    public function pendingamount()
    {
        $amount = 0;
        $referred_list = $this->getReferralList();
        foreach ($referred_list as $value) {
            $amount += $value['amount'];
        }
        return $amount;
    }

    /**
     * @return int|void
     */
    public function pendingreferral()
    {
        $customer = $this->_getSession->getCustomer();
        $customer_Id = $customer->getId();
        $pendingreferral = $this->referralCollectionFactory->create()->addFieldtoFilter('customer_id', [
            'customer_id' => $customer_Id
        ])->addFieldtoFilter('signup_status', 0)->getData();
        return sizeof($pendingreferral);
    }

    /**
     * @return int|void
     */
    public function registeredreferral()
    {
        $customer = $this->_getSession->getCustomer();
        $customer_Id = $customer->getId();
        $registeredreferral = $this->referralCollectionFactory->create()->addFieldtoFilter('customer_id', [
            'customer_id' => $customer_Id
        ])->addFieldtoFilter('signup_status', 1)->getData();
        return sizeof($registeredreferral);
    }

    /**
     * @return mixed
     */
    public function getTarfficHistory()
    {
        return $this->trafficCollectionFactory->create()
            ->addFieldToFilter('affiliate_id', $this->_getSession->getAffiliateId());
    }
}