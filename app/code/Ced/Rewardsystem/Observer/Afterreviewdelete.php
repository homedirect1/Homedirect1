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

class Afterreviewdelete implements ObserverInterface
{
    /**
     * @var \Ced\Rewardsystem\Model\RegisuserpointFactory
     */
    protected $pointFactory;

    /**
     * @var \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint
     */
    protected $pointResource;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * Afterreviewdelete constructor.
     * @param \Ced\Rewardsystem\Model\RegisuserpointFactory $pointFactory
     * @param \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint $pointResource
     */
    public function __construct(
        \Ced\Rewardsystem\Model\RegisuserpointFactory $pointFactory,
        \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint $pointResource
    ) {
        $this->pointFactory = $pointFactory;
        $this->pointResource = $pointResource;
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
            $reviewId = $review->getReviewId();

            if ($reviewId) {
                $rewardPoint = $this->pointFactory->create();
                $this->pointResource->load($rewardPoint, $reviewId, "review_id");
                if ($rewardPoint->getId() && $rewardPoint->getStatus()!='complete') {
                    $this->pointResource->delete($rewardPoint);
                }
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }
}
