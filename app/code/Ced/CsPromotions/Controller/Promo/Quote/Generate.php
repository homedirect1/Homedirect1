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
 * Class Generate
 * @package Ced\CsPromotions\Controller\Promo\Quote
 */
class Generate extends \Ced\CsPromotions\Controller\Promo\Quote
{
    /**
     * @var \Magento\SalesRule\Model\Coupon\Massgenerator
     */
    protected $massgenerator;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;
    protected $_dateFilter;

    public function __construct(
        \Magento\SalesRule\Model\Coupon\Massgenerator $massgenerator,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Session $customerSession,
        UrlFactory $urlFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        \Ced\CsMarketplace\Helper\Acl $aclHelper,
        \Ced\CsMarketplace\Model\VendorFactory $vendor,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
    ) {
        $this->massgenerator = $massgenerator;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
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
     * Generate Coupons action
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noroute');
            return;
        }
        $result = [];
        $this->_initRule();

        /** @var $rule \Magento\SalesRule\Model\Rule */
        $rule = $this->_coreRegistry->registry('current_promo_quote_rule');

        if (!$rule->getId()) {
            $result['error'] = __('Rule is not defined');
        } else {
            try {
                $data = $this->getRequest()->getParams();
                if (!empty($data['to_date'])) {
                    $inputFilter = new \Zend_Filter_Input(['to_date' => $this->_dateFilter], [], $data);
                    $data = $inputFilter->getUnescaped();
                }

                /** @var $generator \Magento\SalesRule\Model\Coupon\Massgenerator */
                $generator = $this->massgenerator;
                if (!$generator->validateData($data)) {
                    $result['error'] = __('Invalid data provided');
                } else {
                    $generator->setData($data);
                    $generator->generatePool();
                    $generated = $generator->getGeneratedCount();
                    $this->messageManager->addSuccessMessage(__('%1 coupon(s) have been generated.', $generated));
                    $this->_view->getLayout()->initMessages();
                    $result['messages'] = $this->_view->getLayout()->getMessagesBlock()->getGroupedHtml();
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $result['error'] = $e->getMessage();
            } catch (\Exception $e) {
                $result['error'] = __(
                    'Something went wrong while generating coupons. Please review the log and try again.'
                );
                $this->logger->critical($e);
            }
        }
        $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($result)
        );
    }
}
