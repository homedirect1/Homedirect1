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
 * Class Managezipcode
 * @package Ced\CsHyperlocal\Controller\Shiparea
 */
class Managezipcode extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var \Magento\Framework\View\Result\Page
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $csmarketplaceHelper;

    /**
     * @var \Ced\CsHyperlocal\Model\ShipareaFactory
     */
    protected $shipareaFactory;

    /**
     * Managezipcode constructor.
     * @param \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaFactory
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
        \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaFactory,
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

        $this->_registry = $registry;
        $this->csmarketplaceHelper = $csmarketplaceHelper;
        $this->shipareaFactory = $shipareaFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|void
     */
    public function execute()
    {
        if (!$this->_getSession()->getVendorId()) {
            return;
        }
        if ($this->csmarketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::MODULE_ENABLE) &&
            $this->csmarketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_TYPE) == 'zipcode') {
            $shipareaData = $this->shipareaFactory->create()->load($this->getRequest()->getParam('id'));
            if ($shipareaData->getZipcodeType() == 'multiple') {
                $this->_registry->register('location_id', $this->getRequest()->getParam('id'));
                $resultPage = $this->resultPageFactory->create();
                $resultPage->getConfig()->getTitle()->set(__($shipareaData->getLocation()));
                return $resultPage;
            } else {
                $this->messageManager->addErrorMessage(__('Location has only single zipcode.'));
                $this->_redirect('*/*/index');
            }
        } else {
            $this->_redirect('csmarketplace/vendor/index');
        }
    }
}
