<?php
namespace Ced\GShop\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;

class DeleteFeed extends Action
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

    const ADMIN_RESOURCE = 'Ced_GShop::GXpress';

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
            $ids = '';
            if(!$ids) {
                $collection = $this->filter->getCollection($this->_objectManager->create(\Ced\GShop\Model\Feeds::class)
                    ->getCollection());
                $ids = $collection->getAllIds();
            }
            if (!empty($ids)) {
                $response = $collection->addFieldToFilter('id',$ids)->walk('delete');
                if($response) {
                    $this->messageManager->addSuccessMessage(__(implode(',',$ids) .' Feeds are deleted'));
                } else {
                    $this->messageManager->addErrorMessage(__(implode(',',$ids) .' Feeds having issue while we trying to delete'));
                }
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            } else {
                $this->messageManager->addErrorMessage(__('No Feed selected for Delete.'));
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }
}