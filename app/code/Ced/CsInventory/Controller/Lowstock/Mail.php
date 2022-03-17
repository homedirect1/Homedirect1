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

namespace Ced\CsInventory\Controller\Lowstock;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Result\PageFactory;

class Mail extends \Ced\CsMarketplace\Controller\Vendor
{
    private $scopeConfig;
    private $_storeManager;
    private $_transportBuilder;
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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        $storeManager = $objectManager->create('\Magento\Store\Model\StoreManagerInterface');
        $transportBuilder = $objectManager->create('\Magento\Framework\Mail\Template\TransportBuilder');
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_transportBuilder = $transportBuilder;
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



    /*public function execute(){
        $post = $this->getRequest()->getPostValue();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        try {
            $postObject = new \Magento\Framework\DataObject();
            $post['myname'] = $customerSession->getCustomer()->getName(); //Loggedin customer Name
            $post['myemail'] = $customerSession->getCustomer()->getEmail(); //Loggedin customer Email

            $myname = $post['myname'];
            $myemail = $post['myemail'];
            $sender = [
                'name' => $myname,
                'email' => $myemail,
            ];
            $sentToEmail = $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE);
            $sentToname = $this->scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE);
            $senderToInfo = [
                'name' =>($sentToname),
                'email' => ($sentToEmail),
            ];
            print_r($myemail);die;
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier('mymodule_email_template') // My email template
                ->setTemplateOptions( [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file if admin then \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ])
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($sender)
                ->addTo($senderToInfo)
                ->addBcc($senderBcc)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
            $this->messageManager->addSuccess(__('Thanks'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setRefererOrBaseUrl();
            return $resultRedirect;
        } catch (\Exception $e) {
            die($e->getMessage());
            $this->messageManager->addError(__('Try again'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setRefererOrBaseUrl();
            return $resultRedirect;
        }
    }*/

    public function execute()
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $vData = $objectManager->create('Ced\CsInventory\Model\Inventory')->getCollection()->getData();

        $vProducts = [];
        $pId = [];
        foreach ($vData as $key=>$value){
            print_r($value);
            $vProducts = $objectManager->create('Ced\CsMarketplace\Model\Vproducts')
                ->getCollection()->addFieldToFilter('vendor_id', $value['vendor_id'] )
                ->addFieldToFilter('qty',array('lt'=> $value['minimum_quantity']))->getData();
            print_r($vProducts);
            foreach ($vProducts as $k=>$v){
                $pId[$value['vendor_id']]['product_id'][$k] = $v['product_id'];
                $pId[$value['vendor_id']]['name'][$k] = $v['name'];
            }
        }

        foreach ($pId as $k=>$v){
            $this->email($k,implode(',', $v['name']));
        }
        print_r($pId);
        die('died');
        if(!$this->_getSession()->getVendorId()) {
            return;
        }
        $check = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')
            ->getStoreConfig('ced_csinventory/general/csinventory_active');
        if(!$check) {
            $this->_redirect('csmarketplace/vendor/index');
            return;
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Inventory Lowstock'));
        return $resultPage;

    }

    private function email($vendorId,$products){

        $emails = 2;


        $emailTemplate = 'csinventory_email_template';

        $vendor = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')
            ->getCollection()->addFieldToFilter('entity_id',$vendorId )
            ->addAttributeToSelect(['email','public_name'])->toArray();
        $vendor_data = [];
        foreach ($vendor as $k=>$v){
            $vendor_data = $v;
        }
        if (isset($vendor_data)) {
            $emailTemplateVariables = array();
            $emailTemplateVariables['vname'] = $vendor_data['public_name'];
            $emailTemplateVariables['vid'] = $vendorId;

            $emailTemplateVariables['message'] = 'vendor '.$vendor_data['public_name'].', you have reached your minimum quantity product for products '.$products;

            $sender = [
                'name' => $vendor_data['public_name'],
                'email' => $vendor_data['email'],
            ];

            $adminMail = $this->scopeConfig->getValue(
                'trans_email/ident_sales/email',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            );
            $adminName = $this->scopeConfig->getValue(
                'trans_email/ident_sales/name',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            );
            $adminDetail = [
                'name' => $adminName,
                'email' => $adminMail,
            ];
            //print_r($sender);
            //  print_r($adminDetail);
            try {
                $transport = $this->_transportBuilder->setTemplateIdentifier($emailTemplate)
                    ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
                    ->setTemplateVars($emailTemplateVariables)
                    ->setFrom($adminDetail)
                    ->addTo($sender)
                    ->getTransport();
            } catch (Exception $e) {
                die($e->getMessage());
                $errorMessage = $e->getMessage();
                $this->messageManager->addError(__($errorMessage));
            }

            try {
                $transport->sendMessage();
            } catch (Exception $e) {
                die($e->getMessage());
                $errorMessage = $e->getMessage();
                $this->messageManager->addError(__($errorMessage));
            }
        }
    }
}
