<?php

namespace Knowband\Mobileappbuilder\Controller\Adminhtml\Mobileappbuilder;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\LayoutFactory;

class PaymentAjax extends \Magento\Framework\App\Action\Action
{
    protected $sp_resultRawFactory;
    protected $sp_request;
    protected $sp_helper;
    protected $sp_scopeConfig;
    protected $inlineTranslation;
    protected $sp_transportBuilder;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultRawFactory,
        \Knowband\Mobileappbuilder\Model\Payment $paymentModel,
        \Knowband\Mobileappbuilder\Helper\Data $helper, 
        \Magento\Framework\Filesystem $fileSystem,
        LayoutFactory $viewLayoutFactory
    ) {
        parent::__construct($context);
        $this->sp_resultRawFactory = $resultRawFactory;
        $this->paymentModel = $paymentModel;
        $this->helper = $helper;
        $this->_filesystem = $fileSystem;
        $this->_viewLayoutFactory = $viewLayoutFactory;
    }

    public function execute() {
        if ($this->getRequest()->isPost()) {
            $post_data = $this->getRequest()->getPost();
            if (isset($post_data['method']) && $post_data['method'] == 'edit') {
                $block = $this->_viewLayoutFactory->create()->createBlock('Knowband\Mobileappbuilder\Block\Adminhtml\PaymentMethod');
                $this->getResponse()->appendBody($block->toHtml());
            }elseif (isset($post_data['payment_method'])) {
                $error = false;
                $msg = '';
                $data = $post_data['payment_method'];
                $payment_id = $data['payment_id'];
                $payment_name = $data['payment_method_name'];
                $status = $data['status'];
                $client_id = isset($data['client_id']) ? $data['client_id'] : "";
                $payment_mode = isset($data['payment_mode']) ? $data['payment_mode'] : "live";
                $is_default = 'no';
                $other_info = '';

                $values_data = array(
                    'payment_method_mode' => $payment_mode,
                    'client_id' => $client_id,
                    'is_default' => $is_default,
                    'other_info' => $other_info,
                );

                $values = json_encode($values_data);

                try {
                    $payment_model = $this->paymentModel->load((int)$payment_id);
                    $payment_model->setStatus($status);
                    $payment_model->setKbPaymentName($payment_name);
                    $payment_model->setValues($values);
                    $payment_model->setDateUpd($this->helper->getDate());
                    $payment_model->save();
                    $payment_model->unsetData();
                    $msg = __('Settings saved successfully');
                } catch (\Exception $ex) {
                    $error = true;
                    $msg = $ex->getMessage();
                }
                $jsondata = array('error' => $error, 'msg' => $msg);
                $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                $resultJson->setData($jsondata);
                return $resultJson;
            }else{
                $block = $this->_viewLayoutFactory->create()->createBlock('Knowband\Mobileappbuilder\Block\Adminhtml\Tab\PaymentMethods');
                $this->getResponse()->appendBody($block->toHtml());
            }
        }
    }
}
