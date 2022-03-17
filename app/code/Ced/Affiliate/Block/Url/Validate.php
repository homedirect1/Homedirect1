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

namespace Ced\Affiliate\Block\Url;

/**
 * Class Validate
 * @package Ced\Affiliate\Block\Url
 */
class Validate extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * @var \Ced\Affiliate\Model\AffiliateBannerFactory
     */
    protected $affiliateBannerFactory;

    /**
     * Validate constructor.
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Ced\Affiliate\Model\AffiliateBannerFactory $affiliateBannerFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Ced\Affiliate\Model\AffiliateBannerFactory $affiliateBannerFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        array $data = []
    )
    {
        $this->_checkoutSession = $checkoutSession;
        $this->date = $date;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->affiliateBannerFactory = $affiliateBannerFactory;
        parent::__construct($context, $data);


    }

    /**
     * @return mixed
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
     * @return mixed
     */
    public function isModuleEnabled()
    {

        return $this->_scopeConfig->getValue('affiliate/account/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function IsurlParams()
    {

        if ($this->getRequest()->getParam('affiliate'))
            return true;

        return false;
    }

    /**
     * @return bool
     */
    public function checkAffiliate()
    {
        if ($this->getRequest()->getParam('affiliate'))
            return $this->affiliateAccountFactory->create()
                ->load($this->getRequest()->getParam('affiliate'), 'affiliate_id');

        return false;
    }

    /**
     * @return mixed
     */
    public function getAffiliateUrl()
    {

        return $this->affiliateBannerFactory->create()->load($this->getRequest()->getParam('bannerid'));
    }

    /**
     * @return false|int
     */
    public function getCurrentTimestamp()
    {
        return strtotime($this->date->gmtDate('Y-m-d'));
    }

    /**
     *
     */
    public function unsetSession()
    {

        $this->_checkoutSession->unsAffiliateId();

    }

    /**
     *
     */
    public function setSession()
    {
        if ($this->getRequest()->getParam('affiliate')) {
            $this->_checkoutSession->setAffiliateId($this->getRequest()->getParam('affiliate'));
        }
    }
}    
