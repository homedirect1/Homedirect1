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
 * @package     Ced_Rewardsystem
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Rewardsystem\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Afterreviewsave implements ObserverInterface
{
    /**
     * @var \Ced\Rewardsystem\ResourceModel\Regisuserpoint\CollectionFactory
     */
    public $pointCollectionFactory;

    /**
     * @var \Ced\Rewardsystem\Helper\Data
     */
    public $cedHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @var \Ced\Rewardsystem\Model\RegisuserpointFactory
     */
    protected $pointFactory;

    /**
     * @var \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint
     */
    protected $pointResource;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Ced\Rewardsystem\Model\RegisuserpointFactory $pointFactory
     * @param \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint $pointResource
     * @param \Ced\Rewardsystem\Helper\Data $cedHelper
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Ced\Rewardsystem\Model\RegisuserpointFactory $pointFactory,
        \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint $pointResource,
        \Ced\Rewardsystem\Helper\Data $cedHelper,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Registry $registry
    ) {
        $this->date = $date;
        $this->_session = $session;
        $this->pointFactory = $pointFactory;
        $this->pointResource = $pointResource;
        $this->cedHelper = $cedHelper;
        $this->_coreRegistry = $registry;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        try {
            $review = $observer->getDataObject();
            $statusId = $review->getStatusId();
            $createdAt = $review->getCreatedAt();
            $reviewId = $review->getReviewId();
            $sessionCustomerId = $this->_session->getCustomerId();
            $status = 'pending';
            if ($statusId && $statusId == 1) {
                $status = 'complete';
            }

            $date = $this->date->gmtDate();
            $expdate = "";
            $add_days = $this->cedHelper->getStoreConfig('reward/setting/point_expiration', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($add_days) {
                $expdate = date('Y-m-d', strtotime($date . ' +' . $add_days . ' days'));
            }
            $reviewEnable = $this->cedHelper->getStoreConfig('reward/review/review_enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $reviewPoints = $this->cedHelper->getStoreConfig('reward/review/review_points', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            if ($sessionCustomerId && !$this->_coreRegistry->registry('vendorPanel')) {
                if ($reviewEnable) {
                    $pointModel = $this->pointFactory->create();
                    $pointModel->setCustomerId($sessionCustomerId);
                    $pointModel->setPoint((int)$reviewPoints);
                    $pointModel->setTitle('Reward for review');
                    $pointModel->setCreatingDate($createdAt);
                    $pointModel->setStatus($status);
                    $pointModel->setReviewId($reviewId);
                    if ($status == 'complete') {
                        if ($expdate) {
                            $pointModel->setExpirationDate($expdate);
                        }
                    }
                    $this->pointResource->save($pointModel);
                }
            } else {
                $pointModel = $this->pointFactory->create();
                $this->pointResource->load($pointModel, $reviewId, 'review_id');
                if ($pointModel->getId()) {
                    $pointModel->setStatus($status);
                    $pointModel->setUpdatedAt($date);
                    if ($status == 'complete') {
                        if ($expdate) {
                            $pointModel->setExpirationDate($expdate);
                        }
                    }
                    $this->pointResource->save($pointModel);
                }
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }
}
