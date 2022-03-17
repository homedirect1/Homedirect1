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

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ced\Affiliate\Model\ResourceModel\DiscountDenomination\CollectionFactory;

/**
 * Class MassDelete
 * @package Ced\Affiliate\Controller\Adminhtml\Discount
 */
class MassDelete extends AbstractMassAction
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Ced\Affiliate\Model\DiscountDenominationFactory
     */
    protected $discountDenominationFactory;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param \Ced\Affiliate\Model\DiscountDenominationFactory $discountDenominationFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Ced\Affiliate\Model\DiscountDenominationFactory $discountDenominationFactory
    )
    {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->discountDenominationFactory = $discountDenominationFactory;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    protected function massAction(AbstractCollection $collection)
    {
        $changeStatus = 0;
        foreach ($collection->getAllIds() as $item) {
            $discount = $this->discountDenominationFactory->create()->load($item);
            $discount->delete();
            $changeStatus++;
        }

        if ($changeStatus) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were Deleted.', $changeStatus));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('affiliate/discount/denomination');
    }
}