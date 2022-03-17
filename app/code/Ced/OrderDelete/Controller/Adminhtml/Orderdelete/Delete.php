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
  * @category  Ced
  * @package   Ced_OrderDelete
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */


namespace Ced\OrderDelete\Controller\Adminhtml\Orderdelete;
 
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $modelFactory;
    protected $_scopeConfig;
 
    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $scopeConfig;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
    }
 
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {    
        $enable=$this->_scopeConfig->getValue(
            'order_section/order_group/order_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if($enable) {
             $id = $this->getRequest()->getParam('order_id');
            try {
                $orders=$this->_objectManager->create('Magento\Sales\Model\Order');
                $order=$orders->load($id);
                $helper=$this->_objectManager->create('Ced\OrderDelete\Helper\Data');
                $status=$helper->deleteOrder($order);
                if($status) {
                    $this->messageManager->addSuccess(__('Order Deleted Successfully.'));
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath('sales/order/index');
                    return $resultRedirect;
                }else{
                    $this->messageManager->addError(__('Order not Deleted.'));
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath('sales/order/index');
                    return $resultRedirect;
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addError(__('This order no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('sales/order/index');
                return $resultRedirect;
            } catch (InputException $e) {
                $this->messageManager->addError(__('This order no longer exists.'));
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
