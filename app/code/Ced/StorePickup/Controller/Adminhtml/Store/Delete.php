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
 * @category  Ced
 * @package   Ced_StorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\StorePickup\Controller\Adminhtml\Store;

use Magento\Backend\App\Action;

/**
 * Class Delete
 * @package Ced\StorePickup\Controller\Adminhtml\Store
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\StorePickup\Model\StoreInfoFactory
     */
    protected $storeInfoFactory;

    /**
     * @var \Ced\StorePickup\Model\StoreHourFactory
     */
    protected $storeHourFactory;

    /**
     * Delete constructor.
     * @param \Ced\StorePickup\Model\StoreInfoFactory $storeInfoFactory
     * @param \Ced\StorePickup\Model\StoreHourFactory $storeHourFactory
     * @param Action\Context $context
     */
    public function __construct(
        \Ced\StorePickup\Model\StoreInfoFactory $storeInfoFactory,
        \Ced\StorePickup\Model\StoreHourFactory $storeHourFactory,
        Action\Context $context
    )
    {
        $this->storeInfoFactory = $storeInfoFactory;
        $this->storeHourFactory = $storeHourFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('pickup_id');
        $model = $this->storeInfoFactory->create();
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $model->load($id);
            $model->delete();
            $coll = $this->storeHourFactory->create();
            $coll = $coll->getCollection()
                ->addFieldToFilter('pickup_id', $id)
                ->getData();
            foreach ($coll as $val) {
                $deleteObject = $this->storeHourFactory->create();
                $deleteObject->load($val['id']);
                $deleteObject->delete();
            }
            $this->messageManager->addSuccessMessage(__('Deleted Successfully'));
            return $resultRedirect->setPath('*/store/');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting the pickup store.'));
        }
        return $resultRedirect->setPath('*/*/delete', ['pickup_id' => $this->getRequest()->getParam('pickup_id')]);
    }
}
