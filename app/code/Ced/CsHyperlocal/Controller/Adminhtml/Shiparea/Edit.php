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

/**
 * Class Edit
 * @package Ced\CsHyperlocal\Controller\Adminhtml\Shiparea
 */
class Edit extends \Magento\Backend\App\Action
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Ced\CsHyperlocal\Model\Shiparea
     */
    protected $shipareaModel;

    /**
     * @var \Ced\CsMarketplace\Model\VendorFactory
     */
    protected $vendorFactory;

    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Ced\CsHyperlocal\Model\Shiparea $shipareaModel
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Ced\CsHyperlocal\Model\Shiparea $shipareaModel,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->shipareaModel = $shipareaModel;
        $this->vendorFactory = $vendorFactory;
        parent::__construct($context);
    }

    /**
     * Edit grid record
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $shipareaData = $this->shipareaModel->load($id);
        $this->_coreRegistry->register('ced_shiparea_data', $shipareaData);
        $resultPage = $this->_initAction();
        if ($shipareaData->getId() != 0)
            $vendorName = $this->vendorFactory->create()->load($shipareaData->getVendorId())->getEmail();
        else
            $vendorName = __('Admin');

        $resultPage->getConfig()->getTitle()
            ->prepend($shipareaData->getId() ? $shipareaData->getLocation() . ' ( ' . $vendorName . ' )' : __('New Ship Area'));
        return $resultPage;
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('Ced_CsHyperlocal::shipping_area')
            ->addBreadcrumb(__('Ship Area'), __('Ship Area'));

        return $resultPage;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return true;
    }

}