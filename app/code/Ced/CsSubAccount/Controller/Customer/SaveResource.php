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

namespace Ced\CsSubAccount\Controller\Customer;

use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;
use Magento\Framework\App\Action\Context;

/**
 * Class SaveResource
 * @package Ced\CsSubAccount\Controller\Customer
 */
class SaveResource extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var \Ced\CsSubAccount\Model\CsSubAccountFactory
     */
    protected $csSubAccountFactory;

    /**
     * SaveResource constructor.
     * @param \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory
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
        \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory,
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
        $this->csSubAccountFactory = $csSubAccountFactory;
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
     * Promo quote edit action
     *
     * @return                                  void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        if (!$this->_getSession()->getVendorId())
            $this->_redirect('csmarketplace/account/login/');
        $post = $this->getRequest()->getPost();
        $id = $post['subVendorId'];
        $resources = [];
        $resource = '';
        try {
            if ($post['all'] == 1) {
                $model = $this->csSubAccountFactory->create()->load($id)->setRole('all');
                $model->save();
            } else {
                $resources = $post['resource'];
                if (!empty($resources)) {
                    $resource = implode(',', $resources);
                }
                $model = $this->csSubAccountFactory->create()->load($id)->setRole($resource);
                $model->save();
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $this->_redirect('cssubaccount/customer/index');
            return;
        }
        $this->messageManager->addSuccessMessage(__('Resources are successfully alloted to the Sub-Vendor'));
        $this->_redirect('cssubaccount/customer/index');
        return;
    }

}
