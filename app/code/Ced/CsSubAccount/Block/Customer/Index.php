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
  * @category  Ced
  * @package   Ced_CsSubAccount
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

namespace Ced\CsSubAccount\Block\Customer;

class Index extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var string
     */
    protected $_template = 'customer/index.phtml';
 
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setData('area','adminhtml');
    }
 
    /**
     * Prepare button and grid
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout()
    {
        $addButtonProps = [
            'id' => 'request_customer',
            'label' => __('Send Request'),
            'class' => 'add primary',
            'onclick' => "location.href='" . $this->getUrl('*/*/new') . "'",
            'button_class' => '',
        ];
        $this->buttonList->add('add_new', $addButtonProps);
        
        $this->setChild(
                'grid',
                $this->getLayout()->createBlock('Ced\CsSubAccount\Block\Customer\Grid', 'ced.cssubaccount.vendor.grid')
        );  
        
        return parent::_prepareLayout(); 
        
    }
 
 
    /**
     *
     *
     * @param string $type
     * @return string
     */
    protected function _getCreateUrl()
    {
        return $this->getUrl(
            '*/*/new'
        );
    }
 
    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}
?>
