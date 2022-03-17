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

namespace Ced\Affiliate\Block\Comission\ComissionList;

/**
 * Class Grid
 * @package Ced\Affiliate\Block\Comission\ComissionList
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory
     */
    protected $comissionCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Grid constructor.
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Customer\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Customer\Model\Session $session,
        array $data = []
    )
    {

        $this->session = $session;
        $this->comissionCollectionFactory = $comissionCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
        $this->_storeManager = $context->getStoreManager();
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
        return $this->_storeManager->getStore($this->_storeManager->getStore()->getId());
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->comissionCollectionFactory->create()
            ->addFieldToFilter('affiliate_id', $this->session->getAffiliateId());
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
            'header' => __('Increment Id#'),
            'index' => 'increment_id',
            'align' => 'left',
            'width' => '50px'
        ));

        $this->addColumn('customer_email', array(
            'header' => __('Customer Email'),
            'index' => 'customer_email',
            'align' => 'left',
        ));

        $this->addColumn('user_type', array(
            'header' => __('User Type'),
            'index' => 'user_type',
            'align' => 'left',
        ));

        $this->addColumn('create_at', array(
            'header' => __('Date'),
            'index' => 'create_at',
            'align' => 'left',
        ));

        $this->addColumn('comission', [
                'header' => __('Commission'),
                'align' => 'left',
                'type' => 'price',
                'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
                'index' => 'comission',
            ]
        );
        $this->addColumn('comission_mode', [
                'header' => __('Commission Mode'),
                'align' => 'left',
                'index' => 'comission_mode',
            ]
        );


        $this->addColumn('status', [
                'header' => __('Status'),
                'align' => 'left',
                'index' => 'status',
            ]
        );


        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {

        return $this->getUrl('affiliate/comission/comissiongrid', array('_secure' => true, '_current' => true));
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