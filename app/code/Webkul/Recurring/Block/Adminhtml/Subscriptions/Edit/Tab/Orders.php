<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Block\Adminhtml\Subscriptions\Edit\Tab;

/**
 * Adminhtml Orders grid block
 *
 * @api
 * @since 100.0.2
 */
class Orders extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Webkul\Recurring\Model\Mapping
     */
    protected $mapping;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory
     * @param \Webkul\Recurring\Model\Mapping $mapping,
     * @param \Magento\Sales\Helper\Reorder $salesReorder
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory,
        \Webkul\Recurring\Model\Mapping $mapping,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry         = $coreRegistry;
        $this->mapping              = $mapping;
        $this->collectionFactory    = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('subscriptions_orders_grid');
        $this->setDefaultSort('created_at', 'desc');
        $this->setUseAjax(true);
    }

    /**
     * Apply various selection filters to prepare the sales order grid collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $id = $this->coreRegistry->registry('subscription_id');
        
        $collection = $this->mapping->getCollection()
        ->addFieldToFilter(
            'subscription_id',
            $id
        );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('Id'),
                'width' => '100',
                'index' => 'entity_id'
            ]
        );
        
        $this->addColumn(
            'order_id',
            [
                'header' => __('Order Id'),
                'width' => '100',
                'index' => 'order_id',
                'renderer' => \Webkul\Recurring\Block\Adminhtml\Customer\OrderIncrementId::class
            ]
        );

        $this->addColumn(
            'created_at',
            ['header' => __('Created At'), 'index' => 'created_at', 'type' => 'datetime']
        );
        
        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getGridUrl()
    {
        return $this->getUrl('recurring/subscriptions/orders', ['_current' => true]);
    }
}
