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

namespace Ced\CsPromotions\Block\Adminhtml\Promo\Quote\Edit;

/**
 * Catalog rules view tabs
 */
class Tabs extends /*\Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tabs*/\Magento\Backend\Block\Widget\Tabs
{

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('promo_quote_edit_tabs');
        $this->setDestElementId('cart_rule_view');
        $this->setTitle(__('Cart Price Rule'));
        $this->setData("area","adminhtml");
    }
}
