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
 * @package   Ced_CsSubAccount
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Block\Customer;

use Ced\CsSubAccount\Model;
use Magento\Customer\Model\Session;

/**
 * Class Grid
 * @package Ced\CsSubAccount\Block\Customer
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Model\ResourceModel\CsSubAccount\CollectionFactory
     */
    protected $subaccountCollectionFactory;

    /**
     * @var Model\CsSubAccountFactory
     */
    protected $csSubAccountFactory;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vendor\CollectionFactory
     */
    protected $vendorCollectionFactory;

    /**
     * Grid constructor.
     * @param Model\ResourceModel\CsSubAccount\CollectionFactory $subaccountCollectionFactory
     * @param Model\CsSubAccountFactory $csSubAccountFactory
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vendor\CollectionFactory $vendorCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        Model\ResourceModel\CsSubAccount\CollectionFactory $subaccountCollectionFactory,
        Model\CsSubAccountFactory $csSubAccountFactory,
        \Ced\CsMarketplace\Model\ResourceModel\Vendor\CollectionFactory $vendorCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        Session $customerSession,
        array $data = []
    )
    {
        $this->subaccountCollectionFactory = $subaccountCollectionFactory;
        $this->csSubAccountFactory = $csSubAccountFactory;
        $this->vendorCollectionFactory = $vendorCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
        $this->setData('area', 'adminhtml');
        $this->session = $customerSession;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $vendor = $this->session->getVendorId();
        $collection = $this->subaccountCollectionFactory->create()->addFieldToFilter('parent_vendor', $vendor);
        $this->setCollection($collection);
        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'first_name',
            [
                'header' => __('First Name'),
                'type' => 'text',
                'index' => 'first_name',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',

            ]
        );

        $this->addColumn(
            'last_name',
            [
                'header' => __('Last Name'),
                'type' => 'text',
                'index' => 'last_name',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',

            ]
        );

        $this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'type' => 'text',
                'index' => 'email',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',

            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'align' => 'left',
                'index' => 'status',
                'type' => 'options',
                'options' => $this->csSubAccountFactory->create()->getOptionArray(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );

        return parent::_prepareColumns();
    }


    /**
     * After load collection
     *
     * @return void
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * Filter store condition
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \Magento\Framework\DataObject $column
     * @return                                        void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _filterStoreCondition($collection, \Magento\Framework\DataObject $column)
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
     * @param \SR\Weblog\Model\BlogPosts|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/view',
            ['id' => $row->getId()]
        );
    }

    /**
     * @param $collection
     * @param $column
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _vendornameFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $vendorIds = $this->vendorCollectionFactory->create()
            ->addAttributeToFilter('name', array('like' => '%' . $column->getFilter()->getValue() . '%'))
            ->getAllIds();

        if (count($vendorIds) > 0) {
            $this->getCollection()->addFieldToFilter('vendor_id', array('in', $vendorIds));
        } else {
            $this->getCollection()->addFieldToFilter('vendor_id');
        }
        return $this;
    }

    /**
     * @return $this|\Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setTemplate('Ced_CsSubAccount::product/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
            'approve',
            [
                'label' => __('Approve'),
                'url' => $this->getUrl('cssubaccount/customer/approve/'),
                'confirm' => __('Are you sure?')
            ]
        );

        $this->getMassactionBlock()->addItem(
            'disapprove',
            [
                'label' => __('Disapprove'),
                'url' => $this->getUrl('cssubaccount/customer/disapprove/'),
                'confirm' => __('Are you sure?')
            ]
        );

        $this->getMassactionBlock()->addItem(
            'deleteSubVendor',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('cssubaccount/customer/deletesubvendor/'),
                'confirm' => __('Are you sure?')
            ]
        );

        return $this;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
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
