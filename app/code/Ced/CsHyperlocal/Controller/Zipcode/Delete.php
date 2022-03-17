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

namespace Ced\CsHyperlocal\Controller\Zipcode;

use Ced\CsMarketplace\Controller\Vendor;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;

/**
 * Class Delete
 * @package Ced\CsHyperlocal\Controller\Zipcode
 */
class Delete extends Vendor
{
    /**
     * @var \Ced\CsHyperlocal\Model\ZipcodeFactory
     */
    protected $zipcodeFactory;

    /**
     * Delete constructor.
     * @param \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory
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
        \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory,
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

        $this->zipcodeFactory = $zipcodeFactory;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $locationId = $this->getRequest()->getParam('location_id');
        $ids = explode(',', $id);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($ids) {
            try {
                foreach ($ids as $id) {
                    $model = $this->zipcodeFactory->create();
                    $model->load($id);
                    $model->delete();
                }
            } catch (\Exception $e) {

                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 element(s) have been deleted.', count($ids)));
        return $resultRedirect->setPath('*/shiparea/managezipcode/', ['id' => $locationId]);
    }
}
