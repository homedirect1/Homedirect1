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

namespace Ced\CsHyperlocal\Controller\Adminhtml\Vendor;


use Magento\Backend\App\Action;


class Newlocation extends \Magento\Backend\App\Action

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
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */

    public function __construct(

        Action\Context $context,

        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry

    )
    {

        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
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

        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()
            ->prepend(__('New Location'));
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
        $selectedVendorIds = $this->getRequest()->getParam('vendor_id');
        $this->_coreRegistry->register('selected_vendors', $selectedVendorIds);
        $resultPage->setActiveMenu('Ced_CsHyperlocal::shipping_area')
            ->addBreadcrumb(__('Delivery Location'), __('Delivery Location'));

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