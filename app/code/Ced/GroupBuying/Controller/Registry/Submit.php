<?php

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

namespace Ced\GroupBuying\Controller\Registry;

use Ced\GroupBuying\Api\MainRepositoryInterface;
use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use \Ced\GroupBuying\Helper\Data as Helper;
use \Ced\GroupBuying\Helper\MassEmail as MassEmailHelper;

class Submit extends Action
{
    protected $resultPageFactory;

    protected $_messageManager;

    protected $urlBuilder;

    private $guestFactory;
    private $productFactory;
    private $customerSession;
    private $mainFactory;
    private $cacheManager;


    /**
     * TODO
     *
     * @param Action\Context $context
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Ced\GroupBuying\Model\GuestFactory $guestFactory
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Ced\GroupBuying\Model\MainFactory $mainFactory
     * @param \Ced\GroupBuying\Model\GroupLogFactory $groupLogFactory
     * @param Helper $groupBuyingHelper
     * @param MassEmailHelper $massEmailHelper
     * @param \Magento\Framework\App\Cache\Manager $cacheManager
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param RequestInterface $request
     * @param MainRepositoryInterface $mainRepository
     * @param PageFactory $resultFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context         $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\UrlInterface             $urlBuilder,
        \Ced\GroupBuying\Model\GuestFactory         $guestFactory,
        \Magento\Catalog\Model\ProductRepository    $productRepository,
        \Magento\Customer\Model\SessionFactory      $customerSession,
        \Ced\GroupBuying\Model\MainFactory          $mainFactory,
        \Ced\GroupBuying\Model\GroupLogFactory      $groupLogFactory,
        Helper                                      $groupBuyingHelper,
        MassEmailHelper                             $massEmailHelper,
        \Magento\Framework\App\Cache\Manager $cacheManager,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        RequestInterface $request,
        MainRepositoryInterface $mainRepository,
        PageFactory $resultFactory
    ) {
        $this->_messageManager           = $messageManager;
        $this->_urlBuilder               = $urlBuilder;
        $this->guestFactory              = $guestFactory;
        $this->productRepository            = $productRepository;
        $this->customerSession           = $customerSession;
        $this->mainFactory               = $mainFactory;
        $this->groupBuyingHelper         = $groupBuyingHelper;
        $this->cacheManager              = $cacheManager;
        $this->massEmail                 = $massEmailHelper;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->groupLog                  = $groupLogFactory;
        $this->request =$request;
        $this->mainRepository = $mainRepository;
        $this->resultPageFactory      = $resultFactory;
        parent::__construct($context);
    }//end __construct()


    /**
     * TODO
     *
     * @return ResponseInterface|ResultInterface|void
     * @throws Exception
     */
    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('1');
        try{
        $resultFactory = $this->resultPageFactory->create();
        }catch(\Exception $e){
        $logger->info($e);

        }
        $data             = $this->request->getParams();
        $group_partial    = $this->guestFactory->create();
        $original_product = $this->productRepository->getById($data['original_product_id']);
        $customerSession  = $this->customerSession->create();
        if ($customerSession->isLoggedIn()) {
        $logger->info('PDF_create_3dview(pdfdoc, username, optlist)');

            $customerData = $customerSession->getCustomer();
            $customer_id  = $customerData->getId();
        } else {
            $this->_messageManager->addErrorMessage(__('You Should be logged in to complete this action'));
        }

        if (!$data['gift_receiver_email']) {
            $data['gift_receiver_email'] = $customerData->getEmail();            
        }

        $group = $this->mainFactory->create();
        try {
            // Set the Group information
            $data['owner_customer_id'] = $customer_id;
            $isGroupApprovalEnabled    = $this->groupBuyingHelper->getConfig(Helper::CONFIG_GROUP_APPROVAL_STATUS);
            $isMassEmailEnabled        = $this->groupBuyingHelper->getConfig(Helper::CONFIG_GROUP_MASS_EMAIL_STATUS);
            if ($isGroupApprovalEnabled) {
                $data['is_approve'] = 2;
            // Set approval status to pending
            } else {
                $data['is_approve'] = 1;
                // Set group status to approved by default
            }

            $group->setData($data);
            $this->mainRepository->save($group);
            $groupId         = $group->getId();
            $groupMemberName = $data['receiver_name'];

            $group_partial->setData('groupgift_id', $groupId)->setData('guest_name', $groupMemberName)->setData('guest_email', $data['gift_receiver_email'])->setData('request_approval', 2)->save();

            $groupLog = $this->groupLog->create();
            $groupLog->setGroupId($groupId);
            $groupLog->setMemberName($groupMemberName);
            $groupLog->setLog($groupMemberName.' created this group!');
            $groupLog->save();
            // Email will be sent from here only if group approval is enabled, otherwise email will be sent after group approval.
            if (!$isGroupApprovalEnabled && $isMassEmailEnabled) {
                $customerEmailArray = $this->customerCollectionFactory->create()->getColumnValues('email');
                $this->massEmail->sendEmail($groupId, $customerEmailArray);
            }

            $this->cacheManager->flush($this->cacheManager->getAvailableTypes());
        } catch (Exception $ex) {
            $this->_messageManager->addErrorMessage(__($ex->getMessage()));
        }//end try

        $mailList = [];

        // Invite the Friends for the group buying
        foreach ($data['uname'] as $key => $value) {
            $mail = trim($data['email'][$key]);

            if (!in_array($mail, $mailList) && $mail) {
                $mailList[]  = $mail;
                $group_guest = $this->guestFactory->create();
                $group_guest->setData('groupgift_id', $group->getId())->setData('guest_name', $value)->setData('guest_email', $data['email'][$key])->save();
                $receiverInfo = [
                    'name'  => $value,
                    'email' => $data['email'][$key],
                ];

                $this->massEmail->sendEmail($group->getId(), [$data['email'][$key]], $customerData->getName());

            }//end if
        }//end foreach

        $this->_messageManager->addSuccessMessage(__('Group Buying Saved Successfully.'));
        return $resultFactory;
    }//end execute()
}//end class
