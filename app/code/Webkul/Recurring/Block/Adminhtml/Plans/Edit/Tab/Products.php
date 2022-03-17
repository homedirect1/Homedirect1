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
namespace Webkul\Recurring\Block\Adminhtml\Plans\Edit\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Products extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     *
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\Product $products
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\Product $products,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->products         = $products;
        $this->coreRegistry     = $coreRegistry;
        $this->jsonEncoder     = $jsonEncoder;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('recurring_plans_products');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    /**
     * Add Column Filter To Collection
     *
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'entity_id') {
            $selectedIds = $this->_getSelectedPlans();
            
            if (empty($selectedIds)) {
                $selectedIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('id', ['in' => $selectedIds]);
            } else {
                if ($selectedIds) {
                    $this->getCollection()->addFieldToFilter('id', ['nin' => $selectedIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare collection
     *
     * @return Modified
     */
    protected function _prepareCollection()
    {
        $collection = $this->products->getCollection();
        $collection->addAttributeToSelect('name');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare form
     *
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'type' => 'checkbox',
                'name' => 'entity_id',
                'values' => $this->_getSelectedPlans(),
                'index' => 'id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Title'),
                'index' => 'name'
            ]
        );
        $this->addColumn(
            'type_id',
            [
                'header' => __('Type'),
                'index' => 'type_id'
            ]
        );
        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'index' => 'created_at'
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsGrid', ['_current' => true]);
    }

    /**
     * @return array|null
     */
    public function getGallery()
    {
        return $this->coreRegistry->registry('recurring_data');
    }

    /**
     * @return array|null
     */
    protected function _getSelectedPlans()
    {
        $plans = array_keys($this->getSelectedPlans());
        return $plans;
    }
    
    /**
     * @return int|null
     */
    protected function _getSelectedThumb()
    {
        return $this->getGallery()->getThumbnailShow();
    }
    
    /**
     * Get selected plans as json
     *
     * @return json
     */
    public function getSelectedPlansJson()
    {
        $jsonUsers = [];
        $images = array_keys($this->getSelectedPlans());
        foreach ($images as $key => $value) {
            $jsonUsers[$value] = 0;
        }
        return $this->jsonEncoder->encode((object)$jsonUsers);
    }
    /**
     * Get selected plans array
     *
     * @return array|null
     */
    public function getSelectedPlans()
    {
        $products = [];
        $pros = $this->coreRegistry->registry('recurring_data')->getProductIds();
        $pros = explode(",", $pros);
        foreach ($pros as $pro) {
            $products[$pro] = ['position' => $pro];
        }
        
        return [1 => ['position' => 1]];
    }

    /**
     * Get image ids
     *
     * @return array|null
     */
    public function getImageIds()
    {
        $imageIds = $this->getGallery()->getImageIds();
        return $imageIds;
    }

    /**
     * Get products from recurring data
     *
     * @return array|null
     */
    public function getPlansProducts()
    {
        $products = [];
        $pros = $this->coreRegistry->registry('recurring_data')->getProductIds();
        $pros = explode(",", $pros);
        foreach ($pros as $pro) {
            $products[$pro] = ['position' => $pro];
        }
        return $products;
    }
}
