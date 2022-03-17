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

namespace Ced\CsHyperlocal\Controller\Shiparea;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;

/**
 * Class Edit
 * @package Ced\CsHyperlocal\Controller\Shiparea
 */
class Edit extends \Ced\CsMarketplace\Controller\Vendor
{

    /**
     * @var \Ced\CsHyperlocal\Model\ShipareaFactory
     */
    protected $_shipareaModel;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory
     */
    protected $_shipareaCollection;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Edit constructor.
     * @param \Ced\CsHyperlocal\Model\ShipareaFactory $shiparea
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaCollection
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlFactory $urlFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper
     * @param \Ced\CsMarketplace\Helper\Acl $aclHelper
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendor
     */
    public function __construct(
        \Ced\CsHyperlocal\Model\ShipareaFactory $shiparea,
        \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaCollection,
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Session $customerSession,
        UrlFactory $urlFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        \Ced\CsMarketplace\Helper\Acl $aclHelper,
        \Ced\CsMarketplace\Model\VendorFactory $vendor
    )
    {
        parent::__construct(
            $context,
            $resultPageFactory,
            $customerSession,
            $urlFactory,
            $registry,
            $jsonFactory,
            $csmarketplaceHelper,
            $aclHelper,
            $vendor
        );

        $this->_shipareaModel = $shiparea;
        $this->_shipareaCollection = $shipareaCollection;
        $this->registry = $registry;
    }

    /**
     * @return \Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
        if (!$this->_getSession()->getVendorId())
            return;

        if ($this->csmarketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::MODULE_ENABLE)) {

            $id = $this->getRequest()->getParam('id');

            $resultPage = $this->resultPageFactory->create();

            if ($id) {
                $shipareaCollection = $this->_shipareaCollection->create()
                    ->addFieldToFilter('vendor_id', $this->_getSession()->getVendorId())
                    ->addFieldToFilter('id', $id);

                if ($shipareaCollection->count() > 0) {

                    $shipareaData = $this->_shipareaModel->create()->load($id);
                    $this->registry->register('shiparea_data', $shipareaData->getData());
                    $resultPage->getConfig()->getTitle()->set(__('Edit ') . $shipareaData->getLocation());
                } else {
                    $this->messageManager->addErrorMessage(__('Location does not exist.'));
                    return $this->_redirect('*/*/');
                }
            } else {
                $resultPage->getConfig()->getTitle()->set(__('Add Location'));
            }
            return $resultPage;
        } else {
            return $this->_redirect('csmarketplace/vendor/index');
        }
    }
}
