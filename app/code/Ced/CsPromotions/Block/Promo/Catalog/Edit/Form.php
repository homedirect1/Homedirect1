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

namespace Ced\CsPromotions\Block\Promo\Catalog\Edit;

/**
 * Adminhtml sales order view plane
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Ced\CsPromotions\Block\Adminhtml\Promo\Catalog\Edit\Tabs
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'promo/catalog/view/form.phtml';

    public function __construct()
    {
        $this->setId('promo_catalog_form');
        $this->setTitle(__('Rule Information'));
        $this->setData('area', 'adminhtml');
    }

}
