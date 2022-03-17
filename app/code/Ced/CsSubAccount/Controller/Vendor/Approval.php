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
 * @package     Ced_CsSubAccount
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Controller\Vendor;

use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Ced\CsMarketplace\Helper\Data;

/**
 * Class Approval
 * @package Ced\CsSubAccount\Controller\Vendor
 */
class Approval extends \Ced\CsMarketplace\Controller\Account\Approval
{
    /**
     * @var \Magento\Framework\View\Result\Page
     */
    public $resultPageFactory;

    /**
     * @var Session
     */
    public $_customerSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Ced\CsSubAccount\Model\CsSubAccountFactory
     */
    protected $csSubAccountFactory;

    /**
     * Approval constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlFactory $urlFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param Data $csmarketplaceHelper
     * @param \Ced\CsMarketplace\Helper\Acl $aclHelper
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendor
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory,
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession,
        UrlFactory $urlFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        Data $csmarketplaceHelper,
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

        $this->_customerSession = $customerSession;
        $this->_scopeConfig = $scopeConfig;
        $this->csSubAccountFactory = $csSubAccountFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {

        if (!$this->_customerSession->getParentVendor())
            return parent::execute();
        if (!$this->_scopeConfig->getValue('ced_cssubaccount/general/cssubaccount_active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $this->_redirect('customer/account/');
            return;
        }

        $collection = $this->csSubAccountFactory->create()->load($this->_customerSession->getCustomerEmail(), 'email');
        if (empty($collection)) {
            $this->messageManager->addErrorMessage(__('Sub-vendor does not exist'));
            $this->_redirect('csmarketplace/account/login/');
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Sub-Vendor Approval Status'));
        return $resultPage;

    }
}
 
