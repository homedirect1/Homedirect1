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
  * @package     Ced_OrderDelete
  * @author   	 CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */


namespace Ced\OrderDelete\Controller\Adminhtml\Orderdelete;
 
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderManagementInterface;

class Massdelete extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
     protected $_scopeConfig;
    protected $resultRedirectFactory;

    public function __construct(Context $context,
     Filter $filter,
      \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
      CollectionFactory $collectionFactory)
    {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
    }

    /**
     * Unhold selected orders
     *
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        /** @var \Magento\Sales\Model\Order $order */
         $enable=$this->_scopeConfig->getValue('order_section/order_group/order_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
       if($enable){
        $helper=$this->_objectManager->create('Ced\OrderDelete\Helper\Data');
            $status=$helper->deleteMassOrder($collection);
            if($status){
                $this->messageManager->addSuccess(__($status.' Orders Deleted Successfully.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('sales/order/index');
                return $resultRedirect;
            }else{
                $this->messageManager->addError(__('Orders not Deleted.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('sales/order/index');
                return $resultRedirect;
            }
        }else{
            $this->messageManager->addError(__('Delete Functionality is disabled.Please enable it'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('sales/order/index');
            return $resultRedirect; 
        }    
    }
}
