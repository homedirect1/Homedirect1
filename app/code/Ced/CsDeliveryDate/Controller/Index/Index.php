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
namespace Ced\CsDeliveryDate\Controller\Index;

use Magento\Framework\App\Action\Context;

/**
 * Class Index
 * @package Ced\CsDeliveryDate\Controller\Index
 */
class Index extends \Magento\Framework\App\Action\Action
{
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
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param Context $context
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Context $context
    )
    {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository = $cartRepository;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return false|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $product = [];
        if (true) {
            $checkoutSession = $this->checkoutSession;
            $quoteRepository = $this->cartRepository;
            $quoteId = $checkoutSession->getQuoteId();
            $quote = $quoteRepository->getActive($quoteId);
            foreach($quote->getAllItems() as $key=>$item) {
                if($item->getProductType() == 'simple'){
                    $product[$key]['cs_timestamp'] = $item->getCsTimestamp();
                    $product[$key]['cs_deliverydate'] = $item->getCsDeliverydate();
                    $product[$key]['name'] = $item->getName();
                }
            }
        }
        return $resultJson->setData($product);
    }

}