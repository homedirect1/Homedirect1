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
namespace Webkul\Recurring\Controller\Adminhtml\Duration;

/**
 * Recurring Adminhtml terms Save Controller
 */
class Save extends \Webkul\Recurring\Controller\Adminhtml\AbstractRecurring
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Webkul_Recurring::term';

    /**
     * This function is reponsible for the saving of plans and terms information
     *
     * @return string
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $id = isset($data['id']) ? $data['id'] : "";
        $resultRedirect = $this->resultRedirectFactory->create();
        
        $this->updateSubscriptionType($data);
        $data['id'] = $id;
        $result = $this->saveTerms($data);
        
        $id = $result['id'];
        
        if ($result['status'] == true) {
            $this->messageManager->addSuccess($result['message']);
        } else {
            $this->messageManager->addError($result['message']);
            return $resultRedirect->setPath('*/*/new');
        }
        return $resultRedirect->setPath('*/*/edit', ['id' => $id ]);
    }

    /**
     * This function is used to filter the plan type as per duration
     *
     * @param array $data
     * @return void
     */
    private function updateSubscriptionType($data)
    {
        $coll = $this->plans->getCollection();
        $coll->addFieldToFilter("type", $data['id']);
        
        foreach ($coll as $model) {
            $this->updateType($data["sort_order"], $model);
        }
    }

    /**
     * This function is used to update the sort order in all product plans
     *
     * @param integer $sortOrder
     * @param object $model
     * @return void
     */
    private function updateType($sortOrder, $model)
    {
        $model->setSortOrder($sortOrder);
        $model->setId($model->getId())->save();
    }

    /**
     * This function saves the terms row wise
     *
     * @param array $row
     * @return void
     */
    private function saveTerms($row)
    {
        try {
            $time = date('Y-m-d H:i:s');
            $model = $this->terms;
            if (isset($row['duration'])) {
                $collection = $model->getCollection()
                            ->addFieldToFilter('duration', $row['duration'])
                            ->addFieldToFilter('entity_id', ["neq" => $row['id']]);
                if ($collection->getSize()) {
                    return [
                        'status' => false,
                        'id' => ($row['id']) ? $row['id'] : '',
                        'message' => __('This Duration already available')
                    ];
                }
            }
            
            $row['update_time'] = $time;
            if ($row['id'] == 0 || $row['id'] == "") {
                $row['created_time'] = $time;
            }
            $model->setData($row);
            if ($row['id'] > 0) {
                $model->setId($row['id']);
            }
            $model->save();
            return [
                'status' => true,
                'id' => $model->getId(),
                'message' => __('Record Saved Successfully')
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'id' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * This function validates the options existence
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
