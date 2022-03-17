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
 * Class Orders
 * @package Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab
 */
class Orders extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory
     */
    protected $comissionCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Orders constructor.
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->comissionCollectionFactory = $comissionCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
        $this->_storeManager = $context->getStoreManager();
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('orderGrid');
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
        $collection = $this->comissionCollectionFactory->create()
            ->addFieldToFilter('affiliate_id',
                $this->_coreRegistry->registry('current_account')->getAffiliateId());
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

        $this->addColumn('increment_id', array(
            'header' => __('Order Id'),
            'index' => 'increment_id',
            'align' => 'left',
            'width' => '50px',
            'renderer' => 'Ced\Affiliate\Block\Adminhtml\Account\Edit\Tab\Renderer\Order',
        ));

        $this->addColumn('customer_name', array(
            'header' => __('Customer Name'),
            'index' => 'customer_name',
            'align' => 'left',
            'width' => '50px'
        ));
        $this->addColumn('customer_email', array(
            'header' => __('Customer Email'),
            'index' => 'customer_email',
            'align' => 'left',
            'width' => '50px'
        ));

        $this->addColumn('user_type', array(
            'header' => __('Customer Type'),
            'index' => 'user_type',
            'align' => 'left',
            'width' => '50px'
        ));

        $this->addColumn('comission', array(
            'header' => __('Commission'),
            'index' => 'comission',
            'type' => 'price',
            'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
            'align' => 'left',
            'width' => '50px',
        ));

        $this->addColumn('comission_mode', array(
            'header' => __('Commission Type'),
            'index' => 'comission_mode',
            'align' => 'left',
            'width' => '50px',
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
        return $this->getUrl('*/*/ordergrid', array('_secure' => true, '_current' => true,
            'id' => $this->getRequest()->getParam('id')));
    }

}