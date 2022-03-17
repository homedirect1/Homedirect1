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
 * @package   Ced_CsOrder
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSla\Block\ListOrders;

use Magento\Customer\Model\Session;

/**
 * Class Grid
 * @package Ced\CsSla\Block\ListOrders
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Ced\CsMarketplace\Model\Vorders
     */
    protected $_vorders;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $_csMarketplaceHelper;

    /**
     * @var \Ced\CsOrder\Model\InvoiceFactory
     */
    protected $invoiceFactory;

    /**
     * @var \Ced\CsSla\Helper\Data
     */
    protected $slaHelper;

    /**
     * @var \Ced\CsMarketplace\Model\vendorFactory
     */
    protected $vendorFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Grid constructor.
     * @param \Ced\CsMarketplace\Model\Vorders $vorders
     * @param \Ced\CsMarketplace\Helper\Data $helperData
     * @param \Ced\CsOrder\Model\InvoiceFactory $invoiceFactory
     * @param \Ced\CsSla\Helper\Data $slaHelper
     * @param \Ced\CsMarketplace\Model\vendorFactory $vendorFactory
     * @param Session $customerSession
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Ced\CsMarketplace\Model\Vorders $vorders,
        \Ced\CsMarketplace\Helper\Data $helperData,
        \Ced\CsOrder\Model\InvoiceFactory $invoiceFactory,
        \Ced\CsSla\Helper\Data $slaHelper,
        \Ced\CsMarketplace\Model\vendorFactory $vendorFactory,
        Session $customerSession,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    )
    {
        parent::__construct($context, $backendHelper, $data);

        $this->_vorders = $vorders;
        $this->_csMarketplaceHelper = $helperData;
        $this->invoiceFactory = $invoiceFactory;
        $this->slaHelper = $slaHelper;
        $this->vendorFactory = $vendorFactory;
        $this->scopeConfig = $context->getScopeConfig();
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
        $this->setDefaultSort('created_at');
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
        $collection = $this->_vorders->getCollection()->addFieldToFilter('vendor_id', $vendor);

        $main_table = $this->_csMarketplaceHelper->getTableKey('main_table');
        $order_total = $this->_csMarketplaceHelper->getTableKey('order_total');
        $shop_commission_fee = $this->_csMarketplaceHelper->getTableKey('shop_commission_fee');
        $collection->getSelect()
            ->columns(array('net_vendor_earn' => new \Zend_Db_Expr("({$main_table}.{$order_total} - {$main_table}.{$shop_commission_fee})")));

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
            'id',
            [
                'header' => __('Id'),
                'type' => 'text',
                'index' => 'id',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display',
            ]
        );

        $this->addColumn(
            'real_order_id',
            [
                'header' => __('Order #'),
                'type' => 'text',
                'index' => 'order_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Purchased On'),
                'type' => 'text',
                'index' => 'created_at',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'filter_condition_callback' => array($this, '_createdAtFilter')
            ]
        );


        $this->addColumn(
            'billing_name',
            [
                'header' => __('Billing To Name'),
                'type' => 'text',
                'index' => 'billing_name',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',

            ]
        );

        $this->addColumn(
            'order_total',
            [
                'header' => __('G.T.'),
                'type' => 'currency',
                'index' => 'order_total',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'

            ]
        );

        $this->addColumn(
            'shop_commission_fee',
            [
                'header' => __('Commission Fee'),
                'type' => 'currency',
                'index' => 'shop_commission_fee',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'net_vendor_earn',
            [
                'header' => __('Net Earned'),
                'type' => 'currency',
                'index' => 'net_vendor_earn',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'filter_condition_callback' => array($this, '_vendorpaymentFilter')
            ]
        );

        $this->addColumn(
            'vendor_earn',
            [
                'header' => __('Vendor Payment'),
                'type' => 'currency',
                'index' => 'vendor_earn',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'renderer' => 'Ced\CsAdvTransaction\Block\Adminhtml\Vorders\Grid\Renderer\Vendorpayment'
            ]
        );

        $this->addColumn(
            'order_payment_state',
            [
                'header' => __('Order Payment<br /> Status'),
                'index' => 'order_payment_state',
                'type' => 'options',
                'options' => $this->invoiceFactory->create()->getStates(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );
        $this->addColumn(
            'payment_state',
            [
                'header' => __('Vendor Payment <br /> Status'),
                'type' => 'options',
                'options' => $this->_vorders->getStates(),
                'index' => 'payment_state',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',

            ]
        );
        $this->addColumn(
            'order_status',
            [
                'header' => __('Order Status'),
                'index' => 'order_status',
                'type' => 'options',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'options' => $this->slaHelper->getStates(),
            ]
        );

        $this->addColumn(
            'edit',
            [
                'header' => __('View'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('View'),
                        'url' => [
                            'base' => '*/*/view'
                        ],
                        'field' => 'order_id'
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
     * @param $collection
     * @param $column
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _vendorpaymentFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $main_table = $this->_csMarketplaceHelper->getTableKey('main_table');
        $order_total = $this->_csMarketplaceHelper->getTableKey('order_total');
        $shop_commission_fee = $this->_csMarketplaceHelper->getTableKey('shop_commission_fee');
        if (isset($value['from']) && $value['from']) {
            $collection->getSelect()->where("({$main_table}.{$order_total}- {$main_table}.{$shop_commission_fee}) >='" . $value['from'] . "'");
        }
        if (isset($value['to']) && $value['to']) {
            
            $collection->getSelect()->where("({$main_table}.{$order_total}- {$main_table}.{$shop_commission_fee}) <='" . $value['to'] . "'");
        } 
        return $collection;
    }
    
    /**
     * @param $collection
     * @param \Magento\Framework\DataObject $column
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _createdAtFilter($collection, \Magento\Framework\DataObject $column) {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        
        $creationData = $column->getFilter()->getValue();
        if ((isset($creationData['from']) && !empty($creationData['from'])) || (isset($creationData['to']) && !empty($creationData['to']))) {
            if ((isset($creationData['from']) && !empty($creationData['from'])) && (isset($creationData['to']) && !empty($creationData['to']))) {
                $fromDate = date('Y-m-d H:i:s', strtotime($creationData['from']->format('Y-m-d H:i:s')));
        
                $toDate = date('Y-m-d H:i:s', strtotime($creationData['to']->format('Y-m-d H:i:s')) + 86400);
                $this->getCollection()->addFieldToFilter('created_at', ['from' => $fromDate, 'to' => $toDate]);
            }elseif (isset($creationData['from']) && !empty($creationData['from'])){
                $fromDate = date('Y-m-d H:i:s', strtotime($creationData['from']->format('Y-m-d H:i:s')));
                $toDate = date('Y-m-d H:i:s');
                
                $this->getCollection()->addFieldToFilter('created_at', ['from' => $fromDate, 'to' => $toDate]);
            }else{
                $toDate = date('Y-m-d H:i:s', strtotime($creationData['to']->format('Y-m-d H:i:s')) + 86400);
    
                $this->getCollection()->addFieldToFilter('created_at', ['lteq' => $toDate]);
            }
        }
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
     * @return string
     */
    public function getRowUrl($row)
    {
        return 'javascript:void(0);';
        return $this->getUrl(
            '*/*/edit',
            ['blogpost_id' => $row->getId()]
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
        $vendorIds = $this->vendorFactory->create()->getCollection()
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


    /**
     * @return $this|\Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id'); 
        $this->getMassactionBlock()->setTemplate('Magento_Backend::widget/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('id');

        $slaenable = $this->scopeConfig->getValue('ced_csmarketplace/general/cssla');

        if ($slaenable) {
            $this->getMassactionBlock()->addItem(
                'confirmation',
                [
                    'label' => __('Confirm Order'),
                    'url' => $this->getUrl('cssla/*/massConfirm'),
                    'confirm' => __('Only pending Orders Can be Confirmed?')
                ]
            );

            $this->getMassactionBlock()->addItem(
                'cancel',
                [
                    'label' => __('Cancel Order'),
                    'url' => $this->getUrl('cssla/*/massCancel'),
                    'confirm' => __('Confirmed Orders can not be cancelled?')
                ]
            );
        }

        return $this;
    }

}
