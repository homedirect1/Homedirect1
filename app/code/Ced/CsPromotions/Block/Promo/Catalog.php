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

namespace Ced\CsPromotions\Block\Promo;

/**
 * Class Catalog
 * @package Ced\CsPromotions\Block\Promo
 */
class Catalog extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var string
     */
    protected $_template = 'promo/catalog.phtml';

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Catalog constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->setData("area", "adminhtml");
    }

    /**
     * @return \Magento\Backend\Block\Widget\Container
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $addButtonProps = [
            'id' => 'add_new_rule',
            'label' => __('Add New Rule'),
            'class' => 'add primary',
            'onclick' => "location.href='" . $this->getUrl('*/*/new') . "'",
            'button_class' => '',
        ];
        $this->buttonList->add('add_new', $addButtonProps);

        $addButtonProps = [
            'id' => 'apply_rule',
            'label' => __('Apply Rules'),
            'class' => 'apply',
            'onclick' => "location.href='" . $this->getUrl('*/*/applyRules') . "'",
            'button_class' => 'apply',
        ];
        $this->buttonList->add('add_apply_rules', $addButtonProps);

        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Ced\CsPromotions\Block\Promo\Catalog\Grid', 'ced.cspromotions.vendor.grid')
        );

        return parent::_prepareLayout();

    }


    /**
     * Retrieve product create url by specified product type
     *
     * @param string $type
     * @return string
     */
    protected function _getProductCreateUrl($type)
    {

        return $this->getUrl(
            'csproduct/*/new',
            ['set' => $this->_productFactory->create()->getDefaultAttributeSetId(), 'type' => $type]
        );
    }

    /**
     * @return array
     */
    protected function _getAddButtonOptions()
    {

        $splitButtonOptions[] = [
            'label' => __('Add New'),
            'onclick' => "setLocation('" . $this->_getCreateUrl() . "')",
            'area' => 'adminhtml'
        ];

        return $splitButtonOptions;
    }

    /**
     * @return string
     */
    protected function _getCreateUrl()
    {
        return $this->getUrl(
            '*/*/new'
        );
    }

    /**
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
