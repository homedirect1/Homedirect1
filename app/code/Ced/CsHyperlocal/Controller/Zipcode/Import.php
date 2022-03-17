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

/**
 * Class Import
 * @package Ced\CsHyperlocal\Controller\Zipcode
 */
class Import extends \Ced\CsMarketplace\Controller\Vendor
{

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
        if (!$this->_getSession()->getVendorId())
            return;


        if ($this->csmarketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::MODULE_ENABLE)) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Import Zipcode CSV'));
            return $resultPage;
        } else {
            return $this->_redirect('csmarketplace/vendor/index');
        }
    }
}
