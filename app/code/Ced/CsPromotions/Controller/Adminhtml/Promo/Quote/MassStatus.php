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
 * @package     Ced_CsPromotions
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsPromotions\Controller\Adminhtml\Promo\Quote;

use Magento\Backend\App\Action;

/**
 * Class MassStatus
 * @package Ced\CsPromotions\Controller\Adminhtml\Promo\Quote
 */
class MassStatus extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor
{
    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * MassStatus constructor.
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
     * @param Action\Context $context
     */
    public function __construct(
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        Action\Context $context
    )
    {
        $this->ruleFactory = $ruleFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Exception
     */
    public function execute()
    {
        $rule_id = $this->getRequest()->getParam('rule_id');
        $status = $this->getRequest()->getParam('status', '');
        $message = '';
        $model = $this->ruleFactory->create()->load($rule_id);
        if ($status == "approved") {
            $message = 'Approved';
            $model->setData('is_approve', 1);
            $model->setData('is_active', 1);
        } else {
            $message = 'Disapproved';
            $model->setData('is_approve', 0);
            $model->setData('is_active', 0);
        }

        $model->save();
        $this->messageManager->addSuccessMessage(__('Cart Rule %1 Successfully.', $message));
        return $this->_redirect('sales_rule/promo_quote/index');
    }
}
