<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Inventory
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsInventory\Controller\Manage;

use Braintree\Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Result\PageFactory;

class Save extends \Ced\CsMarketplace\Controller\Vendor
{
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Session $customerSession,
        UrlFactory $urlFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        \Ced\CsMarketplace\Helper\Acl $aclHelper,
        \Ced\CsMarketplace\Model\VendorFactory $vendor
    ) {
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
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /*if ((int)$data['minimum_quantity'] !== $data['minimum_quantity']){
            return;
        }*/

        if (isset($data) && $this->_getSession()->getData('vendor_id')) {
            $data['vendor_id'] = $this->_getSession()->getData('vendor_id');

            $inventory = $this->_objectManager->create('\Ced\CsInventory\Model\Inventory');

            if (!empty($inventory->load($data['vendor_id'], 'vendor_id')->getData())) {
                $id = $inventory->load($data['vendor_id'], 'vendor_id')->getId();
                $data['id'] = $id;
                $inventory->load($id);
            }
            try {

                $inventory->setData($data)->save();

            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Error during Saving'));
                $this->_redirect('*/*');
                return;

            }
            $this->messageManager->addSuccessMessage(__('Data Saved Successfully'));
            $this->_redirect('*/*');
            return;
        }
        $this->messageManager->addErrorMessage(__('Error during Saving'));
        $this->_redirect('*/*');
    }
}
