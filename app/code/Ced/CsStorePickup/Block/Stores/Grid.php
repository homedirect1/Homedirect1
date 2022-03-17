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
 * @category  Ced
 * @package   Ced_CsStorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsStorePickup\Block\Stores;

use Ced\StorePickup\Model\StoreInfoFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\DataObject;

/**
 * Class Grid
 * @package Ced\CsStorePickup\Block\Stores
 */
class Grid extends Extended
{
    /**
     * @var StoreFactory
     */
    protected $storesFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Grid constructor.
     * @param Context $context
     * @param Data $backendHelper
     * @param StoreInfoFactory $storesFactory
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        StoreInfoFactory $storesFactory,
        Session $customerSession,
        array $data = []
    )
    {
        $this->storesFactory = $storesFactory;
        $this->session = $customerSession;
        parent::__construct($context, $backendHelper, $data);
        $this->setData('area', 'adminhtml');
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('pickup_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this|Extended
     */
    protected function _prepareCollection()
    {
        $vendor_id = $this->session->getVendorId();
        $collection = $this->storesFactory->create()->getCollection()
            ->addFieldToFilter('vendor_id', $vendor_id);

        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'pickup_id',
            [
                'header' => __('Store Id'),
                'index' => 'pickup_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'store_name',
            [
                'header' => __('Store Name'),
                'index' => 'store_name',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'store_manager_name',
            [
                'header' => __('Store Manager Name'),
                'index' => 'store_manager_name',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'store_manager_email',
            [
                'header' => __('Store Manager Email'),
                'index' => 'store_manager_email',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );


        $this->addColumn(
            'is_active',
            [
                'header' => __('Status'),
                'index' => 'is_active',
                'type' => 'options',
                'options' => $this->getStates(),
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getPickupId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => 'csstorepickup/storepickup/edit'
                        ],
                        'field' => 'pickup_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return array
     */
    public static function getStates()
    {
        $_states = array(
            '1' => __('Enabled'),
            '0' => __('Disabled'),
        );

        return $_states;
    }

    /**
     * @return Extended|void
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * @param $collection
     * @param DataObject $column
     */
    protected function _filterStoreCondition($collection, DataObject $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * @param Product|DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return 'javascript:void(0);';
        return $this->getUrl(
            '*/*/edit',
            ['blogpost_id' => $row->getPickupId()]
        );
    }


    /**
     * Prepare grid filter buttons
     *
     * @return void
     */
    protected function _prepareFilterButtons()
    {
        $this->setChild(
            'reset_filter_button',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Button'
            )->setData(
                [
                    'label' => __('Reset Filter'),
                    'onclick' => $this->getJsObjectName() . '.resetFilter()',
                    'class' => 'action-reset action-tertiary',
                    'area' => 'adminhtml'
                ]
            )->setDataAttribute(['action' => 'grid-filter-reset'])
        );
        $this->setChild(
            'search_button',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Button'
            )->setData(
                [
                    'label' => __('Search'),
                    'onclick' => $this->getJsObjectName() . '.doFilter()',
                    'class' => 'action-secondary',
                    'area' => 'adminhtml'
                ]
            )->setDataAttribute(['action' => 'grid-filter-apply'])
        );
    }
}