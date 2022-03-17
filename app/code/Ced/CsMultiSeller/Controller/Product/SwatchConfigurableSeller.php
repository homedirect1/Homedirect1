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
 * @package     Ced_CsMultiSeller
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMultiSeller\Controller\Product;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class SwatchConfigurableSeller
 * @package Ced\CsMultiSeller\Controller\Product
 */
class SwatchConfigurableSeller extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_coreRegistry->register('swatchProductId', $this->getRequest()->getParam('swatchProductId'));
        $resultPage = $this->_resultPageFactory->create();
        $block = $resultPage->getLayout()
        ->createBlock('Ced\CsMultiSeller\Block\SwatchConfigurableMultiseller')
        ->setTemplate('Ced_CsMultiSeller::swatchConfigurableList.phtml')
        ->toHtml();
       
        return $this->getResponse()->setBody($block);
    }
    /**
     * Grid action
     *
     * @return void
     */
   
}