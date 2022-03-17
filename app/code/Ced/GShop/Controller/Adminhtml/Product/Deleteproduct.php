<?php
namespace Ced\GShop\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;

class Deleteproduct extends Action
{

    /**
     * @var PageFactory
     */
    public $resultPageFactory;
    /**
     * @var CollectionFactory
     */
    public $catalogCollection;
    /**
     * @var Filter
     */
    public $filter;

    const ADMIN_RESOURCE = 'Ced_GShop::GShop';

    /**
     * @var \Ced\GShop\Helper\MultiAccount
     */
    protected $multiAccountHelper;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CollectionFactory $collectionFactory,
        \Ced\GShop\Helper\MultiAccount $multiAccountHelper,
        Filter $filter
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->catalogCollection = $collectionFactory;
        $this->filter = $filter;
        $this->multiAccountHelper = $multiAccountHelper;
    }

    public function execute()
    {
        try{
            $productIdsToSync = [];
            $accountId = $this->_session->getAccountId();
            $prodStatusAccAttr = $this->multiAccountHelper->getProdStatusAttrForAcc($accountId);
            $ids = $this->getRequest()->getParam('selected');
            if(!$ids) {
                $collection = $this->filter->getCollection($this->_objectManager->create('Magento\Catalog\Model\Product')
                    ->getCollection());
                $ids = $collection->getAllIds();
            }
            //$ids = $this->filter->getCollection($this->catalogCollection->create()->addFieldToFilter($prodStatusAccAttr, 4))->getAllIds();
            if (!empty($ids)) {
                $productids = array_chunk($ids, 4);
                foreach ($productids as $prodChunkKey => $prodids) {
                    $productIdsToSync[$prodChunkKey] = array($accountId => $prodids);
                }
                $this->_session->setDeleteChunks($productIdsToSync);
                $resultPage = $this->resultPageFactory->create();
                $resultPage->setActiveMenu('Ced_GShop::product');
                $resultPage->getConfig()->getTitle()->prepend(__('Delete Product On Google Shopping'));
                return $resultPage;
            } else {
                $this->messageManager->addErrorMessage(__('No product available for Product Delete.'));
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('No product Assign on Profile'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }
}