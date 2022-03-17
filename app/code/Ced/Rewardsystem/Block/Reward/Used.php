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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Rewardsystem\Block\Reward;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Used
 * @package Ced\Rewardsystem\Block\Reward
 */
class Used extends Template
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
    protected $pointCollectionFactory;

    /**
     * Used constructor.
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint\CollectionFactory $pointCollectionFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint\CollectionFactory $pointCollectionFactory
    ) {
        $this->date = $date;
        $this->_customerSession = $customerSession;
        $this->pointCollectionFactory = $pointCollectionFactory;
        parent::__construct($context);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getUsedPointCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'used.point.pager'
            )->setAvailableLimit([5 => 5, 10 => 10, 15 => 15, 20 => 20])
                ->setShowPerPage(true)->setCollection($this->getUsedPointCollection());
            $this->setChild('pager', $pager);
            $this->getUsedPointCollection()->load();
        }
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getUsedPointCollection()
    {
        $customerId = $this->_customerSession->getCustomerId();
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;

        $collection = $this->pointCollectionFactory->create()
            ->addFieldToFilter('main_table.customer_id', $customerId)
            ->addFieldToFilter('point_used', ['gt' => 0]);
        $salesOrderTable = $collection->getTable('sales_order');
        $collection->getSelect()->joinLeft(
            ['order_table'=>$salesOrderTable],
            "main_table.order_id = order_table.entity_id",
            [
                'order_increment_id'=>'order_table.increment_id'
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

    public function getViewOrderUrl($orderId)
    {
        return $this->getUrl('sales/order/view/', ['order_id' => $orderId]);
    }
}
