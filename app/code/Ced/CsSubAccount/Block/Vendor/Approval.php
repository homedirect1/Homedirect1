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
 * @package     Ced_CsSubAccount
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Block\Vendor;

use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Approval
 * @package Ced\CsSubAccount\Block\Vendor
 */
class Approval extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{

    /**
     * @var \Ced\CsMarketplace\Model\Url
     */
    public $_vendorUrl;

    /**
     * @var \Ced\CsMarketplace\Model\Session
     */
    protected $session;

    /**
     * @var \Ced\CsSubAccount\Model\CsSubAccountFactory
     */
    protected $csSubAccountFactory;

    /**
     * Approval constructor.
     * @param \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory
     * @param \Ced\CsMarketplace\Model\Url $vendorUrl
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param Context $context
     * @param \Ced\CsMarketplace\Model\Session $customerSession
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory,
        \Ced\CsMarketplace\Model\Url $vendorUrl,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        Context $context,
        \Ced\CsMarketplace\Model\Session $customerSession,
        UrlFactory $urlFactory
    )
    {
        $this->_vendorUrl = $vendorUrl;
        parent::__construct($vendorFactory, $customerFactory, $context, $customerSession, $urlFactory);
        $this->session = $customerSession;
        $this->csSubAccountFactory = $csSubAccountFactory;

    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_vendorUrl->getBaseUrl();
    }

    /**
     * Retrieve password forgotten url
     *
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->_vendorUrl->getLogoutUrl();
    }

    /**
     * Approval message
     *
     * @return String
     */
    public function getApprovalMessage()
    {
        $message = '';
        $message .= __('Your sub-vendor account is under approval.');
        return $message;
    }

    /**
     * @return string
     */
    public function getName()
    {
        $email = $this->session->getSubvendorEmail();
        $subvendor = $this->csSubAccountFactory->create()->load($email, 'email');
        return $subvendor->getFirstName() . ' ' . $subvendor->getLastName();
    }
}
