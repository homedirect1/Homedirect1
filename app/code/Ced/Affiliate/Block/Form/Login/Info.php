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
namespace Ced\Affiliate\Block\Form\Login;

/**
 * Customer login info block
 */
class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * Checkout data
     *
     * @var \Magento\Checkout\Helper\Data
     */
    protected $checkoutData;

    /**
     * Core url
     *
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $coreUrl;

    /**
     * Registration
     *
     * @var \Magento\Customer\Model\Registration
     */
    protected $registration;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Registration $registration
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Checkout\Helper\Data $checkoutData
     * @param \Magento\Framework\Url\Helper\Data $coreUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Registration $registration,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Checkout\Helper\Data $checkoutData,
        \Magento\Framework\Url\Helper\Data $coreUrl,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registration = $registration;
        $this->_customerUrl = $customerUrl;
        $this->checkoutData = $checkoutData;
        $this->coreUrl = $coreUrl;
    }

    /**
     * Return registration
     *
     * @return \Magento\Customer\Model\Registration
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * Retrieve create new account url
     *
     * @return string
     */
    public function getCreateAccountUrl()
    {
       return $this->getUrl('affiliate/account/create');
    }
}
