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
 * @package     Ced_CsPromotions
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsPromotions\Block\Adminhtml\Promo\Widget\Chooser;

use Magento\Backend\Block\Widget\Grid\Column;

/**
 * Class Sku
 * @package Ced\CsPromotions\Block\Adminhtml\Promo\Widget\Chooser
 */
class Sku extends \Magento\CatalogRule\Block\Adminhtml\Promo\Widget\Chooser\Sku
{
    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_catalogType;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_cpCollection;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_cpCollectionInstance;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     */
    protected $_eavAttSetCollection;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * Sku constructor.
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $eavAttSetCollection
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $cpCollection
     * @param \Magento\Catalog\Model\Product\Type $catalogType
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $eavAttSetCollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $cpCollection,
        \Magento\Catalog\Model\Product\Type $catalogType,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    )
    {
        $this->_catalogType = $catalogType;
        $this->_cpCollection = $cpCollection;
        $this->_eavAttSetCollection = $eavAttSetCollection;
        $this->session = $session;
        $this->_resource = $resource;
        $this->setArea('adminhtml');
        parent::__construct($context, $backendHelper, $eavAttSetCollection, $cpCollection, $catalogType, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setArea('adminhtml');

        if ($this->getRequest()->getParam('current_grid_id')) {
            $this->setId($this->getRequest()->getParam('current_grid_id'));
        } else {
            $this->setId('skuChooserGrid_' . $this->getId());
        }

        $form = $this->getJsFormObject();
        $this->setRowClickCallback("{$form}.chooserGridRowClick.bind({$form})");
        $this->setCheckboxCheckCallback("{$form}.chooserGridCheckboxCheck.bind({$form})");
        $this->setRowInitCallback("{$form}.chooserGridRowInit.bind({$form})");
        $this->setDefaultSort('sku');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $selected = $this->_getSelectedProducts();
            if (empty($selected)) {
                $selected = '';
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('sku', ['in' => $selected]);
            } else {
                $this->getCollection()->addFieldToFilter('sku', ['nin' => $selected]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare Catalog Product Collection for attribute SKU in Promo Conditions SKU chooser
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_getCpCollectionInstance()->setStoreId(
            0
        )->addAttributeToSelect(
            'name',
            'type_id',
            'attribute_set_id'
        );

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Get catalog product resource collection instance
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getCpCollectionInstance()
    {
        if (!$this->_cpCollectionInstance) {
            $vendor_id = $this->session->getVendor()['entity_id'];
            $this->_cpCollectionInstance = $this->_cpCollection->create();
            $vproducts_table = $this->_resource->getTableName('ced_csmarketplace_vendor_products');
            $this->_cpCollectionInstance
                ->getSelect()
                ->join(
                    array('vendor_table' => $vproducts_table),
                    'entity_id = vendor_table.product_id',
                    array('entity_id' => 'e.entity_id',
                        'vendor_id' => 'vendor_table.vendor_id')
                )->where("vendor_table.vendor_id='" . $vendor_id . "'");
        }
        return $this->_cpCollectionInstance;
    }

    /**
     * Define Cooser Grid Columns and filters
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_products',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_products',
                'values' => $this->_getSelectedProducts(),
                'align' => 'center',
                'index' => 'sku',
                'use_index' => true
            ]
        );

        $this->addColumn(
            'entity_id',
            ['header' => __('ID'), 'sortable' => true, 'width' => '60px', 'index' => 'entity_id']
        );

        $this->addColumn(
            'type',
            [
                'header' => __('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type' => 'options',
                'options' => $this->_catalogType->getOptionArray()
            ]
        );

        $sets = $this->_eavAttSetCollection->create()->setEntityTypeFilter(
            $this->_getCpCollectionInstance()->getEntity()->getTypeId()
        )->load()->toOptionHash();

        $this->addColumn(
            'set_name',
            [
                'header' => __('Attribute Set'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type' => 'options',
                'options' => $sets
            ]
        );

        $this->addColumn(
            'chooser_sku',
            ['header' => __('SKU'), 'name' => 'chooser_sku', 'width' => '80px', 'index' => 'sku']
        );
        $this->addColumn(
            'chooser_name',
            ['header' => __('Product'), 'name' => 'chooser_name', 'index' => 'name']
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'cspromotions/*/chooser',
            ['_current' => true, 'current_grid_id' => $this->getId(), 'collapse' => null]
        );
    }

    /**
     * @return mixed
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected', []);

        return $products;
    }
}
