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
 * @package     Ced_CsHyperlocal
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsHyperlocal\Controller\Adminhtml\Shiparea;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Class Managezipcode
 * @package Ced\CsHyperlocal\Controller\Adminhtml\Shiparea
 */
class Managezipcode extends Action
{
    /**
     *
     */
    const ENTITY_TYPE = \Magento\Catalog\Model\Product::ENTITY;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Ced\CsHyperlocal\Helper\Data
     */
    protected $cshyperlocalHelper;

    /**
     * @var \Ced\CsHyperlocal\Model\ShipareaFactory
     */
    protected $shipareaFactory;

    /**
     * Managezipcode constructor.
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Ced\CsHyperlocal\Helper\Data $cshyperlocalHelper
     * @param \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ced\CsHyperlocal\Helper\Data $cshyperlocalHelper,
        \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaFactory
    )
    {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->cshyperlocalHelper = $cshyperlocalHelper;
        $this->shipareaFactory = $shipareaFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        if ($this->cshyperlocalHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_TYPE) == 'zipcode') {
            $shipareaData = $this->shipareaFactory->create()->load($this->getRequest()->getParam('id'));
            $resultPage = $this->_resultPageFactory->create();
            $resultPage->setActiveMenu('Ced_CsHyperlocal::shippingarea');
            $resultPage->getConfig()->getTitle()->prepend(__($shipareaData->getLocation()));
            return $resultPage;
        } else {
            return $this->_redirect('*/*/');
        }
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_CsHyperlocal::shippingarea');

    }
}
