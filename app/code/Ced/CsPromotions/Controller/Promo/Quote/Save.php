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

namespace Ced\CsPromotions\Controller\Promo\Quote;

use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;

/**
 * Class Save
 * @package Ced\CsPromotions\Controller\Promo\Quote
 */
class Save extends \Ced\CsPromotions\Controller\Promo\Quote
{
    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $marketplaceHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $_dateFilter;

    /**
     * Save constructor.
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
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
        \Magento\Backend\Model\Session $backendSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
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
        $this->marketplaceHelper = $csmarketplaceHelper;
        $this->logger = $logger;
        $this->_dateFilter = $dateFilter;

        parent::__construct(
            $ruleFactory,
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
     * Promo quote save action
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {

        $vendorId = $this->_getSession()->getVendorId();
        if (!$vendorId) {
            return;
        }

        if ($this->getRequest()->getPostValue()) {
            try {
                /** @var $model \Magento\SalesRule\Model\Rule */
                $model = $this->ruleFactory->create();
                $data = $this->getRequest()->getPostValue();

                $filterValues = ['from_date' => $this->_dateFilter];
                if ($this->getRequest()->getParam('to_date')) {
                    $filterValues['to_date'] = $this->_dateFilter;
                }

                $inputFilter = new \Zend_Filter_Input(
                    $filterValues,
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('rule_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong rule is specified.'));
                    }
                }

                $session = $this->backendSession;

                $validateResult = $model->validateData(new \Magento\Framework\DataObject($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addErrorMessage($errorMessage);
                    }
                    $session->setPageData($data);
                    $this->_redirect('cspromotions/*/edit', ['id' => $model->getId()]);
                    return;
                }

                if (isset(
                        $data['simple_action']
                    ) && $data['simple_action'] == 'by_percent' && isset(
                        $data['discount_amount']
                    )
                ) {
                    $data['discount_amount'] = min(100, $data['discount_amount']);
                }
                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                if (isset($data['rule']['actions'])) {
                    $data['actions'] = $data['rule']['actions'];
                }
                $data['is_advanced'] = 1;
                $data['times_used'] = 0;
                unset($data['rule']);
                unset($data['created_at']);
                $name = $this->_getSession()->getVendor()['public_name'];
                $model->loadPost($data);
                $model->setVendorId($vendorId);
                $model->setVendorName($name);

                $useAutoGeneration = (int)(
                    !empty($data['use_auto_generation']) && $data['use_auto_generation'] !== 'false'
                );
                $model->setUseAutoGeneration($useAutoGeneration);

                $session->setPageData($model->getData());
                if (isset($data['from_date'])) {
                    $model->setFromDate($data['from_date']);
                }

                if (isset($data['to_date'])) {
                    $model->setToDate($data['to_date']);
                }
                if (!$id) {
                    $AdminApproval = ($this->marketplaceHelper->getStoreConfig('ced_csmarketplace/vpromotions/cart_price_approval', 0));

                    if ($AdminApproval == 1) {
                        $approval = 0;
                    } else {
                        $approval = 1;
                    }
                    $model->setIsApprove($approval);
                    $model->setIsActive(0);
                } else {
                    if (!$model->getIsApprove()) {
                        $model->setIsActive(0);
                    }
                }

                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the rule.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('cspromotions/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('cspromotions/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $id = (int)$this->getRequest()->getParam('rule_id');
                if (!empty($id)) {
                    $this->_redirect('cspromotions/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('cspromotions/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __($e->getMessage())
                );
                $this->logger->critical($e);
                $this->backendSession->setPageData($data);
                $this->_redirect('cspromotions/*/edit', ['id' => $this->getRequest()->getParam('rule_id')]);
                return;
            }
        }
        $this->_redirect('cspromotions/*/');
    }
}
