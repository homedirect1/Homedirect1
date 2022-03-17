<?php //@codingStandardsIgnoreStart

/*
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
 */
//@codingStandardsIgnoreEnd
namespace Ced\GroupBuying\Controller\Adminhtml\Main;

use Ced\GroupBuying\Api\MainRepositoryInterface;
use Ced\GroupBuying\Model\Main;
use Ced\GroupBuying\Model\MainFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class Save
 */
class Save extends Action
{

    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Ced_GroupBuying::group_buying_form';

    /**
     * Clears form after save.
     *
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * Group table repository.
     *
     * @var MainRepositoryInterface
     */
    protected MainRepositoryInterface $mainRepository;

    /**
     * Redirect
     *
     * @var Http
     */
    private Http $request;

    /**
     * Group main model.
     *
     * @var MainFactory
     */
    private MainFactory $mainFactory;


    /**
     * Constructor
     *
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor Clears form after save.
     * @param Http $request Request param values.
     * @param ResultFactory $resultFactory Redirect.
     * @param ManagerInterface $messageManager Shows success/error message.
     * @param MainFactory $mainFactory Main factory.
     * @param MainRepositoryInterface|null $mainRepository Group table repository.
     *
     * @noinspection SpellCheckingInspection
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        Http $request,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        MainFactory $mainFactory,
        MainRepositoryInterface $mainRepository=null
    ) {
        $this->dataPersistor  = $dataPersistor;
        $this->mainRepository = $mainRepository ?: ObjectManager::getInstance()->create(MainRepositoryInterface::class); //@codingStandardsIgnoreLine
        $this->request        = $request;
        $this->resultFactory  = $resultFactory;
        $this->messageManager = $messageManager;
        $this->mainFactory    = $mainFactory;
        parent::__construct($context);

    }//end __construct()


    /**
     * Save action
     *
     * @return ResultInterface
     * @throws NoSuchEntityException If group not found.
     */
    public function execute(): ResultInterface
    {
        $resultFactory = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT); //@codingStandardsIgnoreLine
        $data          = $this->request->getPostValue();
        if (empty($data) !== true) {
            if (isset($data['is_active']) === true && $data['is_active'] === 'true') {
                $data['is_active'] = Main::STATUS_ENABLED; //@codingStandardsIgnoreLine
            }

            if (empty($data['id']) === true) {
                $data['id'] = null;
            }

            $model = $this->mainFactory->create();

            $id = $this->request->getParam('id');
            if ((bool) $id === true) {
                $model = $this->mainRepository->getById($id);
            }

            $model->setData($data);
            try {
                $this->mainRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the Group.'));
                $this->dataPersistor->clear('ced_groupbuying_main');
                if ($this->request->getParam('back')) { //@codingStandardsIgnoreLine TODO: Temp ignore.
                    return $resultFactory->setPath('groupbuying/groupbuyinggrid/edit', ['group_id' => $model->getId(), '_current' => true]); //@codingStandardsIgnoreLine
                }

                return $resultFactory->setPath('groupbuying/groupbuyinggrid/index'); //@codingStandardsIgnoreLine
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the data.'));
            }

            $this->dataPersistor->set('ced_groupbuying_main', $data);
            return $resultFactory->setPath('*/*/edit', ['group_id' => $this->request->getParam('id')]); //@codingStandardsIgnoreLine
        }//end if

        return $resultFactory->setPath('*/*/'); //@codingStandardsIgnoreLine

    }//end execute()


}//end class
