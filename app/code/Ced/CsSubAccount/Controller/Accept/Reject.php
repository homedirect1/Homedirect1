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
 * @package     Ced_CsSubAccount
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Controller\Accept;

use Magento\Backend\App\Action\Context;

/**
 * Class Reject
 * @package Ced\CsSubAccount\Controller\Accept
 */
class Reject extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Ced\CsSubAccount\Model\AccountStatusFactory
     */
    protected $accountStatusFactory;

    /**
     * Reject constructor.
     * @param \Ced\CsSubAccount\Model\AccountStatusFactory $accountStatusFactory
     * @param Context $context
     */
    public function __construct(
        \Ced\CsSubAccount\Model\AccountStatusFactory $accountStatusFactory,
        Context $context
    )
    {
        parent::__construct($context);
        $this->accountStatusFactory = $accountStatusFactory;
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $model = $this->accountStatusFactory->create()->load($this->getRequest()->getParam('cid'));
        if ($model['status'] == \Ced\CsSubAccount\Model\AccountStatus::ACCOUNT_STATUS_ACCEPTED)
            $msg = 'You have already accepted the seller request';
        if ($model['status'] == \Ced\CsSubAccount\Model\AccountStatus::ACCOUNT_STATUS_REJECTED)
            $msg = 'You have rejected the seller request';

        try {
            if ($model['status'] == \Ced\CsSubAccount\Model\AccountStatus::ACCOUNT_STATUS_PENDING) {
                $model->setStatus(\Ced\CsSubAccount\Model\AccountStatus::ACCOUNT_STATUS_REJECTED);
                $model->save();
                $msg = 'You have successfully rejected the seller request';
            }

        } catch (\Exception $e) {
            $msg = $e->getMessage();
        }

        $this->messageManager->addSuccessMessage(__($msg));
        $this->_redirect("/");
        return;
    }


}
