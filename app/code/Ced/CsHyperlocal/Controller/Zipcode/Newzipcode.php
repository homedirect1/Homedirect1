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
 * Class Add
 * @package Ced\CsHyperlocal\Controller\Shiparea
 */
class Newzipcode extends \Ced\CsMarketplace\Controller\Vendor
{

    /**
     * @return \Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
        if (!$this->_getSession()->getVendorId())
            return;

        if ($this->csmarketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::MODULE_ENABLE) &&
            $this->csmarketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_TYPE) == 'zipcode') {
            return $this->_forward('edit');
        } else {
            $this->_redirect('csmarketplace/vendor/index');
        }
    }
}


