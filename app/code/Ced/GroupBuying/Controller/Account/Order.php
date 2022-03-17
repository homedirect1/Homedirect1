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

namespace Ced\GroupBuying\Controller\Account;

use Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\SessionFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\ResultFactory;

class Order extends Action
{

    protected $customerSession;

    protected $_coreRegistry = null;

    protected $_scopeConfig;

    private $productRep;
    private $cartRep;
    private $checkoutSession;
    private $customerFactory;
    private $_giftCollectionFactory;
    private $_lastCollectionFactory;
    private $request;


    /**
     * TODO
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param Registry $registry
     * @param Session $customerSession
     * @param CollectionFactory $giftCollectionFactory
     * @param \Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory $lastCollectionFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerFactory $customerFactory
     * @param SessionFactory $checkoutSession
     * @param CartRepositoryInterface $cartRep
     * @param ProductRepositoryInterface $productRep
     * @param ResultFactory $resultFactory
     * @param Http $request
     */
    public function __construct(
        Context                         $context,
        ObjectManagerInterface                                      $objectManager,
        Registry                                                    $registry,
        Session                                                     $customerSession,
        CollectionFactory                                           $giftCollectionFactory,
        \Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory $lastCollectionFactory,
        ScopeConfigInterface                                        $scopeConfig,
        CustomerFactory                                             $customerFactory,
        SessionFactory                                              $checkoutSession,
        CartRepositoryInterface                                     $cartRep,
        ProductRepositoryInterface $productRep,
        ResultFactory        $resultFactory,
        Http $request
    ) {
        $this->_objectManager    = $objectManager;
        $this->_coreRegistry     = $registry;
        $this->customerSession  = $customerSession;
        $this->_giftCollectionFactory = $giftCollectionFactory;
        $this->_lastCollectionFactory = $lastCollectionFactory;
        $this->_scopeConfig           = $scopeConfig;

        $this->customerFactory = $customerFactory;
        $this->checkoutSession = $checkoutSession;
        $this->cartRep         = $cartRep;
        $this->productRep      = $productRep;

        $this->resultFactory          = $resultFactory;
        $this->request = $request;
        parent::__construct($context);


    }//end __construct()


    /**
     * Action for reorder
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $id = $this->request->getParam('gift_id');

        $session = $this->checkoutSession->create();
        $session->setGid($id);
        $customerId = $this->customerSession->getCustomerId();
        $customer   = $this->customerFactory->create()->load($customerId);
        $guest      = $this->_giftCollectionFactory->create()->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'guest_email',
            $customer['email']
        )->addFieldToFilter(
            'groupgift_id',
            $id
        )->getFirstItem();
        $p_qty      = $guest->getData();
        $main       = $this->_lastCollectionFactory->create()->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'group_id',
            $id
        )->getFirstItem();
        $p_id       = $main->getData();
        $product    = $this->productRep->getById($p_id['original_product_id']);

        try {
            $quote = $session->getQuote();
            $quote->removeAllItems();
            $this->checkoutSession->create()->setIsGroupBuy(true);
            $quote->addProduct($product, $p_qty['quantity']);
            $this->cartRep->save($quote);
            $session->replaceQuote($quote)->unsLastRealOrderId();
        } catch (LocalizedException $e) {
            if ($this->checkoutSession->create()->getUseNotice(true)) {
                $this->messageManager->addNotice($e->getMessage());
            } else {
                $this->messageManager->addError($e->getMessage());
            }

            // return $resultRedirect->setPath('*/*/history');
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
            // return $resultRedirect->setPath('checkout/index/index');
        }

        return $redirect->setPath('checkout/cart/index');

    }//end execute()


}//end class
