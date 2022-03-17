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

namespace Ced\CsPromotions\Controller\Promo\Catalog;

use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;

/**
 * Class Save
 * @package Ced\CsPromotions\Controller\Promo\Catalog
 */
class Save extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $csmarketplaceHelper;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @var \Magento\CatalogRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\CatalogRule\Model\FlagFactory
     */
    protected $flagFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Save constructor.
     * @param \Magento\CatalogRule\Model\RuleFactory $ruleFactory
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\CatalogRule\Model\FlagFactory $flagFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlFactory $urlFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper
     * @param \Ced\CsMarketplace\Helper\Acl $aclHelper
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendor
     */
    public function __construct(
        \Magento\CatalogRule\Model\RuleFactory $ruleFactory,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\CatalogRule\Model\FlagFactory $flagFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Session $customerSession,
        UrlFactory $urlFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        \Ced\CsMarketplace\Helper\Acl $aclHelper,
        \Ced\CsMarketplace\Model\VendorFactory $vendor
    )
    {
        $this->ruleFactory = $ruleFactory;
        $this->backendSession = $backendSession;
        $this->flagFactory = $flagFactory;
        $this->logger = $logger;

        parent::__construct(
            $context,
            $resultPageFactory,
            $customerSession,
            $urlFactory,
            $registry,
            $jsonFactory,
            $csmarketplaceHelper,
            $aclHelper,
            $vendor
        );
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $vendorId = $this->_getSession()->getVendorId();
        if (!$vendorId) {
            return;
        }
        if ($this->getRequest()->getPostValue()) {
            try {
                /** @var \Magento\CatalogRule\Model\Rule $model */
                $model = $this->ruleFactory->create();
                $this->_eventManager->dispatch(
                    'adminhtml_controller_catalogrule_prepare_save',
                    ['request' => $this->getRequest()]
                );
                $data = $this->getRequest()->getPostValue();

                $id = $this->getRequest()->getParam('rule_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new LocalizedException(__('Wrong rule specified.'));
                    }
                }
                $validateResult = $model->validateData(new \Magento\Framework\DataObject($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addErrorMessage($errorMessage);
                    }
                    $this->_getSession()->setPageData($data);
                    $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                    return;
                }

                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
                $name = $this->_getSession()->getVendor()['public_name'];
                $model->loadPost($data);
                $model->setVendorId($vendorId);
                $model->setVendorName($name);

                if (!$id) {
                    $AdminApproval = ($this->csmarketplaceHelper->getStoreConfig('ced_csmarketplace/vpromotions/catlog_price_approval', 0));

                    if ($AdminApproval == 1) {
                        $approval = 0;
                        $model->setIsActive(0);
                    } else {
                        $approval = 1;
                    }
                    $model->setIsApprove($approval);
                } else {
                    if (!$model->getIsApprove()) {
                        $model->setIsActive(0);
                    }
                }

                $this->backendSession->setPageData($model->getData());

                $model->save();

                $this->messageManager->addSuccessMessage(__('You saved the rule.'));

                $this->backendSession->setPageData(false);
                if ($this->getRequest()->getParam('auto_apply')) {
                    $this->getRequest()->setParam('rule_id', $model->getId());
                    $this->_forward('applyRules');
                } else {
                    if ($model->isRuleBehaviorChanged()) {
                        $this->flagFactory->create()
                            ->loadSelf()
                            ->setState(1)
                            ->save();
                    }
                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                        return;
                    }
                    $this->_redirect('*/*/');
                }
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the rule data. Please review the error log.')
                );
                $this->logger->critical($e);
                $this->backendSession->setPageData($data);
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('rule_id')]);
                return;
            }
        }
        $this->_redirect('*/*/');
    }
}
