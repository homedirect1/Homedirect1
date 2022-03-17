<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSla\Controller\Vorders;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;

/**
 * Class MassCancel
 * @package Ced\CsSla\Controller\Vorders
 */
class MassCancel extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var \Ced\CsMarketplace\Model\VordersFactory
     */
    protected $vordersFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var \Ced\CsMarketplace\Model\VproductsFactory
     */
    protected $vproductsFactory;

    /**
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    protected $itemFactory;

    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Sales\Api\CreditmemoManagementInterface
     */
    protected $creditmemoManagement;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\CreditmemoSender
     */
    protected $creditmemoSender;

    /**
     * MassCancel constructor.
     * @param \Ced\CsMarketplace\Model\VordersFactory $vordersFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagement
     * @param \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory
     * @param \Magento\Sales\Model\Order\ItemFactory $itemFactory
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
     * @param \Magento\Sales\Api\CreditmemoManagementInterface $creditmemoManagement
     * @param \Magento\Sales\Model\Order\Email\Sender\CreditmemoSender $creditmemoSender
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlFactory $urlFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper
     * @param \Ced\CsMarketplace\Helper\Acl $aclHelper
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendor
     */
    public function __construct(
        \Ced\CsMarketplace\Model\VordersFactory $vordersFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory,
        \Magento\Sales\Model\Order\ItemFactory $itemFactory,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        \Magento\Sales\Api\CreditmemoManagementInterface $creditmemoManagement,
        \Magento\Sales\Model\Order\Email\Sender\CreditmemoSender $creditmemoSender,
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Session $customerSession,
        UrlFactory $urlFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        \Ced\CsMarketplace\Helper\Acl $aclHelper,
        \Ced\CsMarketplace\Model\VendorFactory $vendor
    )
    {
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

        $this->vordersFactory = $vordersFactory;
        $this->orderFactory = $orderFactory;
        $this->orderManagement = $orderManagement;
        $this->vproductsFactory = $vproductsFactory;
        $this->itemFactory = $itemFactory;
        $this->creditmemoLoader = $creditmemoLoader;
        $this->request = $context->getRequest();
        $this->creditmemoManagement = $creditmemoManagement;
        $this->creditmemoSender = $creditmemoSender;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Exception
     */
    public function execute()
    {

        $params = $this->getRequest()->getParams();
        if (isset($params['id'])) {
            $cancelIds = explode(',', $params['id']);
        } else {
            $cancelIds = [$params['order_id']];
        }

        if (isset($cancelIds)) {
            $successIds = [];
            $errorIds = [];
            foreach ($cancelIds as $id) {
                $vorderModel = $this->vordersFactory->create()->load($id);

                if ($vorderModel->getOrderStatus() != \Ced\CsSla\Helper\Data::STATE_CONFIRMED) {
                    if ($vorderModel->getOrderPaymentState() == \Ced\CsMarketplace\Model\Vorders::STATE_OPEN || $vorderModel->getOrderPaymentState() == \Ced\CsMarketplace\Model\Vorders::STATE_PAID) {
                        $incrementId = $vorderModel->getOrderId();
                        $order = $this->orderFactory->create()->load($incrementId, 'increment_id');
                        if ($order && $order->canCancel()) {
                            $vOrderCollection = $this->vordersFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('order_id', $incrementId)
                                ->getData();
                            if (sizeof($vOrderCollection) == 1) {
                                $this->orderManagement->cancel($order->getEntityId());
                                $isVendorOrder = true;
                            } else {
                                $isVendorOrder = false;
                                $allItems = $order->getAllItems();
                                foreach ($allItems as $item) {
                                    if (!($item->getQtyCanceled() > 0)) {
                                        $vendorId = $this->vproductsFactory->create()->getVendorIdByProduct($item->getProductId());
                                        if ($vendorId == $this->session->getVendorId()) {

                                            $itemModel = $this->itemFactory->create()->load($item->getItemId());
                                            $itemModel->setQtyCanceled($item->getQtyOrdered());
                                            $itemModel->save();
                                            $isVendorOrder = true;
                                        }
                                    }
                                }
                            }
                            if ($isVendorOrder) {

                                $vorderModel = $this->vordersFactory->create()->load($id);
                                $vorderModel->setOrderStatus(\Ced\CsSla\Helper\Data::STATE_CANCELLED);
                                $vorderModel->setOrderPaymentState(3);
                                $vorderModel->save();

                            }
                        } elseif ($order && $order->canCreditmemo()) {

                            $vorder = $this->vordersFactory->create()->load($id);
                            $vendorId = $vorder->getVendorId();
                            $orderId = $vorder->getOrder()->getId();

                            $this->creditmemoLoader->setOrderId($orderId);

                            $order = $this->orderFactory->create()->load($orderId);
                            $creditMemoItems = [];
                            $items = $order->getAllItems();
                            foreach ($items as $item) {
                                if ($item->getVendorId() == $vendorId) {
                                    $creditMemoItems['items'][$item->getItemId()]['qty'] = round($item->getQtyOrdered());
                                }
                            }

                            $this->creditmemoLoader->setCreditmemoId('');
                            $this->creditmemoLoader->setCreditmemo($creditMemoItems);
                            $this->creditmemoLoader->setInvoiceId('');
                            $creditmemo = $this->creditmemoLoader->load();

                            $this->request->setParam('creditmemo', $creditMemoItems);

                            try {
                                if ($creditmemo) {

                                    $error = [];
                                    if (!$creditmemo->isValidGrandTotal()) {
                                        $error[][$id] = "The credit memo\'s total must be positive.";

                                    }

                                    $data = [];
                                    $data['do_offline'] = 0;
                                    $data['send_email'] = 1;
                                    $this->creditmemoManagement->refund($creditmemo, (bool)$data['do_offline'], !empty($data['send_email']));

                                    if (!empty($data['send_email'])) {

                                        $this->creditmemoSender->send($creditmemo);
                                    }

                                    $vorderModel = $this->vordersFactory->create()->load($id);
                                    $vorderModel->setOrderStatus(\Ced\CsSla\Helper\Data::STATE_CANCELLED);
                                    $vorderModel->setOrderPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUNDED);
                                    $vorderModel->save();
                                } else {

                                }
                            } catch (\Exception $e) {

                                $this->messageManager->addErrorMessage($e->getMessage());

                            }
                        }

                        $successIds[] = $id;
                    }
                }
                if ($vorderModel->getOrderStatus() == \Ced\CsSla\Helper\Data::STATE_CONFIRMED) {
                    $errorIds[] = $id;
                }
            }

            if (count($successIds)) {
                $this->messageManager->addSuccessMessage('Orders cancelled successfully...');
                return $this->_redirect('csorder/*/index');
            }

            if (!empty($errorIds)) {

                $this->messageManager->addErrorMessage('Orders with id ' . implode(',', $errorIds) . ' can not be cancelled.');

                return $this->_redirect('csorder/*/index');
            }
        }
    }
}
