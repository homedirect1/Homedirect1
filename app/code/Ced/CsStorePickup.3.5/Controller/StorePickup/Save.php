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
 * @package   Ced_CsStorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsStorePickup\Controller\StorePickup;

use Ced\CsStorePickup\Helper\Data;
use Ced\StorePickup\Model\StoreHourFactory;
use Ced\StorePickup\Model\StoreInfoFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Save
 * @package Ced\CsStorePickup\Controller\StorePickup
 */
class Save extends Action
{
    /**
     * @var Session
     */
    protected $_getSession;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var StoreFactory
     */
    protected $storesFactory;

    /**
     * @var StoreHourFactory
     */
    protected $storeHourFactory;

    /**
     * Save constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param Data $dataHelper
     * @param StoreInfoFactory $storesFactory
     * @param StoreHourFactory $storeHourFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Data $dataHelper,
        StoreInfoFactory $storesFactory,
        StoreHourFactory $storeHourFactory
    )
    {
        $this->_getSession = $customerSession;
        $this->dataHelper = $dataHelper;
        $this->storesFactory = $storesFactory;
        $this->storeHourFactory = $storeHourFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface|void
     */
    public function execute()
    {
        if ($this->dataHelper->isEnable() == "0") {
            $this->_redirect('*/*/index');
            return;
        }
        if (!$this->_getSession->getVendorId())
            return;
        if ($this->getRequest()->isPost()) {
            if ($this->getRequest()->getPost() != Null) {
                $data = $this->getRequest()->getPostValue();
                $vendor_id = $this->_getSession->getVendorId();

                $store_name = $data['store_name'];
                $store_manager_name = $data['store_manager_name'];
                $store_manager_email = $data['store_manager_email'];
                $store_address = $data['store_address'];
                $store_city = $data['store_city'];
                $store_country = $data['store_country'];
                $store_state = $data['store_state'];
                $store_zcode = $data['store_zcode'];
                $store_phone = $data['store_phone'];
                $shipping_price = isset($data['shipping_price']) ? $data['shipping_price'] : 0;
                $latitude = $data['latitude'];
                $longitude = $data['longitude'];
                /*Calculate Tha Latitute and Longitude*/

                $is_active = $data['is_active'];
                $storeHourInfo = array('Monday' => array('status' => $data['days_status']['mon'], 'start' => $data['start']['mon'], 'end' => $data['end']['mon'], 'interval' => $data['interval']['mon']),
                    'Tuesday' => array('status' => $data['days_status']['tue'], 'start' => $data['start']['tue'], 'end' => $data['end']['tue'], 'interval' => $data['interval']['tue']),
                    'Wednesday' => array('status' => $data['days_status']['wed'], 'start' => $data['start']['wed'], 'end' => $data['end']['wed'], 'interval' => $data['interval']['wed']),
                    'Thursday' => array('status' => $data['days_status']['thu'], 'start' => $data['start']['thu'], 'end' => $data['end']['thu'], 'interval' => $data['interval']['thu']),
                    'Friday' => array('status' => $data['days_status']['fri'], 'start' => $data['start']['fri'], 'end' => $data['end']['fri'], 'interval' => $data['interval']['fri']),
                    'Saturday' => array('status' => $data['days_status']['sat'], 'start' => $data['start']['sat'], 'end' => $data['end']['sat'], 'interval' => $data['interval']['sat']),
                    'Sunday' => array('status' => $data['days_status']['sun'], 'start' => $data['start']['sun'], 'end' => $data['end']['sun'], 'interval' => $data['interval']['sun']));
                $resultRedirect = $this->resultRedirectFactory->create();
                if ($data) {
                    $model = $this->storesFactory->create();
                    $id = $this->getRequest()->getParam('pickup_id');

                    try {
                        if ($id) {
                            $model->load($id);
                            $model->setData('vendor_id', $vendor_id);
                            $model->setData('store_name', $store_name);
                            $model->setData('store_manager_name', $store_manager_name);
                            $model->setData('store_manager_email', $store_manager_email);
                            $model->setData('store_address', $store_address);
                            $model->setData('store_city', $store_city);
                            $model->setData('store_country', $store_country);
                            $model->setData('store_state', $store_state);
                            $model->setData('store_zcode', $store_zcode);
                            $model->setData('latitude', $latitude);
                            $model->setData('longitude', $longitude);
                            $model->setData('store_phone', $store_phone);
                            $model->setData('is_active', $is_active);
                            $model->setData('shipping_price', $shipping_price);
                            $model->save();

                            $coll = $this->storeHourFactory->create();
                            $coll = $coll->getCollection()
                                ->addFieldToFilter('pickup_id', $id)
                                ->getData();

                            foreach ($coll as $val) {
                                $deleteObject = $this->storeHourFactory->create();
                                $deleteObject->load($val['id']);
                                $deleteObject->delete();
                            }

                            if (isset($storeHourInfo)) {
                                foreach ($storeHourInfo as $key => $val) {
                                    $storeObject = $this->storeHourFactory->create();
                                    $storeObject->setData('pickup_id', $id);
                                    $storeObject->setData('days', $key);
                                    $storeObject->setData('start', $val['start']);
                                    $storeObject->setData('end', $val['end']);
                                    $storeObject->setData('interval', $val['interval']);
                                    $storeObject->setData('status', $val['status']);
                                    $storeObject->save();
                                }
                            }
                        } else {
                            $model->setData('vendor_id', $vendor_id);
                            $model->setData('store_name', $store_name);
                            $model->setData('store_manager_name', $store_manager_name);
                            $model->setData('store_manager_email', $store_manager_email);
                            $model->setData('store_address', $store_address);
                            $model->setData('store_city', $store_city);
                            $model->setData('store_country', $store_country);
                            $model->setData('store_state', $store_state);
                            $model->setData('store_zcode', $store_zcode);
                            $model->setData('latitude', $latitude);
                            $model->setData('longitude', $longitude);
                            $model->setData('store_phone', $store_phone);
                            $model->setData('is_active', $is_active);
                            $model->setData('shipping_price', $shipping_price);
                            $model->save();
                            $lastID = $model->getPickupId();

                            if (isset($storeHourInfo)) {
                                foreach ($storeHourInfo as $key => $val) {
                                    $storeObject = $this->storeHourFactory->create();
                                    $storeObject->setData('pickup_id', $lastID);
                                    $storeObject->setData('days', $key);
                                    $storeObject->setData('start', $val['start']);
                                    $storeObject->setData('end', $val['end']);
                                    $storeObject->setData('interval', $val['interval']);
                                    $storeObject->setData('status', $val['status']);
                                    $storeObject->save();
                                }
                            }
                        }
                        $this->messageManager->addSuccessMessage(__('The store pickup information has been saved.'));
                        $this->_getSession->setFormData(false);
                        if ($this->getRequest()->getParam('back')) {
                            return $resultRedirect->setPath('*/*/edit', ['pickup_id' => $model->getId(), '_current' => true]);
                        }
                        return $resultRedirect->setPath('*/*/');
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    } catch (\RuntimeException $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    } catch (\Exception $e) {
                        $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the pickup store.'));
                    }

                    $this->_getSession()->setFormData($data);
                    return $resultRedirect->setPath('*/*/edit', ['pickup_id' => $this->getRequest()->getParam('pickup_id')]);
                }
                $this->_redirect('csstorepickup/storepickup/index');
                return;
            }
        }
    }
}
