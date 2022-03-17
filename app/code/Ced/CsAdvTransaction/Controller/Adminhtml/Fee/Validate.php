<?php
namespace Ced\CsAdvTransaction\Controller\Adminhtml\Fee;

use Magento\Backend\App\Action\Context;

/**
 * Class Validate
 * @package Ced\CsAdvTransaction\Controller\Adminhtml\Fee
 */
class Validate extends \Magento\Backend\App\Action
{
    /**
     * Validate constructor.
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory
    )
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->feeFactory = $feeFactory;
    }

    /**
     * @param $response
     * @return void|null
     */
    protected function _validateField($response)
    {
        $customer = null;
        $errors = [];

        try {
            $postdata = $this->getRequest()->getPostValue('fee_fieldset');

            if (!is_array($postdata)) {
                return;
            }
            $fees = $this->feeFactory->create()->load($postdata['field_code'], 'field_code')->getData();

            /*new : check if code exist */
            if (count($fees) > 0 && !isset($postdata['id']) && $fees['field_code'] == $postdata['field_code']) {
                $messages = __('Field Code already exist');
                $errors[] = $messages;
                /* $response->setMessages($messages);
                 $response->setError(1);
                 return;*/
            }

            /*edit : check if code exist */
            if (count($fees) > 0 && isset($postdata['id']) && $fees['id'] != $postdata['id']) {
                $messages = __('Field Code already exist');
                $errors[] = $messages;
                /*$response->setMessages($messages);
                $response->setError(1);
                return;*/
            }
        } catch (\Magento\Framework\Validator\Exception $exception) {
            /* @var $error Error */
            foreach ($exception->getMessages(\Magento\Framework\Message\MessageInterface::TYPE_ERROR) as $error) {
                $errors[] = $error->getText();
            }
        }

        if ($errors) {
            $messages = $response->hasMessages() ? $response->getMessages() : [];
            foreach ($errors as $error) {
                $messages[] = $error;
            }
            $response->setMessages($messages);
            $response->setError(1);
        }

        return $customer;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(0);

        $this->_validateField($response);
        $resultJson = $this->resultJsonFactory->create();
        if ($response->getError()) {
            $response->setError(true);
            $response->setMessages($response->getMessages());
        }

        $resultJson->setData($response);
        return $resultJson;
    }
}
