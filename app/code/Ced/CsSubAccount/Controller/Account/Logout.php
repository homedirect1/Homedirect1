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

namespace Ced\CsSubAccount\Controller\Account;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
class Logout extends \Ced\CsMarketplace\Controller\Account\Logout
{
    /**
     * Dispatch request
     *
     * @param  RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $this->_eventManager->dispatch(
            'ced_csmarketplace_predispatch_action', [
            'session' => $this->session,
            ]
        );
        return parent::dispatch($request);
    }
    
    
    /**
     * Customer logout action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $lastCustomerId = $this->session->getId();
        if($this->session->getSubVendorData())
            $this->session->unsSubVendorData();
        $this->session->logout()->setBeforeVendorAuthUrl($this->_redirect->getRefererUrl())
            ->setLastCustomerId($lastCustomerId);

        /**
         * 
         *
         * @var \Magento\Framework\Controller\Result\Redirect $resultRedirect 
         */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('csmarketplace/account/logoutSuccess');
        return $resultRedirect;
    }
}
