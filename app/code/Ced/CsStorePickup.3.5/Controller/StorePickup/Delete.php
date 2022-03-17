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

use Ced\CsMarketplace\Helper\Acl;
use Ced\CsMarketplace\Helper\Data;
use Ced\CsMarketplace\Model\VendorFactory;
use Ced\StorePickup\Model\StoreHourFactory;
use Ced\StorePickup\Model\StoreInfoFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Delete
 * @package Ced\CsStorePickup\Controller\StorePickup
 */
class Delete extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var StoresFactory
     */
    protected $storesFactory;

    /**
     * @var \Ced\CsStorePickup\Model\StoreHourFactory
     */
    protected $storeHourFactory;

    /**
     * Delete constructor.
     * @param StoreInfoFactory $storesFactory
     * @param StoreHourFactory $storeHourFactory
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
        StoreHourFactory $storeHourFactory,
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession,
        UrlFactory $urlFactory,
        Registry $registry,
        JsonFactory $jsonFactory,
        Data $csmarketplaceHelper,
        Acl $aclHelper,
        VendorFactory $vendor
    )
    {
        $this->storesFactory = $storesFactory;
        $this->storeHourFactory = $storeHourFactory;
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
        $id = $this->getRequest()->getParam('pickup_id');
        $model = $this->storesFactory->create();
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $model->load($id);
            $model->delete();
            $coll = $this->storeHourFactory->create();
            $coll = $coll->getCollection()
                ->addFieldToFilter('pickup_id', $id)
                ->getData();
            foreach ($coll as $val) {
                $deleteObject = $this->storeHourFactory->create();
                $deleteObject->load($val['id']);
                $deleteObject->delete();
            }
            $this->messageManager->addSuccessMessage(__('Deleted Successfully'));
            return $this->_redirect('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting the pickup store.'));
        }
        return $this->_redirect('*/*/');
    }
}