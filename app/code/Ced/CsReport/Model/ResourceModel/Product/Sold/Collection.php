<?php

namespace Ced\CsReport\Model\ResourceModel\Product\Sold;

/**
 * Class Collection
 * @package Ced\CsReport\Model\ResourceModel\Product\Sold
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Collection
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $product;

    /**
     * Collection constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product $product
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot
     * @param \Magento\Framework\DB\Helper $coreResourceHelper
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product $product,
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\DB\Helper $coreResourceHelper,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    )
    {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $entitySnapshot,
            $coreResourceHelper,
            $connection,
            $resource
        );

        $this->product = $product;
    }

    /**
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function addOrderedQty($from = '', $to = '')
    {

        $connection = $this->getConnection();
        $orderTableAliasName = $connection->quoteIdentifier('order');

        $compositeTypeIds = $this->getCompositeTypes();

        $orderJoinCondition = [
            $orderTableAliasName . '.entity_id = order_items.order_id',
            $connection->quoteInto("{$orderTableAliasName}.state <> ?", \Magento\Sales\Model\Order::STATE_CANCELED),
        ];

        $productJoinCondition = array(
            $connection->quoteInto('(e.type_id NOT IN (?))', $compositeTypeIds),
            'e.entity_id = order_items.product_id',
            $connection->quoteInto('e.entity_type_id = ?', $this->getProductEntityTypeId())
        );
        if ($from != '' && $to != '') {
            $fieldName = $orderTableAliasName . '.created_at';
            $orderJoinCondition[] = $this->prepareBetweenSql($fieldName, $to, $from);
        }

        $this->getSelect()->reset()->from(
            ['order_items' => $this->getTable('sales_order_item')],
            [
                'ordered_qty' => 'SUM(order_items.qty_ordered)',
                'order_items_name' => 'order_items.name',
                'sku' => 'order_items.sku',
                'product_type' => 'order_items.product_type'
            ]
        )->joinInner(
            ['order' => $this->getTable('sales_order')],
            implode(' AND ', $orderJoinCondition),
            []
        )->where(
            'parent_item_id IS NULL'
        )->group(
            'order_items.product_id'
        )->having(
            'SUM(order_items.qty_ordered) > ?',
            0
        );

        return $this;
    }

    /**
     * @return string
     */
    public function getProductEntityTableName()
    {
        return $this->product->getEntityTable();
    }

    /**
     * @return array
     */
    public function getCompositeTypes()
    {
        return array('bundle', 'grouped', 'configurable');
    }

    /**
     * @return int
     */
    public function getProductEntityTypeId()
    {
        return $this->product->getTypeId();
    }

    /**
     * @param $fieldName
     * @param $from
     * @param $to
     * @return string
     */
    protected function prepareBetweenSql($fieldName, $from, $to)
    {
        return sprintf(
            '(%s BETWEEN %s AND %s)',
            $fieldName,
            $this->getConnection()->quote($from),
            $this->getConnection()->quote($to)
        );
    }
}