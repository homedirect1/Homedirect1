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
 * @package     Ced_CsAdvTransaction
 * @author     CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Controller\Adminhtml\Fee;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class InlineEdit
 * @package Ced\CsAdvTransaction\Controller\Adminhtml\Fee
 */
class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \Ced\CsAdvTransaction\Model\FeeFactory
     */
    protected $feeFactory;

    /**
     * InlineEdit constructor.
     * @param \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory
     * @param JsonFactory $jsonFactory
     * @param Context $context
     */
    public function __construct(
        \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory,
        JsonFactory $jsonFactory,
        Context $context
    )
    {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->feeFactory = $feeFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);

            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $feeId) {
                    /** @var \Ced\CsAdvTransaction\Model\Fee $fee */
                    $fee = $this->feeFactory->create()->load($feeId);

                    try {

                        $fee->setData(array_merge($fee->getData(), $postItems[$feeId]));
                        $fee->save($fee);
                    } catch (\Exception $e) {
                        $messages[] = __('Something Went Wrong');
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

}
