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

namespace Ced\CsAdvTransaction\Block\Adminhtml\Payments;

/**
 * Class Grid
 * @package Ced\CsAdvTransaction\Block\Adminhtml\Payments
 */
class Grid extends \Ced\CsMarketplace\Block\Adminhtml\Vpayments\Grid
{
    /**
     * @var \Ced\CsMarketplace\Model\VendorFactory
     */
    protected $vendorFactory;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Ced\CsMarketplace\Model\VpaymentFactory $vpaymentFactory
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ced\CsMarketplace\Model\VpaymentFactory $vpaymentFactory,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Ced\CsMarketplace\Model\Vpayment $vPaymentModel,
        array $data = []
    )
    {
        $this->vPaymentModel = $vPaymentModel;
        parent::__construct($vPaymentModel, $context, $backendHelper, $vpaymentFactory, $vendorFactory, $data);
    }

    /**
     * @return $this|\Ced\CsMarketplace\Block\Adminhtml\Vpayments\Grid
     */
    protected function _prepareCollection()
    {
        $vendor_id = $this->getRequest()->getParam('vendor_id', 0);
        $collection = $this->_vpaymentFactory->create()->getCollection();
        if ($vendor_id) {
            $collection->addFieldToFilter('vendor_id', array('eq' => $vendor_id));
        }
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

        $this->addColumn('created_at', array(
            'header' => __('Transaction Date'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('transaction_id', array(
            'header' => __('Transaction ID#'),
            'align' => 'left',
            'index' => 'transaction_id',
            'filter_index' => 'transaction_id',

        ));

        $this->addColumn('vendor_id', array(
            'header' => __('Vendor Name'),
            'align' => 'left',
            'index' => 'vendor_id',
            'renderer' => 'Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid\Renderer\Vendorname',
            'filter_condition_callback' => array($this, '_vendornameFilter'),
        ));


        $this->addColumn('transaction_type',
            array(
                'header' => __('Transaction Type'),
                'index' => 'transaction_type',
                'type' => 'options',
                'options' => $this->vPaymentModel->getStates(),
            ));

        $this->addColumn('base_amount',
            array(
                'header' => __('Amount'),
                'index' => 'base_amount',
                'type' => 'currency',
                'currency' => 'base_currency'
            ));


        $this->addColumn('base_fee',
            array(
                'header' => __('Adjustment Amount'),
                'index' => 'base_fee',
                'type' => 'currency',
                'currency' => 'base_currency'
            ));


        $this->addColumn('base_net_amount',
            array(
                'header' => __('Net Amount'),
                'index' => 'base_net_amount',
                'type' => 'currency',
                'currency' => 'base_currency'
            ));


        $this->addColumn('action',
            array(
                'header' => __('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => __('View'),
                        'url' => array('base' => 'csadvtransaction/pay/details'),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));

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
     * @param $collection
     * @param \Magento\Framework\DataObject $column
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _vendornameFilter($collection, \Magento\Framework\DataObject $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }

        $vendors = $this->vendorFactory->create()->getCollection()
            ->addAttributeToFilter('name', ['like' => $value . '%']);
        $vendor_id = array();
        foreach ($vendors as $_vendor) {
            $vendor_id[] = $_vendor->getId();
        }
        $this->getCollection()->addFieldToFilter('vendor_id', array('eq' => $vendor_id));
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
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setTemplate('Magento_Catalog::product/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );


        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/vpaymentsgrid', ['_current' => true]);
    }

}
