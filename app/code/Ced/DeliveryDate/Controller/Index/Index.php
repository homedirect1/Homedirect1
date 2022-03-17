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

namespace Ced\DeliveryDate\Controller\Index;

use Magento\Framework\App\Action\Context;

/**
 * Class Index
 * @package Ced\DeliveryDate\Controller\Index
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Ced\DeliveryDate\Helper\ConfigData
     */
    protected $configDataHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Index constructor.
     * @param \Ced\DeliveryDate\Helper\ConfigData $configDataHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param Context $context
     */
    public function __construct(
        \Ced\DeliveryDate\Helper\ConfigData $configDataHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Context $context
    )
    {
        parent::__construct($context);
        $this->configDataHelper = $configDataHelper;
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository = $cartRepository;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $moduleEnabled = $this->configDataHelper;
        $data['enableModule'] = $moduleEnabled->moduleEnabled();
        if ($data['enableModule'] == 1) {
            $checkoutSession = $this->checkoutSession;
            $quoteRepository = $this->cartRepository;
            $quoteId = $checkoutSession->getQuoteId();
            $quote = $quoteRepository->getActive($quoteId);

            $data['dDate'] = $quote->getData('cedDeliveryDate');
            if ($data['dDate'] != '0000-00-00 00:00:00') {
                $da = strtotime($data['dDate']);
                $data['dDate'] = date("F j, Y", $da);
            } else {
                $data['dDate'] = 'No Date Selected';
            }
            $data['deliveryComment'] = $quote->getData('cedDeliveryComment');

            $data['timestamp'] = $quote->getData('cedTimestamp');
        }
        return $resultJson->setData($data);
    }
}