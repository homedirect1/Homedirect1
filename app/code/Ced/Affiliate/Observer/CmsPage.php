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

namespace Ced\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class CmsPage
 * @package Ced\Affiliate\Observer
 */
Class CmsPage implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * CmsPage constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        $this->_request = $request;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     *Product Assignment Tab
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_request->getParam('affiliate')):
            $this->checkoutSession->setAffiliateId($this->_request->getParam('affiliate'));
        endif;

    }
}    

