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

use Ced\CsMarketplace\Controller\ResponseInterface;
use Ced\CsMarketplace\Controller\Vendor;
use Ced\CsMarketplace\Helper\Acl;
use Ced\CsMarketplace\Helper\Data;
use Ced\CsMarketplace\Model\VendorFactory;
use Ced\StorePickup\Model\StoreInfoFactory;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 * @package Ced\CsStorePickup\Controller\StorePickup
 */
class Edit extends Vendor
{

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @var StoresFactory
     */
    protected $storesFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Edit constructor.
     * @param StoreInfoFactory $storesFactory
     * @param \Magento\Backend\Model\Session $backendSession
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlFactory $urlFactory
     * @param Registry $registry
     * @param JsonFactory $jsonFactory
     * @param Data $csmarketplaceHelper
     * @param Acl $aclHelper
     * @param VendorFactory $vendor
     */
    public function __construct(
        StoreInfoFactory $storesFactory,
        \Magento\Backend\Model\Session $backendSession,
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession,
        UrlFactory $urlFactory,
        Registry $registry,
        JsonFactory $jsonFactory,
        Data $csmarketplaceHelper,
        Acl $aclHelper,
        VendorFactory $vendor
    ) {
        $this->storesFactory = $storesFactory;
        $this->backendSession = $backendSession;
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
     * @return ResponseInterface|ResultInterface|Page|void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('pickup_id');
        $model = $this->storesFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getPickupId()) {
                $this->messageManager->addErrorMessage(__('This store no longer exists.'));
                $this->_redirect('csstorepickup/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = $this->backendSession->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->registry->register('csstorepickup_stores', $model);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(
            $model->getPickupId() ? $model->getStoreName() : __('New Store')
        );
        return $resultPage;
    }
}