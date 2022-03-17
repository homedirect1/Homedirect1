<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Block\Adminhtml\Catalog\Product;

class PlanTab extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Webkul_Recurring::catalog/product/tab.phtml';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Request\Http $request
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->storeManager = $context->getStoreManager();
        $this->request = $request;
        
        parent::__construct($context, $data);
    }
    
    /**
     * Get product data
     *
     * @return array
     */
    public function getProduct()
    {
        try {
            return $this->registry->registry("current_product");
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Retrieve options field id prefix
     *
     * @return string
     */
    public function getFieldId()
    {
        return 'product_option';
    }

    /**
     * Get item count
     *
     * @return int
     */
    public function getItemCount()
    {
        return 1;
    }

    /**
     * Return current product id
     *
     * @return null|int
     */
    public function getCurrentProductId()
    {
        return $this->getProduct()->getId();
    }
}
