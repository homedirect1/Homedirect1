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

namespace Ced\CsInventory\Cron;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Result\PageFactory;

class Mail
{
    private $scopeConfig;
    private $_storeManager;
    private $_transportBuilder;
    private $_objectManager;
    public function __construct()
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        $storeManager = $objectManager->create('\Magento\Store\Model\StoreManagerInterface');
        $transportBuilder = $objectManager->create('\Magento\Framework\Mail\Template\TransportBuilder');
        $messageManager = $objectManager->create('\Magento\Framework\Message\ManagerInterface');

        $this->messageManager = $messageManager;
        $this->scopeConfig = $scopeConfig;
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_transportBuilder = $transportBuilder;

    }
    public function execute()
    {

        $vData = $this->_objectManager->create('Ced\CsInventory\Model\Inventory')->getCollection()->getData();

        $vProducts = [];
        $pId = [];
        foreach ($vData as $key=>$value){
//            print_r($value);
            $vProducts = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')
                ->getCollection()->addFieldToFilter('vendor_id', $value['vendor_id'] )
                ->addFieldToFilter('qty',array('lt'=> $value['minimum_quantity']))->getData();
//            print_r($vProducts);
            foreach ($vProducts as $k=>$v){
                $pId[$value['vendor_id']]['product_id'][$k] = $v['product_id'];
                $pId[$value['vendor_id']]['name'][$k] = $v['name'];
            }
        }

        foreach ($pId as $k=>$v){
            $this->email($k,implode(',', $v['name']));
        }

    }

    private function email($vendorId,$products){

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
            try {
                $transport = $this->_transportBuilder->setTemplateIdentifier($emailTemplate)
                    ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
                    ->setTemplateVars($emailTemplateVariables)
                    ->setFrom($adminDetail)
                    ->addTo($sender)
                    ->getTransport();

                $transport->sendMessage();

            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
                $this->messageManager->addError(__($errorMessage));
            }

        }
    }
}
