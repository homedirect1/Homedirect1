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
 * Class Delete
 * @package Ced\Affiliate\Controller\Adminhtml\Manage
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * Delete constructor.
     * @param Action\Context $context
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     */
    public function __construct(
        Action\Context $context,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
    )
    {
        parent::__construct($context);
        $this->affiliateAccountFactory = $affiliateAccountFactory;
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
        $model->delete();
        $this->messageManager->addSuccessMessage(__('Account Deleted Successfully'));

        $resultPage = $this->resultRedirectFactory->create();
        $resultPage->setPath('affiliate/manage/account');
        return $resultPage;
    }
}