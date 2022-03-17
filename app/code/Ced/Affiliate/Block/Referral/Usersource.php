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
 * Class Usersource
 * @package Ced\Affiliate\Block\Referral
 */
class Usersource extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_getSession;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\Refersource\CollectionFactory
     */
    protected $referCollectionFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateTraffic\CollectionFactory
     */
    protected $trafficCollectionFactory;

    /**
     * Usersource constructor.
     * @param \Ced\Affiliate\Model\ResourceModel\Refersource\CollectionFactory $referCollectionFactory
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateTraffic\CollectionFactory $trafficCollectionFactory
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Ced\Affiliate\Model\ResourceModel\Refersource\CollectionFactory $referCollectionFactory,
        \Ced\Affiliate\Model\ResourceModel\AffiliateTraffic\CollectionFactory $trafficCollectionFactory,
        Context $context,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->_getSession = $customerSession;
        $this->referCollectionFactory = $referCollectionFactory;
        $this->trafficCollectionFactory = $trafficCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @param $source
     * @return int|void
     */
    public function getCount($source)
    {
        $count = array();
        $customer_Id = $this->_getSession->getCustomer()->getId();
        $count = $this->referCollectionFactory->create()
            ->addFieldtoFilter('customer_id', $customer_Id)
            ->addFieldtoFilter('source', $source);
        return count($count);
    }

    /**
     * @return mixed
     */
    public function getTotalSources()
    {

        $traffic = $this->trafficCollectionFactory->create()
            ->addFieldToFilter('affiliate_id', $this->_getSession->getAffiliateId());
        $traffic->getSelect()->reset('columns')->columns(['facebook_tclick' => 'SUM(facebook_click)',
            'google_tclick' => 'SUM(google_click)', 'twitter_tclick' => 'SUM(twitter_click)',
            'email_tclick' => 'SUM(email_click)', 'total_clicks' => 'SUM(total_click)']);
        return $traffic->getData();
    }
}