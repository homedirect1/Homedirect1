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

namespace Ced\Affiliate\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action;

/**
 * Class Disapprove
 * @package Ced\Affiliate\Controller\Adminhtml\Manage
 */
class Disapprove extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * @var \Ced\Affiliate\Helper\Email
     */
    protected $emailHelper;

    /**
     * Disapprove constructor.
     * @param Action\Context $context
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Ced\Affiliate\Helper\Email $emailHelper
     */
    public function __construct(
        Action\Context $context,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Ced\Affiliate\Helper\Email $emailHelper
    )
    {
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->emailHelper = $emailHelper;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return true;
    }


    /**
     * Edit grid record
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {

        $model = $this->affiliateAccountFactory->create()->load($this->getRequest()->getParam('id'));
        $model->setStatus(\Ced\Affiliate\Model\AffiliateAccount::DISAPPROVE);
        $model->setApprove(\Ced\Affiliate\Model\AffiliateAccount::DISAPPROVE);
        $model->save();
        $this->emailHelper->sendConfirmationEmail($model, \Ced\Affiliate\Model\AffiliateAccount::DISAPPROVE);
        $this->emailHelper->sendAdminNotifyStatusMail($model, \Ced\Affiliate\Model\AffiliateAccount::DISAPPROVE);
        $this->messageManager->addSuccessMessage(__('Account Has Been DisApproved'));

        $resultPage = $this->resultRedirectFactory->create();
        $resultPage->setPath('affiliate/manage/account');
        return $resultPage;
    }
}