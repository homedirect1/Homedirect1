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

namespace Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab;

/**
 * Class Withdrawl
 * @package Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab
 */
class Withdrawl extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory
     */
    protected $withdrawlCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Withdrawl constructor.
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory $withdrawlCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory $withdrawlCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->withdrawlCollectionFactory = $withdrawlCollectionFactory;
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('withdrawlGrid');
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
        $collection = $this->withdrawlCollectionFactory->create()
            ->addFieldToFilter('affiliate_id',
                $this->_coreRegistry->registry('current_account')->getAffiliateId());
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

        $this->addColumn('created_at', array(
            'header' => __('Requested Date'),
            'index' => 'created_at',
            'align' => 'left',
            'width' => '50px'
        ));

        $this->addColumn('request_amount', array(
            'header' => __('Requested Amount'),
            'index' => 'request_amount',
            'type' => 'price',
            'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
            'align' => 'left',
            'width' => '50px'
        ));
        $this->addColumn('service_tax', array(
            'header' => __('Service Tax'),
            'index' => 'service_tax',
            'type' => 'price',
            'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
            'align' => 'left',
            'width' => '50px'
        ));

        $this->addColumn('service_tax_mode', array(
            'header' => __('Tax Type'),
            'index' => 'service_tax_mode',
            'align' => 'left',
            'width' => '50px'
        ));

        $this->addColumn('payable_amount', array(
            'header' => __('Net Payable Amount'),
            'index' => 'payable_amount',
            'type' => 'price',
            'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
            'align' => 'left',
            'width' => '50px'
        ));

        $this->addColumn('status', array(
            'header' => __('Status'),
            'index' => 'status',
            'align' => 'left',
            'width' => '50px',
            'renderer' => '\Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab\Renderer\Status'
        ));

        return parent::_prepareColumns();
    }


    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/withdrawlgrid', array('_secure' => true, '_current' => true));
    }
}