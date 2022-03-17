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
 * @package     Ced_DeliveryDate
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\DeliveryDate\Block\Checkout\MiniCart;

use Magento\Framework\View\Element\Template;

/**
 * Class Summary
 * @package Ced\DeliveryDate\Block\Checkout\MiniCart
 */
class Summary extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Summary constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        Template\Context $context,
        array $data = []
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deliveryDateData()
    {
        $quoteId = $this->checkoutSession->getQuoteId();
        $quote = $this->quoteRepository->getActive($quoteId);
        $data['deliveryDate'] = $quote->getData('cedDeliveryDate');
        $data['deliveryComment'] = $quote->getData('cedDeliveryComment');
        $data['timestamp'] = $quote->getData('cedTimestamp');
        return $data;
    }

}