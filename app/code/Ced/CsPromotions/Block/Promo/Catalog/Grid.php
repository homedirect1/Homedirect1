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
 * @package     Ced_CsPromotions
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsPromotions\Block\Promo\Catalog;

use Magento\Customer\Model\Session;

/**
 * Class Grid
 * @package Ced\CsPromotions\Block\Promo\Catalog
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * @var \Magento\CatalogRule\Model\Rule
     */
    protected $catalogRule;

    /**
     * Grid constructor.
     * @param \Magento\CatalogRule\Model\Rule $catalogRule
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param Session $customerSession
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param array $data
     */
    public function __construct(
        \Magento\CatalogRule\Model\Rule $catalogRule,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        Session $customerSession,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        array $data = []
    )
    {
        $this->session = $customerSession;
        $this->_websiteFactory = $websiteFactory;
        $this->catalogRule = $catalogRule;
        parent::__construct($context, $backendHelper, $data);
        $this->setData('area', 'adminhtml');
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('vendorcatalogrulegrid');
        $this->setDefaultSort('rule_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this|\Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $vendor_id = $this->session->getVendorId();
        $collection = $this->catalogRule
            ->getCollection();
        $collection->addWebsitesToResult()
            ->addFieldToFilter('vendor_id', $vendor_id);
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {


        $this->addColumn('rule_id', [
                'header' => __('ID'),
                'index' => 'rule_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn('name', [
                'header' => __('Rule'),
                'index' => 'name',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn('from_date', [
                'header' => __('Start'),
                'align' => 'right',
                'index' => 'from_date',
                'type' => 'date',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn('to_date', [
                'header' => __('End'),
                'align' => 'left',
                'index' => 'to_date',
                'default' => '--',
                'type' => 'date',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'is_active', [
                'header' => __('Status'),
                'index' => 'is_active',
                'type' => 'options',
                'header_css_class' => 'col-websites',
                'column_css_class' => 'col-websites',
                'options' => array(
                    1 => __('Active'),
                    0 => __('Inactive')
                ),
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn('approve', [
                'header' => __('Approve'),
                'align' => 'left',
                'index' => 'is_approve',
                'filter' => false,
                'type' => 'text',
                'renderer' => 'Ced\CsPromotions\Block\Promo\Catalog\Renderer\Approved',
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'website_ids', [
                    'header' => __('Web Site'),
                    'index' => 'website_ids',
                    'type' => 'options',
                    'sortable' => false,
                    'filter' => false,
                    'options' => $this->_websiteFactory->create()->getCollection()->toOptionHash(),
                    'header_css_class' => 'col-websites',
                    'column_css_class' => 'col-websites'
                ]
            );
        }

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'core/url';
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
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

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended|void
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            ['id' => $row->getRuleId()]
        );
    }
}
