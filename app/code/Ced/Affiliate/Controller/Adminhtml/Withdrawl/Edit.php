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

namespace Ced\Affiliate\Controller\Adminhtml\Withdrawl;

use Magento\Backend\App\Action;

/**
 * Class Edit
 * @package Ced\Affiliate\Controller\Adminhtml\Withdrawl
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var
     */
    protected $_fileCsv;

    /**
     * @var \Ced\Affiliate\Model\AffiliateWithdrawlFactory
     */
    protected $affiliateWithdrawlFactory;

    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Ced\Affiliate\Model\AffiliateWithdrawlFactory $affiliateWithdrawlFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ced\Affiliate\Model\AffiliateWithdrawlFactory $affiliateWithdrawlFactory
    )
    {
        $this->_coreRegistry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->affiliateWithdrawlFactory = $affiliateWithdrawlFactory;
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
        $withdrwalid = $this->getRequest()->getParam('id');
        $withdrwalData = $this->affiliateWithdrawlFactory->create()->load($withdrwalid);
        $this->_coreRegistry->register('withdrawl_request', $withdrwalData);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ced_AffiliateWithdrawl');
        $resultPage->addBreadcrumb(__('CMS'), __('CMS'));
        $resultPage->addBreadcrumb(__('Withdrawal'), __('Request'));
        $resultPage->getConfig()->getTitle()->prepend(__('Withdrawal Request'));
        return $resultPage;
    }
}