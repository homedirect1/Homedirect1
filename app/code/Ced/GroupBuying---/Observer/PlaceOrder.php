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
 * @package     Ced_GroupBuying
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GroupBuying\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class PlaceOrder implements ObserverInterface
{

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote
     */
    protected $_registry = null;


    /**
     * TODO
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory $giftCollectionFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory $giftCollectionFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_objectManager         = $objectManager;
        $this->_logger                = $logger;
        $this->_giftCollectionFactory = $giftCollectionFactory;
        $this->_checkoutSession       = $checkoutSession;
        $this->_registry              = $registry;
        $this->messageManager         = $messageManager;

    }//end __construct()


    /**
     * TODO
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_checkoutSession->getGid()) {
            $id         = $this->_checkoutSession->getGid();
            $customerid = $this->_objectManager->create('Magento\Customer\Model\Session')->getCustomerId();
            $customer   = $this->_objectManager->get('Magento\Customer\Model\Customer')->load($customerid);
            $guest_data = $this->_giftCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'groupgift_id',
                $id
            )->addFieldToFilter(
                'guest_email',
                $customer['email']
            )->getFirstItem();
            try {
                $guest_data->setData('request_approval', 5);
                $guest_data->setData('pay_status', 1);
                $guest_data->save();
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            $this->_checkoutSession->unsGid();
        }//end if

    }//end execute()


}//end class
