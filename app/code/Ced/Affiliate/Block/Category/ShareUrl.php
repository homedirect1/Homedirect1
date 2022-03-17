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

namespace Ced\Affiliate\Block\Category;

/**
 * Class ShareUrl
 * @package Ced\Affiliate\Block\Category
 */
class ShareUrl extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * ShareUrl constructor.
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    )
    {
        $this->_customerSession = $customerSession;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        parent::__construct($context, $data);


    }

    /**
     * @return bool
     */
    public function checkLoggedIn()
    {

        return $this->_customerSession->isLoggedIn();
    }

    /**
     * @return bool
     */
    public function getAffiliateId()
    {

        $affiliate = $this->affiliateAccountFactory->create()
            ->load($this->_customerSession->getCustomer()->getId(), 'customer_id');
        if ($affiliate->getData())
            return $affiliate->getAffiliateId();
        else
            return false;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {

        return $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
    }

    /**
     * @return mixed
     */
    public function isListingPageEnable()
    {

        return $this->_scopeConfig->getValue('affiliate/referfriend/listing_page',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function isProductPageEnable()
    {

        return $this->_scopeConfig->getValue('affiliate/referfriend/details_page',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $class
     * @return mixed
     */
    public function getHelper($class)
    {

        return \Magento\Framework\App\ObjectManager::getInstance()->create($class);

    }
}