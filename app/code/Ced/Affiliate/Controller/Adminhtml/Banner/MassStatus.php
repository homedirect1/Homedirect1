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

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Ced\Affiliate\Model\ResourceModel\AffiliateBanner\CollectionFactory;

/**
 * Class MassStatus
 * @package Ced\Affiliate\Controller\Adminhtml\Banner
 */
class MassStatus extends AbstractMassAction
{
    /**
     * @var \Ced\Affiliate\Model\AffiliateBannerFactory
     */
    protected $affiliateBannerFactory;

    /**
     * MassStatus constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param \Ced\Affiliate\Model\AffiliateBannerFactory $affiliateBannerFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Ced\Affiliate\Model\AffiliateBannerFactory $affiliateBannerFactory
    )
    {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->affiliateBannerFactory = $affiliateBannerFactory;
    }

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $changeStatus = 0;
        foreach ($collection->getAllIds() as $_accountId) {
            $_account = $this->affiliateBannerFactory->create()->load($_accountId);
            $_account->setStatus($this->getRequest()->getParam('status'));
            $_account->setApprove($this->getRequest()->getParam('status'));
            $_account->save();
            $changeStatus++;
        }

        if ($changeStatus) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $changeStatus));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('affiliate/banner/index');

        return $resultRedirect;
    }

}