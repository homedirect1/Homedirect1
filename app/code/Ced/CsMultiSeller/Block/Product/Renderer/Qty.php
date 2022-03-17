<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * @category    Ced
 * @package     Ced_CsMultiSeller
 * @author   	CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ced\CsMultiSeller\Block\Product\Renderer;

class Qty extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory
    )
    {
        $this->productFactory = $productFactory;
    }

    /**
     * @return Qty
     */
    public function render(\Magento\Framework\DataObject $row) {
        $product =  $this->productFactory->create()->load($row->getProductId());
        $stockqty = $product->getExtensionAttributes()->getStockItem()->getQty();
        return $stockqty;
    }

}
