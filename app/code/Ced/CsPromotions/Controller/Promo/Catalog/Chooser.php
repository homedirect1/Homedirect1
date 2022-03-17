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

namespace Ced\CsPromotions\Controller\Promo\Catalog;

class Chooser extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('attribute') == 'sku') {
            $type = 'Magento\CatalogRule\Block\Adminhtml\Promo\Widget\Chooser\Sku';
        }
        if (!empty($type)) {
            $block = $this->_view->getLayout()->createBlock($type);
            if ($block) {
                $this->getResponse()->setBody($block->toHtml());
            }
        }
    }
}
