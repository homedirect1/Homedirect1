<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Controller\Adminhtml\Discount;

use Magento\Backend\App\Action;

/**
 * Class Save
 * @package Ced\Affiliate\Controller\Adminhtml\Discount
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Ced\Affiliate\Model\DiscountDenominationFactory
     */
    protected $discountDenominationFactory;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param \Ced\Affiliate\Model\DiscountDenominationFactory $discountDenominationFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Backend\Model\Session $session,
        \Ced\Affiliate\Model\DiscountDenominationFactory $discountDenominationFactory
    )
    {
        $this->session = $session;
        $this->discountDenominationFactory = $discountDenominationFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->discountDenominationFactory->create();
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            $model->setData('rule_name', $data["rule_name"]);
            $model->setData('discount_amount', $data ['discount_amount']);
            $model->setData('cart_amount', $data ['cart_amount']);
            $model->setData('status', $data ['status']);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('Discount Rule successfully saved.'));
                $this->session->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/discount/edit', [
                        'grid_record_id' => $model->getId(),
                        '_current' => true
                    ]);
                }
                return $resultRedirect->setPath('*/discount/denomination');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/discount/denomination', [
                'id' => $this->getRequest()->getParam('id')
            ]);
        }
        return $resultRedirect->setPath('*/discount/denomination');
    }
}