<?php
namespace Ced\StorePickup\Controller\Index;
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
 * @category  Ced
 * @package   Ced_StorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Search extends \Magento\Framework\App\Action\Action
{
    
    protected $resultPageFactory;
    
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    
    public function execute() 
    {
        $data = $this->getRequest()->getPostValue();
        $flag = false;
        $country = trim($data['country_id']);
        $state = trim($data['region_id']);
        $city = trim($data['city']);
        if($country || $state || $city ) {
            $flag = true;
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if($flag) {
            $resultRedirect = $this->resultPageFactory->create();
             return $resultRedirect;
        }else{
            $this->messageManager->addError('Please enter Country or State or City');
            return $resultRedirect->setPath('*/*/*');
        }
    }
}
