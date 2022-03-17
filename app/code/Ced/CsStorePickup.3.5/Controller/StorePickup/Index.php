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
 * @category  Ced
 * @package   Ced_CsStorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsStorePickup\Controller\StorePickup;

use Ced\CsMarketplace\Controller\Vendor;
use Ced\CsMarketplace\Helper\Acl;
use Ced\CsMarketplace\Model\VendorFactory;
use Ced\CsStorePickup\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Ced\CsStorePickup\Controller\StorePickup
 */
class Index extends Vendor
{
    /**
     * @var Data
     */
    protected $storepickupHelper;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param Data $storepickupHelper
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlFactory $urlFactory
     * @param Registry $registry
     * @param JsonFactory $jsonFactory
     * @param \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper
     * @param Acl $aclHelper
     * @param VendorFactory $vendor
     */
    public function __construct(
        Data $storepickupHelper,
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession,
        UrlFactory $urlFactory,
        Registry $registry,
        JsonFactory $jsonFactory,
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        Acl $aclHelper,
        VendorFactory $vendor
    )
    {
        $this->storepickupHelper = $storepickupHelper;
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
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        if (!$this->session->getVendorId()) {
            return;
        }
        if ($this->storepickupHelper->isEnable() == "0") {
            $this->_redirect('*/*/index');
            return;
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Your Stores'));
        return $resultPage;
    }
}