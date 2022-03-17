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

namespace Ced\CsHyperlocal\Controller\Adminhtml\Zipcode;

use Magento\Backend\App\Action;

/**
 * Class Edit
 * @package Ced\CsHyperlocal\Controller\Adminhtml\Zipcode
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
     * @var \Ced\CsHyperlocal\Model\ZipcodeFactory
     */
    protected $zipcodeFactory;

    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->zipcodeFactory = $zipcodeFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return true;
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

        $resultPage->setActiveMenu('Ced_CsHyperlocal::shippingarea')
            ->addBreadcrumb(__('Add Zipcode'), __('Add Zipcode'));

        return $resultPage;
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
        $zipcodeData = $this->zipcodeFactory->create()->load($id);
        $this->_coreRegistry->register('ced_zipcode_data', $zipcodeData);
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()
            ->prepend($zipcodeData->getId() ? $zipcodeData->getZipcode() : __('New Zipcode'));

        return $resultPage;
    }

}