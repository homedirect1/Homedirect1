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
 * @package     Ced_CsAdvTransaction
 * @author     CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Controller\Adminhtml\Pay;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Details
 * @package Ced\CsAdvTransaction\Controller\Adminhtml\Pay
 */
class Details extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var \Ced\CsMarketplace\Model\Vpayment
     */
    protected $vpayment;

    /**
     * Details constructor.
     * @param \Magento\Framework\Registry $registry
     * @param PageFactory $resultPageFactory
     * @param \Ced\CsMarketplace\Model\Vpayment $vpayment
     * @param Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        PageFactory $resultPageFactory,
        \Ced\CsMarketplace\Model\Vpayment $vpayment,
        Action\Context $context
    )
    {
        $this->_coreRegistry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->vpayment = $vpayment;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {

        $rowId = $this->getRequest()->getParam('id');
        $row = $this->vpayment->load($rowId);
        if (!$row->getId()) {
            $this->_redirect('*/*/', array('_secure' => true));
            return;
        }

        $this->_coreRegistry->register('csadvtransaction_current_transaction', $row);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ced_CsMarketplace::vendor_transaction');
        $resultPage->addBreadcrumb(__('CsMarketplace'), __('CsMarketplace'));
        $resultPage->addBreadcrumb(__('Manage Vendor Transactions'), __('Manage Vendor Transactions'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Vendor Transactions'));
        return $resultPage;

    }

}
