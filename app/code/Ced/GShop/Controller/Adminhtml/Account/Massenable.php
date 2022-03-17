<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package  Ced_GShop
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GShop\Controller\Adminhtml\Account;

use Ced\GShop\Model\Accounts;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ced\GShop\Model\ResourceModel\Accounts\CollectionFactory;

/**
 * Class Massenable
 * @package Ced\GShop\Controller\Adminhtml\Account
 */
class Massenable extends Action
{
    /**
     * @var CollectionFactory
     */
    public $accounts;
    /**
     * @var Filter
     */
    public $filter;

    /** @var Accounts $accModel */
    public $accModel;

    const ADMIN_RESOURCE = 'Ced_GShop::GXpress';

    /**
     * Massenable constructor.
     * @param Context $context
     * @param CollectionFactory $accounts
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        CollectionFactory $accounts,
        Accounts $accModel,
        Filter $filter
    ) {
        parent::__construct($context);
        $this->accounts = $accounts;
        $this->filter = $filter;
        $this->accModel = $accModel;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $ids = $this->filter->getCollection($this->accounts->create())->getAllIds();
        if (!empty($ids)) {
            $collection = $this->accounts->create()->addFieldToFilter('id', ['in' => $ids]);
            if (isset($collection) and $collection->getSize() > 0) {
                foreach ($ids as $id) {
                    $this->accModel->load($id)->setAccountStatus(1)->save();
                }
                $this->messageManager->addSuccessMessage(__($collection->getSize(). ' Account(s) Enabled Successfully'));
            } else {
                $this->messageManager->addErrorMessage(__('No product available for Enable.'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('No product available for Enable.'));

        }
        return $this->_redirect('gxpress/account/index');
    }
}
