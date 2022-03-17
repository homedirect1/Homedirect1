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
 * @package     Ced_CsAdvTransaction
 * @author     CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Block\Adminhtml\Order;

/**
 * Class Grid
 * @package Ced\CsAdvTransaction\Block\Adminhtml\Order
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Ced\CsMarketplace\Model\Vorders
     */
    protected $_vorders;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vendor\CollectionFactory
     */
    protected $vendorCollection;

    /**
     * Grid constructor.
     * @param \Ced\CsMarketplace\Model\Vorders $vorders
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vendor\CollectionFactory $vendorCollection
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Ced\CsMarketplace\Model\Vorders $vorders,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Ced\CsMarketplace\Model\ResourceModel\Vendor\CollectionFactory $vendorCollection,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    )
    {
        $this->_vorders = $vorders;
        $this->scopeConfig = $context->getScopeConfig();
        $this->dateTime = $dateTime;
        $this->vendorCollection = $vendorCollection;
        parent::__construct($context, $backendHelper, $data);
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
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $orderIds = $this->getVendorEligibleOrders();
        $vid = $this->getRequest()->getParam('vendor_id', 0);
        $collection = $this->_vorders->getCollection()
            ->addFieldToFilter('vendor_id', $vid)
            ->addFieldToFilter('order_id', ['in' => $orderIds]);
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return array
     */
    public function getVendorEligibleOrders()
    {
        $rmaDate = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/refund_policy');
        $paycycle = $this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_cycle');
        $completeCycle = $rmaDate + $paycycle;

        $date = $this->dateTime->gmtDate();
        $date = explode(' ', $date);

        $vid = $this->getRequest()->getParam('vendor_id', 0);
        $vorders = $this->_vorders->getCollection()
            ->addFieldToFilter('payment_state', ['nin' => [2, 5]])
            ->addFieldToFilter('order_payment_state', ['nin' => 1])
            ->addFieldToFilter('vendor_id', $vid)
            ->addFieldToFilter('vendor_earn', ['neq' => 0]);

        $orderIds = [];
        foreach ($vorders as $k => $v) {
            if (!$v->canInvoice() && !$v->canShip()) {

                $days = $completeCycle;

                $afterdate = strtotime("+" . $days . " days", strtotime($v->getCreatedAt()));
                $afterdate = date("Y-m-d", $afterdate);

                if ($date[0] >= $afterdate) {
                    $orderIds[] = $v->getOrderId();
                }
            }
        }
        return $orderIds;


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
                'type' => 'hidden',
                'index' => 'id',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display'
            ]
        );


        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'type' => 'date',
                'index' => 'created_at',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'order_id',
            [
                'header' => __('Order Id'),
                'type' => 'text',
                'index' => 'order_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'renderer' => 'Ced\CsAdvTransaction\Block\Adminhtml\Vorders\Grid\Renderer\Orderid',

            ]
        );

        $this->addColumn(
            'payment_state',
            [
                'header' => __('Order State'),
                'type' => 'options',
                'options' => $this->_vorders->getStates(),
                'index' => 'payment_state',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',

            ]
        );


        $this->addColumn(
            'order_payment_type',
            [
                'header' => __('Order Payment Mode'),
                'type' => 'options',
                'options' => ['PrePaid' => 'PrePaid', 'PostPaid' => 'PostPaid'],
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'renderer' => 'Ced\CsAdvTransaction\Block\Adminhtml\Vorders\Grid\Renderer\Paytype',

            ]
        );


        $this->addColumn(
            'order_total',
            [
                'header' => __('Order Total'),
                'type' => 'currency',
                'index' => 'order_total',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );


        $this->addColumn('shop_commission_fee', array(
            'header' => __('Commission Fee'),
            'index' => 'shop_commission_fee',
            'type' => 'currency',
            'currency' => 'currency',

        ));
        $this->addColumn(
            'postpaid_amount',
            [
                'header' => __('Paid Amount to Vendor'),
                'type' => 'currency',
                'index' => 'vendor_earn',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'renderer' => 'Ced\CsAdvTransaction\Block\Adminhtml\Vorders\Grid\Renderer\PostPaid'
            ]
        );
        if ($this->scopeConfig->getValue('ced_csmarketplace/vadvtransaction/pay_shipping')) {
            $this->addColumn(
                'vendor_shipping',
                [
                    'header' => __('Vendor Shipping'),
                    'type' => 'currency',
                    'header_css_class' => 'col-id',
                    'column_css_class' => 'col-id',
                    'renderer' => 'Ced\CsAdvTransaction\Block\Adminhtml\Vorders\Grid\Renderer\Shipping',
                ]
            );
        }


        $this->addColumn(
            'net_vendor_pay',
            [
                'header' => __('Payable Amount'),
                'type' => 'currency',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'renderer' => 'Ced\CsAdvTransaction\Block\Adminhtml\Vorders\Grid\Renderer\Vendorpay',
            ]
        );


        return parent::_prepareColumns();
    }

    /**
     * @return array
     */
    protected function _getSelectedOrders()
    {
        $params = $this->getRequest()->getParams();
        $orderIds = isset($params['order_ids']) ? explode(',', trim($params['order_ids'])) : array();
        return $orderIds;
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
     * @return void
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
        return $this->getUrl('csadvtransaction/pay/order', ['_current' => true, 'vendor_id' => $this->getRequest()->getParam('vendor_id')]);
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
        $vendorIds = $this->vendorCollection->create()
            ->addAttributeToFilter('name', array('like' => '%' . $column->getFilter()->getValue() . '%'))
            ->getAllIds();

        if (count($vendorIds) > 0)
            $this->getCollection()->addFieldToFilter('vendor_id', array('in', $vendorIds));
        else {
            $this->getCollection()->addFieldToFilter('vendor_id');
        }
        return $this;
    }

}
