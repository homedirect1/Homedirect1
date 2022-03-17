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

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;

/**
 * Class Edit
 * @package Ced\CsHyperlocal\Controller\Zipcode
 */
class Edit extends \Ced\CsMarketplace\Controller\Vendor
{

    /**
     * @var \Ced\CsHyperlocal\Model\ZipcodeFactory
     */
    protected $_zipcodeModel;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory
     */
    protected $_zipcodeCollection;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Edit constructor.
     * @param \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcode
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection
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
        \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcode,
        \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection,
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

        $this->_zipcodeModel = $zipcode;
        $this->_zipcodeCollection = $zipcodeCollection;
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
                $zipcodeCollection = $this->_zipcodeCollection->create()
                    ->addFieldToFilter('vendor_id',$this->_getSession()->getVendorId())
                    ->addFieldToFilter('id',$id);

                if ($zipcodeCollection->count() >0) {

                    $zipcodeData = $this->_zipcodeModel->create()->load($id);
                    $this->registry->register('zipcode_data',$zipcodeData->getData());
                    $resultPage->getConfig()->getTitle()->set(__('Edit ') . $zipcodeData->getZipcode());
                } else {
                    $this->messageManager->addErrorMessage(__('Zip code does not exist.'));
                    return $this->_redirect('*/*/');
                }
            } else {
                $resultPage->getConfig()->getTitle()->set(__('Add new zip code'));
            }
            return $resultPage;
        } else {
            return $this->_redirect('csmarketplace/vendor/index');
        }
    }
}
