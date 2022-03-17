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
 * @package     Ced_Rewardsystem
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Rewardsystem\Block\Reward;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Received
 * @package Ced\Rewardsystem\Block\Reward
 */
class Received extends Template
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint\CollectionFactory
     */
    protected $customCollection;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * Received constructor.
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint\CollectionFactory $pointCollectionFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint\CollectionFactory $pointCollectionFactory
    ) {
        $this->productFactory = $productFactory;
        $this->productResource = $productResource;
        $this->date = $date;
        $this->_customerSession = $customerSession;
        $this->customCollection = $pointCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return $this|Received
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getReceivedCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'custom.history.pager'
            )->setAvailableLimit([5 => 5, 10 => 10, 15 => 15, 20 => 20])
                ->setShowPerPage(true)->setCollection($this->getReceivedCollection());
            $this->setChild('pager', $pager);
            $this->getReceivedCollection()->load();
        }
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
     * @return \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint\Collection
     */
    public function getReceivedCollection()
    {
        $date = $this->date->gmtDate();
        $curdate = date("Y-m-d h:i:s", strtotime($date));

        $customerId = $this->_customerSession->getCustomerId();
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;

        $collection = $this->customCollection->create()
            ->addFieldToFilter('main_table.customer_id', $customerId)
            ->addFieldToFilter('main_table.status', 'complete')
            ->addFieldToFilter('point', ['gt' => 0])
            ->addFieldToFilter(
                ['main_table.expiration_date','main_table.expiration_date'],
                [
                    ['null'=>true],
                    ['gteq'=>$curdate]
                ]
            );
        $salesOrderTable = $collection->getTable('sales_order');
        $reviewTable = $collection->getTable('review');
        $collection->getSelect()->joinLeft(
            ['order_table'=>$salesOrderTable],
            "main_table.order_id = order_table.entity_id",
            [
                'order_increment_id'=>'order_table.increment_id'
            ]
        );
        $collection->getSelect()->joinLeft(
            ['review_table'=>$reviewTable],
            "main_table.review_id = review_table.review_id",
            [
                'product_id'=>'review_table.entity_pk_value'
            ]
        );
        $collection->setPageSize($pageSize);
        $coll = clone $collection;
        $collection->setCurPage($page);
        if (count($collection)==0) {
            return $coll;
        }
        return $collection;
    }

    /**
     * @param $orderId
     * @return string
     */
    public function getViewOrderUrl($orderId)
    {
        return $this->getUrl('sales/order/view/', ['order_id' => $orderId]);
    }

    /**
     * @param $productId
     * @return array
     */
    public function getProduct($productId)
    {
        $product = $this->productFactory->create();
        $this->productResource->load($product, $productId);
        if ($product->getId()) {
            return  [
               'name' =>   $product->getName(),
               'url' =>$this->getUrl('catalog/product/view/', ['id' => $productId])
           ];
        } else {
            return  ['name' =>  '', 'url' =>''];
        }
    }
}
