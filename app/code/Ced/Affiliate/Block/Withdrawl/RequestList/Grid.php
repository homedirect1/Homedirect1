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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Block\Withdrawl\RequestList;

/**
 * Class Grid
 * @package Ced\Affiliate\Block\Withdrawl\RequestList
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory
     */
    protected $affiliatewithdrawlCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Grid constructor.
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory $affiliatewithdrawlCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Customer\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory $affiliatewithdrawlCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Customer\Model\Session $session,
        array $data = []
    )
    {

        $this->session = $session;
        $this->_storeManager = $context->getStoreManager();
        $this->affiliatewithdrawlCollectionFactory = $affiliatewithdrawlCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
        $this->setData('area', 'adminhtml');
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('assignedGrid');
        $this->setDefaultSort('Asc');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

    }

    /**
     * @return $this
     */
    protected function _getStore()
    {
        return $this->_storeManager
            ->getStore($this->_storeManager->getStore()->getId());
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->affiliatewithdrawlCollectionFactory->create()
            ->addFieldToFilter('customer_id', $this->session->getCustomer()->getId());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {

        $this->addColumn('id', array(
            'header' => __('ID#'),
            'index' => 'id',
            'align' => 'left',
            'width' => '50px'
        ));

        $this->addColumn('created_at', array(
            'header' => __('Request Date'),
            'index' => 'created_at',
            'align' => 'left',
        ));

        $this->addColumn('request_amount', array(
            'header' => __('Amount Requested'),
            'index' => 'request_amount',
            'type' => 'price',
            'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
            'align' => 'left',
        ));

        $this->addColumn('service_tax', array(
            'header' => __('Service Tax'),
            'index' => 'service_tax',
            'type' => 'price',
            'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
            'align' => 'left',
        ));


        $this->addColumn('payable_amount', array(
            'header' => __('Net Payable Amount'),
            'index' => 'payable_amount',
            'type' => 'price',
            'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
            'align' => 'left',
        ));

        $this->addColumn('service_tax_mode', array(
            'header' => __('Service Tax Mode'),
            'index' => 'service_tax_mode',
            'align' => 'left',
        ));

        $this->addColumn('customer_email', array(
            'header' => __('Email'),
            'index' => 'customer_email',
            'align' => 'left',
        ));

        $this->addColumn('status', [
                'header' => __('Status'),
                'align' => 'left',
                'index' => 'status',
                'renderer' => 'Ced\Affiliate\Block\Withdrawl\Renderer\Status',
                'type' => 'options',
                'options' => array(0 => "Pending", 1 => "Complete", 2 => "Cancelled")
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

        return $this->getUrl('*/*/withdrawlgrid', array('_secure' => true, '_current' => true));
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