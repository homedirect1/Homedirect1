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
 * @package     Ced_CsSubAccount
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Controller\Customer; 
 
class View extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * 
     *
     * @var \Magento\Framework\View\Result\Page 
     */
    protected $resultPageFactory;

    public function execute()
    {  
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Assign Resources'));
        $resultPage->getLayout()
            ->getBlock('group_edit_js')
            ->setIsPopup((bool)$this->getRequest()->getParam('popup'));
        return $resultPage;
        
    }
}
 
