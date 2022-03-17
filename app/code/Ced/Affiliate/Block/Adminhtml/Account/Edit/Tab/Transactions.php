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
 * Class Transactions
 * @package Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab
 */
class Transactions extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateTransaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Transactions constructor.
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateTransaction\CollectionFactory $transactionCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\ResourceModel\AffiliateTransaction\CollectionFactory $transactionCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
        $this->_storeManager = $context->getStoreManager();
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('transactionsGrid');
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
        return $this->_storeManager->getStore($this->_storeManager->getStore()->getId());
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->transactionCollectionFactory->create()
            ->addFieldToFilter('affiliate_id', $this->_coreRegistry->registry('current_account')
                ->getAffiliateId());
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

        $this->addColumn('transaction_id', array(
            'header' => __('Transaction Id'),
            'index' => 'transaction_id',
            'align' => 'left',
            'width' => '50px'
        ));

        $this->addColumn('amount_paid', array(
            'header' => __('Amount Paid'),
            'index' => 'amount_paid',
            'type' => 'price',
            'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
            'align' => 'left',
            'width' => '50px'
        ));
        $this->addColumn('payment_mode', array(
            'header' => __('Paid Through'),
            'index' => 'payment_mode',
            'align' => 'left',
            'width' => '50px'
        ));

        $this->addColumn('created_at', array(
            'header' => __('Date Paid'),
            'index' => 'created_at',
            'align' => 'left',
            'width' => '50px'
        ));

        $this->addColumn('status', array(
            'header' => __('Status'),
            'index' => 'status',
            'align' => 'left',
            'width' => '50px',
        ));


        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/transactiongrid', array('_secure' => true, '_current' => true));
    }
}