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
 * @package     Ced_CsPromotions
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsPromotions\Block\Adminhtml\Promo\Quote\Edit\Tab;

/**
 * "Manage Coupons Codes" Tab
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Coupons extends \Magento\Backend\Block\Text\ListText implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->setData('area', 'adminhtml');
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Manage Coupon Codes');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Manage Coupon Codes');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return $this->_isEditing();
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return !$this->_isEditing();
    }

    /**
     * Check whether we edit existing rule or adding new one
     *
     * @return bool
     */
    protected function _isEditing()
    {
        $priceRule = $this->_coreRegistry->registry('current_promo_quote_rule');
        return $priceRule->getRuleId() !== null;
    }
}
