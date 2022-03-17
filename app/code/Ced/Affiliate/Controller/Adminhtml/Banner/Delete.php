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

namespace Ced\Affiliate\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;

/**
 * Class Delete
 * @package Ced\Affiliate\Controller\Adminhtml\Banner
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\Affiliate\Model\AffiliateBannerFactory
     */
    protected $affiliateBannerFactory;

    /**
     * Delete constructor.
     * @param Action\Context $context
     * @param \Ced\Affiliate\Model\AffiliateBannerFactory $affiliateBannerFactory
     */
    public function __construct(
        Action\Context $context,
        \Ced\Affiliate\Model\AffiliateBannerFactory $affiliateBannerFactory
    )
    {
        $this->affiliateBannerFactory = $affiliateBannerFactory;
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

        $model = $this->affiliateBannerFactory->create()->load($this->getRequest()->getParam('id'));
        $model->delete();
        $this->messageManager->addSuccessMessage(__('Banner Deleted Successfully'));

        $resultPage = $this->resultRedirectFactory->create();
        $resultPage->setPath('affiliate/banner/index');
        return $resultPage;
    }
}