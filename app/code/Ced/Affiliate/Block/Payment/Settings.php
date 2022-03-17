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

namespace Ced\Affiliate\Block\Payment;

/**
 * Class Settings
 * @package Ced\Affiliate\Block\Payment
 */
class Settings extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory
     */
    protected $comissionCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Settings constructor.
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    )
    {
        $this->_customerSession = $customerSession;
        $this->comissionCollectionFactory = $comissionCollectionFactory;
        parent::__construct($context, $data);


    }

    /**
     * @return mixed
     */
    public function getComisssion()
    {
        return $this->comissionCollectionFactory->create();
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->_customerSession->getCustomer()->getId();
    }
}