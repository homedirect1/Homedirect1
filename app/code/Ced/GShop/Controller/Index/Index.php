<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
  * @package  Ced_GShop
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GShop\Controller\Index;

use \Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Index extends Action
{
    /**
     * @var \Ced\GShop\Helper\MultiAccount $multiAccHelper
     */
    public $multiAccHelper;

    /** @var \Ced\GShop\Helper\GXpresslib $gXpressHelper */
    public $gXpressHelper;

    /** @var \Magento\Framework\Message\ManagerInterface $messageManager */
    public $messageManager;

    /** @var \Magento\Framework\UrlInterface $_urlInterface ; */
    public $_urlInterface;

    public $_coreRegistry;

    public function __construct(
        Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Ced\GShop\Helper\MultiAccount $multiAccHelper,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Registry $coreRegistry,
        \Ced\GShop\Helper\GXpresslib $gXpressHelper
    )
    {
        $this->multiAccHelper = $multiAccHelper;
        $this->gXpressHelper = $gXpressHelper;
        $this->messageManager = $messageManager;
        $this->_urlInterface = $urlInterface;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    public function execute()
    {
        $admin = $this->_objectManager->create(\Magento\Backend\Helper\Data::class)->getAreaFrontName();
        try {
            $cacheManager = $this->_objectManager->create(\Magento\Framework\App\CacheInterface::class);
            $accountID = $cacheManager->load('google_account');
            $formKey = $cacheManager->load('form_key');
            $account = $this->multiAccHelper->getAccountRegistry($accountID);
            $client = $this->gXpressHelper->getGoogleClient();
            $client->authenticate($this->getRequest()->getParam('code'));
            $refreshToken = $client->getAccessToken()['refresh_token'];
            if ($account && $account->getId()) {
                $base_token = $account->setAccountToken($refreshToken)->save();
                $this->messageManager->addSuccessMessage(
                    "Token for Google Shopping Account ". $account->getAccountCode() . " has been fetched successfully."
                );
                if (!($account->getMerchantId() == 123898449)) {
                    //$this->sendAccountLinkRequest($account);
                }
            }

            $url = $this->_urlInterface->getUrl($admin . '/gxpress/account/index');
            if($formKey) {
                $url = $url.'index/key/'.$formKey;
            }
            $this->getResponse()->setRedirect($url)->sendResponse();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                "Token for Google Shopping Account " .$account->getAccountCode() . " fetching failed. " . $e->getMessage()
            );
            $url = $this->_urlInterface->getUrl($admin . '/gxpress/account/index');
            if($formKey) {
                $url = $url.'key/'.$formKey;
            }
            $this->getResponse()->setRedirect($url)->sendResponse();
        }
    }

    public function sendAccountLinkRequest($accounts)
    {

        try {
            $merchantId = $accounts->getMerchantId();
            /* First request */
            /** @var \Ced\GShop\Helper\GXpresslib $helper */
            $helper = $this->_objectManager->create(\Ced\GShop\Helper\GXpresslib::class);
            if ($this->_coreRegistry->registry('google_account')) {
                $this->_coreRegistry->unregister('google_account');
            }
            $cedAccount = $this->multiAccHelper->getAccountRegistry(2);
            $cedMerchantId = $cedAccount->getMerchantId();
            $cedClient = $helper->getGoogleClient();
            $service = new \Google_Service_ShoppingContent($cedClient);
            $accountLinkRequest = new \Google_Service_ShoppingContent_AccountsLinkRequest();
            $accountLinkRequest->setAction('request');
            $accountLinkRequest->setLinkType('channelPartner');
            $accountLinkRequest->setLinkedAccountId($merchantId);
            $response = $service->accounts->link($cedMerchantId, $cedMerchantId, $accountLinkRequest);
            /* End of first request */

            /* Second request */
            if ($this->_coreRegistry->registry('google_account')) {
                $this->_coreRegistry->unregister('google_account');
            }
            $this->multiAccHelper->getAccountRegistry($accounts->getId());
            $client = $helper->getGoogleClient();
            $service = new \Google_Service_ShoppingContent($client);
            $accountLinkRequest = new \Google_Service_ShoppingContent_AccountsLinkRequest();
            $accountLinkRequest->setAction('approve');
            $accountLinkRequest->setLinkType('channelPartner');
            $accountLinkRequest->setLinkedAccountId($cedMerchantId);
            $response = $service->accounts->link($merchantId, $merchantId, $accountLinkRequest);
            $this->messageManager->addSuccess("Flagging API successfully executed for: ".$merchantId);
            /* End of second request */
            return true;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage("Flagging API have issue while executed for: ".$merchantId);
            $this->messageManager->addErrorMessage($e->getMessage());
            return false;
        }
    }
}
