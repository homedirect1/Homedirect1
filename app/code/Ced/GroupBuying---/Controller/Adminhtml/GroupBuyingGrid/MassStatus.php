<?php //@codingStandardsIgnoreStart
/*
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
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Ui\Component\MassAction\Filter;
use Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Updates status for a batch of Group.
 */
class MassStatus extends Action
{

    /**
     * MassActions filter
     *
     * @var Filter
     */
    protected Filter $filter;

    /**
     * Group table collection
     *
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * Redirect
     *
     * @var Http
     */
    private Http $request;

    /**
     * Logs error to var/log
     *
     * @var LoggerInterface|mixed
     */
    private LoggerInterface $logger;

    /**
     * @var MainRepositoryInterface|mixed
     */
    private MainRepositoryInterface $mainRepository;


    /**
     * Constructor
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     * @param Http $request
     * @param MainRepositoryInterface|null $mainRepository
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Http $request,
        MainRepositoryInterface $mainRepository=null,
        LoggerInterface $logger=null
    ) {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->mainRepository    = $mainRepository ?: ObjectManager::getInstance()->create(MainRepositoryInterface::class);
        $this->logger            = $logger ?: ObjectManager::getInstance()->create(LoggerInterface::class);
        $this->request           = $request;
        parent::__construct($context);

    }//end __construct()


    /**
     * Execute action
     *
     * @return Redirect
     * @throws LocalizedException|\Exception
     */
    public function execute(): Redirect
    {
        // $collection        = $this->filter->getCollection($this->collectionFactory->create());
        // $collection = $this->collectionFactory->create();
        $groupUpdated      = 0;
        $groupUpdatedError = 0;
        $status            = (int) $this->request->getParam('status');
        $selectedIds = $this->request->getParam('selected');
        $collection = $this->collectionFactory->create()->addFieldToFilter('group_id', $selectedIds);
        foreach ($collection as $group) {
            try {
                $group->setIsApprove($status);
                $this->mainRepository->save($group);
                $groupUpdated++;
            } catch (LocalizedException $exception) {
                $this->logger->error($exception->getLogMessage());
                $groupUpdatedError++;
            }
        }

        if ((bool) $groupUpdated === true) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been updated.', $groupUpdated)
            );
        }

        if ((bool) $groupUpdatedError === true) {
            $this->messageManager->addErrorMessage(
                __(
                    'A total of %1 record(s) haven\'t been updated. Please see server logs for more details.',
                    $groupUpdatedError
                )
            );
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('groupbuying/groupbuyinggrid/index');

    }//end execute()


}//end class
