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

namespace Ced\GroupBuying\Controller\Account;

use Ced\GroupBuying\Helper\Data;
use Ced\GroupBuying\Model\GroupLogFactory;
use Ced\GroupBuying\Model\ResourceModel\Guest\CollectionFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;

class Approve extends Action //@codingStandardsIgnoreLine
{

    /**
     * Page factory
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Cache manager.
     *
     * @var Manager
     */
    private $cacheManager;

    /**
     * Helper file.
     *
     * @var Data
     */
    private $helper;

    /**
     * Magento customer factory.
     *
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * Group member collection.
     *
     * @var CollectionFactory
     */
    private $giftCollectionFactory;

    /**
     * Magento customer session.
     *
     * @var SessionFactory
     */
    private $customerSession;

    /**
     * Group log table factory.
     *
     * @var GroupLogFactory
     */
    private $groupLog;

    /**
     * Param values.
     *
     * @var Http
     */
    private $request;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory Page factory.
     * @param CollectionFactory $giftCollectionFactory Group member collection.
     * @param Data $helper Helper file.
     * @param GroupLogFactory $groupLogFactory Group log table factory.
     * @param ManagerInterface $messageManager Shows messages.
     * @param CustomerFactory $customerFactory Magento customer factory.
     * @param SessionFactory $customerSession Magento customer session.
     * @param Manager $cacheManager Cache manager.
     * @param ResultFactory $resultFactory Redirect.
     * @param Http $request Param values.
     */
    public function __construct(
        Context         $context,
        PageFactory $resultPageFactory,
        CollectionFactory $giftCollectionFactory,
        Data $helper,
        GroupLogFactory $groupLogFactory,
        ManagerInterface $messageManager,
        CustomerFactory $customerFactory,
        SessionFactory $customerSession,
        Manager $cacheManager,
        ResultFactory $resultFactory,
        Http $request
    ) {
        $this->resultPageFactory     = $resultPageFactory;
        $this->resultFactory         = $resultFactory;
        $this->giftCollectionFactory = $giftCollectionFactory;
        $this->messageManager        = $messageManager;
        $this->customerFactory       = $customerFactory;
        $this->helper                = $helper;
        $this->customerSession       = $customerSession;
        $this->cacheManager          = $cacheManager;
        $this->groupLog              = $groupLogFactory;
        $this->request               = $request;
        parent::__construct($context);
    }//end __construct()


    /**
     * TODO
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $customerSession = $this->customerSession->create();
        $redirect        = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT); //@codingStandardsIgnoreLine
        if ($customerSession->isLoggedIn() === false) {
            $this->messageManager->addErrorMessage(__('Please login to join this group!'));
            return $this->_redirect('customer/account/login'); //@codingStandardsIgnoreLine
        }

        $param = $this->request->getParam('gift_id');

        $groupVacancy = $this->helper->getGroupVacancy($param);
        if ($groupVacancy < 1) {
            $this->messageManager->addErrorMessage(__('Sorry! This group is full!'));
            return $this->_redirect('groupbuying/account/request'); //@codingStandardsIgnoreLine
        }

        $customerData = $customerSession->getCustomer();
        $customerId   = $customerData->getId();
        $customer     = $this->customerFactory->create()->load($customerId);
        $data         = $this->giftCollectionFactory->create()->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'guest_email',
            $customer->getEmail()
        )->addFieldToFilter(
            'groupgift_id',
            $param
        )->getFirstItem();

        try {
            // If customer is not invited by admin, but joining from product page.
            if (empty($data->getData()) === true) {
                $data->setGroupgiftId($param);
                $data->setGuestName($customerData->getFirstname().' '.$customerData->getLastname());
                $data->setGuestEmail($customerData->getEmail());

                $groupLog  = $this->groupLog->create();
                $guestName = $data->getGuestName();
                $groupLog->setGroupId($param);
                $groupLog->setMemberName($guestName);
                $groupLog->setLog($guestName.' joined this group from product page!');
            } else {
                $groupLog  = $this->groupLog->create();
                $guestName = $data->getGuestName();
                $groupLog->setGroupId($param);
                $groupLog->setMemberName($guestName);
                $groupLog->setLog($guestName.' joined this group from invitation!');
            }

            $groupLog->save();
            $data->setData('request_approval', 2);
            $data->save();
            $this->cacheManager->flush($this->cacheManager->getAvailableTypes());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }//end try

        $this->messageManager->addSuccessMessage(__('Request Approved Successfully'));
        return $this->_redirect('groupbuying/account/request'); //@codingStandardsIgnoreLine
    }//end execute()
}//end class
