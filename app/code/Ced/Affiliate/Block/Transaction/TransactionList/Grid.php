<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Block\Transaction\TransactionList;

/**
 * Class Grid
 * @package Ced\Affiliate\Block\Transaction\TransactionList
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    protected $_gridFactory;

    protected $backendHelper;

    protected $_resource;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Ced\Affiliate\Model\AffiliateTransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Grid constructor.
     * @param \Ced\Affiliate\Model\AffiliateTransactionFactory $transactionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Customer\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\AffiliateTransactionFactory $transactionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Customer\Model\Session $session,
        array $data = []
    )
    {

        $this->session = $session;
        $this->transactionFactory = $transactionFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $backendHelper, $data);
        $this->setData('area', 'adminhtml');
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('transactionGrid');
        $this->setDefaultSort('Asc');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

    }

    /**
     * @return $this
     */

    protected function _prepareCollection()
    {
        $collection = $this->transactionFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $this->session->getCustomer()->getId());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getStore()
    {
        return $this->_storeManager->getStore($this->_storeManager->getStore()->getId());
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _prepareColumns()
    {


        $this->addColumn('id', array(
            'header' => __('ID#'),
            'index' => 'id',
            'align' => 'left',
            'width' => '50px'
        ));

        $this->addColumn('transaction_id', array(
            'header' => __('Transaction Id'),
            'index' => 'transaction_id',
            'align' => 'left',
            //'renderer' => 'Ced\CsPurchaseOrder\Block\Vendor\QuotationList\Renderer\Customer'
        ));

        $this->addColumn('payment_mode', array(
            'header' => __('Payment Mode'),
            'index' => 'payment_mode',
            'align' => 'left',
        ));

        $this->addColumn('service_tax', array(
            'header' => __('Service Tax'),
            'index' => 'service_tax',
            'type' => 'price',
            'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
            'align' => 'left',
        ));


        $this->addColumn('amount_paid', array(
            'header' => __('Amount Paid'),
            'index' => 'amount_paid',
            'type' => 'price',
            'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
            'align' => 'left',
        ));

        /* $this->addColumn('service_tax_mode', array(
                'header'    =>__('Service Tax Mode'),
                'index'     =>'service_tax_mode',
                'align'     => 'left',
        )); */

        $this->addColumn('customer_email', array(
            'header' => __('Email'),
            'index' => 'customer_email',
            'align' => 'left',
        ));


        $this->addColumn('status', [
                'header' => __('Status'),
                'align' => 'left',
                'index' => 'status',
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
                            'base' => '*/*/view',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'id'
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
     * @return string
     */
    public function getGridUrl()
    {

        return $this->getUrl('*/*/transactiongrid', array('_secure' => true, '_current' => true));
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