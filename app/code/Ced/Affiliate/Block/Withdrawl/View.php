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

namespace Ced\Affiliate\Block\Withdrawl;

use Magento\Customer\Model\Session;

/**
 * Class View
 * @package Ced\Affiliate\Block\Withdrawl
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ced\Affiliate\Model\AffiliateWithdrawlFactory
     */
    protected $affiliateWithdrawlFactory;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * @var Session
     */
    public $_customerSession;

    /**
     * View constructor.
     * @param \Ced\Affiliate\Model\AffiliateWithdrawlFactory $affiliateWithdrawlFactory
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param Session $session
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\AffiliateWithdrawlFactory $affiliateWithdrawlFactory,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        $this->affiliateWithdrawlFactory = $affiliateWithdrawlFactory;
        $this->affiliateHelper = $affiliateHelper;
        $this->_customerSession = $session;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getWithdrawlInformation()
    {
        return $this->affiliateWithdrawlFactory->create()->load($this->getRequest()->getParam('id'));
    }

    /**
     * @return bool
     */
    public function checkCancel()
    {
        $cancelDays = $this->_scopeConfig->getValue('affiliate/withdrawl/cancel_days',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($cancelDays && $cancelDays > 0) {

            $timeStamp = time();
            $toDate = date('Y-m-d H:i:s', $timeStamp);
            $fromDate = date('Y-m-d H:i:s', $timeStamp + 86400 * $cancelDays);
            $model = $this->affiliateWithdrawlFactory->create()->load($this->getRequest()->getParam('id'));
            if ($model->getStatus() != '0')
                return false;

            $newDate = "+" . $cancelDays . " days";
            $date = $model->getCreatedAt();
            $timestamp = strtotime($newDate, strtotime($date));
            $days_after = date('Y-m-d H:i:s', strtotime($newDate, strtotime($date)));
            $toDate = date('Y-m-d H:i:s', time());
            $currentTimeStamp = strtotime($toDate);

            if ($currentTimeStamp <= $timestamp)
                return true;
            else
                return false;
        }
        return false;

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