<?php //@codingStandardsIgnoreStart

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
 * @package     Ced_GroupBuying
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 * @noinspection PhpUndefinedMethodInspection
 * @codingStandardsIgnoreEnd
 */

namespace Ced\GroupBuying\Controller\Adminhtml\GroupBuyingGrid;

use Ced\GroupBuying\Api\MainRepositoryInterface;
use Ced\GroupBuying\Helper\MassEmail as MassEmailHelper;
use Ced\GroupBuying\Helper\Data as Helper;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;

class Approve extends Action //@codingStandardsIgnoreLine
{

    /**
     * Magento customer collection.
     *
     * @var CollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * Sends mass emails.
     *
     * @var MassEmailHelper
     */
    private $massEmail;

    /**
     * GroupBuying Basic Helper.
     *
     * @var Helper
     */
    private $groupBuyingHelper;

    /**
     * Magento cache.
     *
     * @var Manager
     */
    private $cacheManager;

    /**
     * Redirect
     *
     * @var Http
     */
    private $request;

    /**
     * Group table repository
     *
     * @var MainRepositoryInterface
     */
    protected $mainRepository;

    /**
     * Logs error to var/log
     *
     * @var LoggerInterface
     */
    private $logger;


    /**
     * Constructor
     *
     * @param Context $context
     * @param Manager $cacheManager Magento cache.
     * @param CollectionFactory $customerCollectionFactory Magento customer collection.
     * @param MassEmailHelper $massEmailHelper Sends mass emails.
     * @param Helper $groupBuyingHelper GroupBuying Basic Helper.
     * @param Http $request Get params.
     * @param MainRepositoryInterface|null $mainRepository Group Table Repository.
     * @param LoggerInterface|null $logger Logs error to logs.
     */
    public function __construct(
        Context $context,
        Manager $cacheManager,
        CollectionFactory $customerCollectionFactory,
        MassEmailHelper $massEmailHelper,
        Helper $groupBuyingHelper,
        Http $request,
        MainRepositoryInterface $mainRepository=null,
        LoggerInterface $logger=null
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->massEmail                 = $massEmailHelper;
        $this->groupBuyingHelper         = $groupBuyingHelper;
        $this->cacheManager              = $cacheManager;
        $this->request                   = $request;
        $this->mainRepository            = $mainRepository ?: ObjectManager::getInstance()->create(MainRepositoryInterface::class); //@codingStandardsIgnoreStart
        $this->logger                    = $logger ?: ObjectManager::getInstance()->create(LoggerInterface::class); //@codingStandardsIgnoreEnd
        parent::__construct($context);
    }//end __construct()


    /**
     * Execute Approve
     *
     * @return ResponseInterface|Redirect|ResultInterface
     *
     * @noinspection PhpUndefinedMethodInspection
     */
    public function execute()
    {
        $resultRedirect     = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT); //@codingStandardsIgnoreLine
        $approveStatus  = (int) $this->request->getParam('approve');
        $groupId        = (int) $this->request->getParam('group_id');
        if ((bool) $groupId === true) {
            try {
                $this->mainRepository->getById($groupId)->setIsApprove($approveStatus)->save();
                if ($approveStatus === 1) {
                    $isMassEmailEnabled = $this->groupBuyingHelper->getConfig(Helper::CONFIG_GROUP_MASS_EMAIL_STATUS); //@codingStandardsIgnoreLine
                    if ((bool) $isMassEmailEnabled === true) {
                        $customerEmailArray = $this->customerCollectionFactory->create()->getColumnValues('email');
                        $this->massEmail->sendEmail($groupId, $customerEmailArray);
                    }

                    $this->messageManager->addSuccessMessage(__('You have approved the Group.'));
                    $this->cacheManager->flush($this->cacheManager->getAvailableTypes());
                } elseif ($approveStatus === 0) {
                    $this->messageManager->addSuccessMessage(__('You have disapproved the Group.'));
                }

                return $resultRedirect->setPath('*/*/'); //@codingStandardsIgnoreLine
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->error($e->getMessage());
                return $resultRedirect->setPath('*/*/'); //@codingStandardsIgnoreLine
            }//end try
        }//end if

        $this->messageManager->addErrorMessage(__('We can not find an group to delete.'));
        return $resultRedirect->setPath('*/*/'); //@codingStandardsIgnoreLine
    }//end execute()
}//end class
