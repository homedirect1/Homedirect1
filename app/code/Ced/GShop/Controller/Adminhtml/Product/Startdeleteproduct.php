<?php
/**
 * Created by PhpStorm.
 * User: cedcoss
 * Date: 4/3/19
 * Time: 6:37 PM
 */

namespace Ced\GShop\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Ced\GShop\Helper\GXpress;
use Ced\GShop\Helper\Data;
use Ced\GShop\Helper\Logger;
use Magento\TestFramework\Event\Magento;

class Startdeleteproduct extends Action
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;
    /**
     * @var JsonFactory
     */
    public $resultJsonFactory;
    /**
     * @var GXpress
     */
    public $gxpressHelper;
    /**
     * @var Data
     */
    public $dataHelper;
    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var \Ced\GShop\Helper\MultiAccount
     */
    protected $multiAccountHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /** @var \Ced\GShop\Helper\GXpresslib $gxpressLibHelper */
    public $gxpressLibHelper;

    /**
     * Startdeleteproduct constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param PageFactory $resultPageFactory
     * @param Data $dataHelper
     * @param Logger $logger
     * @param GXpress $gxpressHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Ced\GShop\Helper\MultiAccount $multiAccountHelper
     * @param \Ced\GShop\Helper\GXpresslib $gxpressLibHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory,
        Data $dataHelper,
        Logger $logger,
        GXpress $gxpressHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Ced\GShop\Helper\MultiAccount $multiAccountHelper,
        \Ced\GShop\Helper\GXpresslib $gxpressLibHelper)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->gxpressHelper = $gxpressHelper;
        $this->dataHelper = $dataHelper;
        $this->logger = $logger;
        $this->_coreRegistry = $coreRegistry;
        $this->multiAccountHelper = $multiAccountHelper;
        $this->gxpressLibHelper = $gxpressLibHelper;
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $message = [];
        $message['error'] = "";
        $message['success'] = "";
        $error = $successids ='';
        $finalXml = '';
        $key = $this->getRequest()->getParam('index');
        $totalChunk = $this->_session->getDeleteChunks();
        $index = $key + 1;
        if (count($totalChunk) <= $index) {
            $this->_session->unsDeleteChunks();
        }
        try {
            if (isset($totalChunk[$key])) {
                $ids = $totalChunk[$key];
                foreach ($ids as $accountId => $prodIds) {
                    if (!is_array($prodIds)) {
                        $prodIds[] = $prodIds;
                    }
                    if ($this->_coreRegistry->registry('google_account')) {
                        $this->_coreRegistry->unregister('google_account');
                    }
                    $this->multiAccountHelper->getAccountRegistry($accountId);
                    $this->dataHelper->updateAccountVariable();
                    $this->gxpressHelper->updateAccountVariable();
                    foreach ($prodIds as $id) {
                        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')
                            ->load($id);
                        if($product->getTypeId() == 'configurable') {
                            $children = $product->getTypeInstance()->getUsedProducts($product);
                            foreach ($children as $child) {
                                $finaldata = $this->gxpressLibHelper->deleteRequest($child->getId());
                                if (isset($finaldata['type']) && $finaldata['type'] == 'success') {
                                    $this->dataHelper->updateStatus($child->getId(), 5);
                                    $finalXml .= $finaldata['data'].' Deleted Successfully. ';
                                    $this->dataHelper->updateStatus($id, 5);
                                } else {
                                    $error .= $finaldata['data'];
                                    $this->dataHelper->updateStatus($child->getId(), 8);
                                    $this->dataHelper->updateStatus($id, 8);
                                }
                            }
                        } else {
                            $finaldata = $this->gxpressLibHelper->deleteRequest($id);
                            if (isset($finaldata['type']) && $finaldata['type'] == 'success') {
                                $this->dataHelper->updateStatus($id, 5);
                                $finalXml .= $finaldata['data'].' Deleted Successfully. ';
                            } else {
                                $error .= $finaldata['data'];
                                $this->dataHelper->updateStatus($id, 8);
                            }
                        }
                    }
                    if ($error) {
                        $message['error'] = $error;
                    }
                }
            } else {
                $message['error'] = $message['error']." included Product(s) data not found. ";
            }
            if($finalXml) {
                $message['success'] = $finalXml;
            }
        } catch (\Exception $e) {
            $message['error'] = $e->getMessage();
            $this->logger->addError($message['error'], ['path' => __METHOD__]);
        }
        return $resultJson->setData($message);
    }
}
