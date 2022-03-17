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

namespace Ced\Affiliate\Block\Transaction;

use Magento\Customer\Model\Session;

/**
 * Class View
 * @package Ced\Affiliate\Block\Transaction
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ced\Affiliate\Model\AffiliateTransactionFactory
     */
    protected $transactionFactory;

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
     * @param \Ced\Affiliate\Model\AffiliateTransactionFactory $transactionFactory
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param Session $session
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\AffiliateTransactionFactory $transactionFactory,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        $this->affiliateHelper = $affiliateHelper;
        $this->transactionFactory = $transactionFactory;
        $this->_customerSession = $session;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getTransactionSummary()
    {

        return $this->transactionFactory->create()->load($this->getRequest()->getParam('id'));

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