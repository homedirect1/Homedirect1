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

namespace Ced\Affiliate\Block\Referral\Summary;

use Magento\Framework\View\Element\Template\Context;

/**
 * Class Lists
 * @package Ced\Affiliate\Block\Referral\Summary
 */
class Lists extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_getSession;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\Transaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * Lists constructor.
     * @param \Ced\Affiliate\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Ced\Affiliate\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory,
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        Context $context,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->_getSession = $customerSession;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->affiliateHelper = $affiliateHelper;
        parent::__construct($context);
    }

    public function _construct()
    {

        $customer = $this->_getSession->getCustomer();
        $customer_Id = $customer->getId();
        $customerModel = $this->transactionCollectionFactory->create()->addFieldtoFilter('customer_id', [
            'customer_id' => $customer_Id
        ]);
        $this->setCollection($customerModel);
    }

    /**
     * Prepare Pager Layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCollection()) {
            $pager = $this->getLayout()
                ->createBlock('Magento\Theme\Block\Html\Pager', 'my.custom.pager')
                ->setLimit(5)->setCollection($this->getCollection());
            $this->setChild('pager', $pager);
        }
        $this->pageConfig->getTitle()->set("Transaction Summary");
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
     * @param $amount
     * @return mixed
     */
    public function getFormattedPrice($amount)
    {
        return $this->affiliateHelper->getFormattedPrice($amount);
    }
}