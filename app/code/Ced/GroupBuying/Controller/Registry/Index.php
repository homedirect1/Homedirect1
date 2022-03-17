<?php //@codingStandardsIgnoreStart

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
//@codingStandardsIgnoreEnd
namespace Ced\GroupBuying\Controller\Registry;

use Ced\GroupBuying\Model\Session;
use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\SessionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\Page;
use Magento\Quote\Api\CartRepositoryInterface;

class Index extends Action//@codingStandardsIgnoreLine
{

    /**
     * Group Buying custom session.
     *
     * @var Session
     */
    private $groupBuyingSession;

    /**
     * Get module data.
     *
     * @var RequestInterface
     */
    private $request;

    /**
     * Magento product repository.
     *
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * Magento Checkout session
     *
     * @var SessionFactory
     */
    private $checkoutSession;

    /**
     * Cart Repository.
     *
     * @var CartRepositoryInterface
     */
    private $cartRep;


    /**
     * Constructor
     *
     * @param ManagerInterface        $messageManager    Shows success/error message.
     * @param Session                 $session           Group Buying custom session.
     * @param RequestInterface        $request           Get module data.
     * @param ProductRepository       $productRepository Magento product repository.
     * @param ResultFactory           $resultFactory     Redirect.
     * @param SessionFactory          $checkoutSession   Magento checkout session.
     * @param CartRepositoryInterface $cartRep           Cart repository.
     */
    public function __construct(
        Context $context,
        Session $session,
        RequestInterface $request,
        ProductRepository $productRepository,
        SessionFactory $checkoutSession,
        CartRepositoryInterface $cartRep
    ) {
        $this->groupBuyingSession = $session;
        $this->request            = $request;
        $this->productRepository  = $productRepository;
        $this->checkoutSession    = $checkoutSession;
        $this->cartRep            = $cartRep;
        parent::__construct($context);

    }//end __construct()


    /**
     * Frontend group form index execute.
     *
     * @return       ResponseInterface|ResultInterface|Page
     * @throws       LocalizedException Local exception.
     * @throws       NoSuchEntityException If product not found.
     * @noinspection PhpUndefinedMethodInspection
     */
    public function execute()
    {
        $resultFactory = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT); //@codingStandardsIgnoreLine
        $params        = $this->request->getParams();
        if (isset($params['product']) === true) {
            $product = $this->productRepository->getById($params['product']);

            if (isset($params['qty']) === true) {
                $params['qty'] = 1;
            }

            if (isset($params['related_product']) === true) {
                $params['related_product'] = '';
            }

            $price = 0;

            if ((string) $product->getTypeId() !== 'grouped') {
                $session   = $this->checkoutSession->create();
                $quote     = $session->getQuote();
                $cartCount = $quote->getAllVisibleItems();
                foreach ($quote->getAllItems() as $item) {
                    if ((int) $item->getProduct()->getId() === (int) $params['product']) {
                        try {
                            $quote->removeItem($item->getId())->save();
                        } catch (Exception $e) {
                            $this->messageManager->addErrorMessage(__('1 - '.$e->getMessage()));
                        }
                    }
                }

                $quote->addProduct($product, $params);
                $this->cartRep->save($quote);
                foreach ($quote->getAllItems() as $key => $items) {
                    if ((int) $items->getProduct()->getId() === (int) $params['product']) {
                        $price = $items->getBaseRowTotalInclTax();
                        try {
                            $quote->removeItem($items->getId());
                            $this->cartRep->save($quote);
                        } catch (Exception $e) {
                            $this->messageManager->addErrorMessage(__('2 - '.$e->getMessage()));
                        }
                    }
                }

                if ($cartCount < 1) {
                    $quote->removeAllItems();
                    $this->cartRep->save($quote);
                }

                $this->groupBuyingSession->setProductPrice($price);
            }//end if

            if ((string) $product->getTypeId() === 'grouped') {
                foreach ($params['super_group'] as $key => $value) {
                    $childProduct = $this->productRepository->getById($key);
                    $childPrice   = ($childProduct->getFinalPrice() * $value);
                    $price       += $childPrice;
                }

                $this->groupBuyingSession->setProductPrice($price);
            }
        }//end if

        return $resultFactory;

    }//end execute()


}//end class
