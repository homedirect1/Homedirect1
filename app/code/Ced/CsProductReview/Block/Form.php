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
 * @package     Ced_CsProductReview
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsProductReview\Block;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Context;
use Magento\Customer\Model\Url;
use Magento\Review\Model\ResourceModel\Rating\Collection as RatingCollection;

/**
 * Review form block
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Form extends \Magento\Review\Block\Form
{
    /**
     * Review data
     *
     * @var \Magento\Review\Helper\Data
     */
    protected $_reviewData = null;

    /**
     * Catalog product model
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Rating model
     *
     * @var \Magento\Review\Model\RatingFactory
     */
    protected $_ratingFactory;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * Message manager interface
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;

    /**
     * @var array
     */
    protected $jsLayout;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Review\Helper\Data $reviewData
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Ced\CsMarketplace\Model\Vproducts $vproducts
     * @param \Ced\CsProductReview\Model\Review $vendorReview
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Review\Helper\Data $reviewData,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Url $customerUrl,
        array $data = [],
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        \Ced\CsMarketplace\Model\Vproducts $vproducts,
        \Ced\CsProductReview\Model\Review $vendorReview
    ) {
        $this->_vproducts = $vproducts;
        $this->_vendorReview = $vendorReview;
        parent::__construct($context, $urlEncoder, $reviewData, $productRepository, $ratingFactory, $messageManager, $httpContext, $customerUrl, $data, $serializer);
    }

    /**
     * Get collection of ratings
     *
     * @return RatingCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRatings()
    {
        if(
            !$this->_scopeConfig->getValue('ced_csmarketplace/general/csproductreview',\Magento\Store\Model\ScopeInterface::SCOPE_STORE) || !$this->getVendorId()
        ){
            return $this->_ratingFactory->create()->getResourceCollection()->addEntityFilter(
                'product'
            )->setPositionOrder()->addRatingPerStoreName(
                $this->_storeManager->getStore()->getId()
            )->setStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->setActiveFilter(
                true
            )->load()->addOptionToItems();
        }else{
            $storeId = $this->_storeManager->getStore()->getId();
            $vendorId = $this->getVenddorId();
            $ratingmodel = $this->_vendorReview
                ->getCollection()
                ->addFieldToFilter('store_id',$storeId)
                ->addFieldtoFilter('vendor_id',$vendorId)
                ->addFieldToSelect('rating_id');

            if($ratingmodel->count()){

                $ratingids= [];
                foreach ($ratingmodel as $_rating){
                    $ratingids[] = $_rating->getRatingId();
                }

                return  $this->_ratingFactory->create()->getResourceCollection()->addEntityFilter(
                                'product'
                        )->addFieldToFilter('rating_id',array('in'=>$ratingids))
                         ->setPositionOrder()->addRatingPerStoreName(
                            $this->_storeManager->getStore()->getId()
                        )->setStoreFilter(
                            $this->_storeManager->getStore()->getId()
                        )->setActiveFilter(
                            true
                        )->load()->addOptionToItems();
            }else{
                return $this->_ratingFactory->create()->getResourceCollection()->addEntityFilter(
                    'product'
                )->setPositionOrder()->addRatingPerStoreName(
                    $this->_storeManager->getStore()->getId()
                )->setStoreFilter(
                    $this->_storeManager->getStore()->getId()
                )->setActiveFilter(
                    true
                )->load()->addOptionToItems();
            }
        }
    }

    /**
     * 
     * @return bollean
     */
    public function getVendorId(){
    	return $this->_vproducts->getVendorIdByProduct($this->getProductId());
    }
}
