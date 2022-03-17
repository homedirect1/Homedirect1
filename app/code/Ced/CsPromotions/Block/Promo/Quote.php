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
 * Class Quote
 * @package Ced\CsPromotions\Block\Promo
 */
class Quote extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var string
     */
    protected $_template = 'promo/catalog.phtml';

    /**
     * @return \Magento\Backend\Block\Widget\Container
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $newurl = $this->getUrl('*/*/new');
        $addButtonProps = [
            'id' => 'add_new_rule',
            'label' => __('Add New Rule'),
            'class' => 'add primary',
            'onclick' => "setLocation('{$newurl}')",
            'button_class' => '',
        ];
        $this->buttonList->add('add_new', $addButtonProps);

        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Ced\CsPromotions\Block\Promo\Quote\Grid', 'ced.cspromotions.vendor.cart.grid')
        );

        return parent::_prepareLayout();

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
