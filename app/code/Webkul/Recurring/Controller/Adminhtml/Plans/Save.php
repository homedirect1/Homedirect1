<?php
/**
 * Webkul Software.
 *
 * @category   Webkul
 * @package    Webkul_Recurring
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Controller\Adminhtml\Plans;

/**
 * Recurring Adminhtml Plans Save Controller
 */
class Save extends \Webkul\Recurring\Controller\Adminhtml\AbstractRecurring
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Webkul_Recurring::plans';

    /**
     * This function is reponsible for the saving of plans and terms information
     *
     * @return string
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();

        $planIds = $this->getRequest()->getParam('plan_ids', null);
        $planIds = $this->paypalHelper->getParsedString($planIds);
        $planIds = array_keys($planIds);
        foreach ($planIds as $productId) {
            $this->savePlans($productId);
        }
        $planIds = implode(",", $planIds);
        $id = isset($data['id']) ? $data['id'] : "";
        $resultRedirect = $this->resultRedirectFactory->create();
        
        $data['product_ids'] = $planIds;
        $result = $this->saveData($data);
        $id = $result['id'];
        
        if ($result['status'] == true) {
            $this->messageManager->addSuccess($result['message']);
        } else {
            $this->messageManager->addError($result['message']);
        }
        return $resultRedirect->setPath('*/*/edit', ['id' => $id ]);
    }

    /**
     * Save the plans
     *
     * @param integer $productId
     * @return void
     */
    private function savePlans($productId)
    {
        $productModel = $this->product->load($productId);
    }

    /**
     * This function saves the terms row wise
     *
     * @param array $row
     * @return void
     */
    private function saveTerms($row)
    {
        $time = date('Y-m-d H:i:s');
        $model = $this->terms;
        $row['update_time'] = $time;
        if ($row['id'] == 0 || $row['id'] == "") {
            $row['created_time'] = $time;
        }
        $model->setData($row);
        if ($row['id'] > 0) {
            $model->setId($row['id']);
        }
        $model->save();
    }

    /**
     * This function saves plans data
     *
     * @param array $wholeData
     * @return void
     */
    private function saveData($wholeData)
    {
        $result = [];
        $result['id'] = '';
        $time = date('Y-m-d H:i:s');
        try {
            $model = $this->plans;
            $wholeData['update_time'] = $time;
            if (isset($wholeData['id']) || ( $wholeData['id'] == '' && $wholeData['id'] == 0 )) {
                $wholeData['created_time'] = $time;
            }
            $model->setData($wholeData);
            if (isset($wholeData['id']) && $wholeData['id'] > 0) {
                $model->setId($wholeData['id']);
            }
            $model->save();
            $result['id'] = $model->getId();
            $result['status'] = true;
            $result['message'] = __("Subscription Type has been Saved successfully!");
        } catch (\Exception $e) {
            $result['status'] = false;
            $result['message'] = __("Something went wrong!");
        }
        return $result;
    }

    /**
     * This function validates the options existance
     *
     * @param array $rows
     * @param integer $id
     * @return void
     */
    private function validate($rows, $id)
    {
        $postIds = [];
        $collection = $this->terms->getCollection()->addFieldToFilter('plan_id', $id);
        foreach ($rows as $row) {
            $postIds[] = $row['id'];
        }
        $collection->addFieldToFilter('entity_id', ['nin' => $postIds]);
        $collection->walk('delete');
    }
}
