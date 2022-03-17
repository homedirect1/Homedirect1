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
 * @author 	CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsHyperlocal\Controller\Shiparea;

/**
 * Class Add
 * @package Ced\CsHyperlocal\Controller\Shiparea
 */
class Add extends \Ced\CsMarketplace\Controller\Vendor
{


    /**
     * @return \Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
        if(!$this->_getSession()->getVendorId())
            return;
        return $this->_forward('edit');
    }
}


