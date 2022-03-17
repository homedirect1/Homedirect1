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
 * @package     Ced_CsDeliveryDate
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeliverydate\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SaveDeliveryDateToOrderObserver
 * @package Ced\CsDeliverydate\Observer
 */
class SaveDeliveryDateToOrderObserver implements ObserverInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * SaveDeliveryDateToOrderObserver constructor.
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     */
    public function __construct(\Magento\Quote\Model\QuoteRepository $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param EventObserver $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getOrder();

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->get($order->getQuoteId());
        $order->setData('cedDeliveryDate', $quote->getData('cedDeliveryDate'));
        $order->setData('cedDeliveryComment', $quote->getData('cedDeliveryComment'));
        $order->setData('cedTimestamp', $quote->getData('cedTimestamp'));
        try{
            $order->save();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to save Delivery Date on Quote'), $exception);

        }
        return $this;
    }

}