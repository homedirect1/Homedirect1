<?php //@codingStandardsIgnoreStart
/**
 *
 *  CedCommerce
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the End User License Agreement (EULA)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  https://cedcommerce.com/license-agreement.txt
 *
 *  @author    CedCommerce Core Team <connect@cedcommerce.com>
 *  @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 *  @license   https://cedcommerce.com/license-agreement.txt
 *
 */
//@codingStandardsIgnoreEnd
namespace Ced\GroupBuying\Controller\Adminhtml\GroupBuyingGrid;

use Ced\GroupBuying\Api\MainRepositoryInterface;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Ui\Component\MassAction\Filter;
use Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Request\Http;


class MassDelete extends Action //@codingStandardsIgnoreLine
{

    /**
     * Mass action filter variable
     *
     * @var Filter
     */
    protected Filter $filter;

    /**
     * Group main table collection factory
     *
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * Group table repository
     *
     * @var MainRepositoryInterface
     */
    protected MainRepositoryInterface $mainRepository;

    /**
     * Logs error to var/log
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Redirect
     *
     * @var Http
     */
    private Http $request;


    /**
     * Constructor
     *
     * @param Context $context
     * @param Filter $filter Applies filter.
     * @param CollectionFactory $collectionFactory Group table collection.
     * @param ManagerInterface $messageManager Shows messages.
     * @param ResultFactory $resultFactory Redirect.
     * @param MainRepositoryInterface|null $mainRepository Group table repository.
     * @param LoggerInterface|null $logger Logs error.
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        Http $request,
        MainRepositoryInterface $mainRepository=null,
        LoggerInterface $logger=null
    ) {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->mainRepository    = $mainRepository ?: ObjectManager::getInstance()->create(MainRepositoryInterface::class); //@codingStandardsIgnoreStart
        $this->logger            = $logger ?: ObjectManager::getInstance()->create(LoggerInterface::class); //@codingStandardsIgnoreEnd
        $this->messageManager    = $messageManager;
        $this->resultFactory     = $resultFactory;
        $this->request           = $request;
        
        parent::__construct($context);
    }//end __construct()


    /**
     * Execute action
     *
     * @return Redirect
     * @throws LocalizedException|Exception Throws errors.
     */
    public function execute(): Redirect
    {
        $resultFactory     = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT); //@codingStandardsIgnoreLine
        // $collection        = $this->filter->getCollection($this->collectionFactory->create());
        $groupDeleted      = 0;
        $groupDeletedError = 0;
        $selectedIds = $this->request->getParam('selected');
        $collection = $this->collectionFactory->create()->addFieldToFilter('group_id', $selectedIds);
        foreach ($collection as $group) {
            try {
                $this->mainRepository->delete($group->getId());
                $groupDeleted++;
            } catch (LocalizedException $exception) {
                $this->logger->error($exception->getLogMessage());
                $groupDeletedError++;
            }
        }

        if ((bool) $groupDeleted === true) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $groupDeleted)
            );
        }

        if ((bool) $groupDeletedError === true) {
            $this->messageManager->addErrorMessage(
                __(
                    'A total of %1 record(s) haven\'t been deleted. Please see server logs for more details.',
                    $groupDeletedError
                )
            );
        }

        return $resultFactory->setPath('groupbuying/groupbuyinggrid/index'); //@codingStandardsIgnoreLine
    }//end execute()
}//end class
