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
 */
//@codingStandardsIgnoreEnd

namespace Ced\GroupBuying\Controller\Adminhtml\Main;

use Ced\GroupBuying\Api\MainRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class Delete
 */
class Delete extends Action
{

    public const ADMIN_RESOURCE = 'Ced_GroupBuying::group_buying_form';

    /**
     * Group table repository.
     *
     * @var MainRepositoryInterface
     */
    protected MainRepositoryInterface $mainRepository;

    /**
     * Param values.
     *
     * @var Http
     */
    private Http $request;


    /**
     * Delete constructor.
     *
     * @param Http                         $request        Param values.
     * @param MainRepositoryInterface|null $mainRepository Group table repository.
     */
    public function __construct(
        Context $context,
        Http $request,
        MainRepositoryInterface $mainRepository=null
    ) {
        $this->mainRepository = $mainRepository ?: ObjectManager::getInstance()->create(MainRepositoryInterface::class); //@codingStandardsIgnoreLine
        $this->request        = $request;
        parent::__construct($context);


    }//end __construct()


    /**
     * Delete execution
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        // Check if we know what should be deleted.
        $id       = (int) $this->request->getParam('group_id');
        $redirect        = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT); //@codingStandardsIgnoreLine
        if ((bool) $id === true) {
            try {
                $this->mainRepository->delete($id);
                $this->messageManager->addSuccessMessage(__('You have deleted the Group.'));
                return $redirect->setPath('groupbuying/groupbuyinggrid/index'); //@codingStandardsIgnoreLine
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $redirect->setPath('groupbuying/main/edit', ['group_id' => $id]); //@codingStandardsIgnoreLine
            }
        }

        $this->messageManager->addErrorMessage(__('We can not find an group to delete.'));
        return $redirect->setPath('groupbuying/groupbuyinggrid/index'); //@codingStandardsIgnoreLine

    }//end execute()


}//end class
