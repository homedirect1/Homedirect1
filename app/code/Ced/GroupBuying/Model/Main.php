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

namespace Ced\GroupBuying\Model;

use Ced\GroupBuying\Api\Data\GroupInterface;
use Magento\Framework\Model\AbstractModel;
use Ced\GroupBuying\Model\ResourceModel\Main as MainResource;

class Main extends AbstractModel implements GroupInterface //@codingStandardsIgnoreLine
{
    const STATUS_ENABLED = 1;
    const ORIGINAL_PRODUCT_ID = "original_product_id";
    const GROUP_START_DATE = "start_date";
    const GROUP_END_DATE = "end_date";


    /**
     * Define resource model
     *
     * @return       void
     * @noinspection MagicMethodsValidityInspection
     */
    protected function _construct():void //@codingStandardsIgnoreLine
    {
        $this->_init(MainResource::class); //@codingStandardsIgnoreLine

    }//end _construct()


    /**
     * @inheritDoc
     */
    public function setGroupId(int $groupId):GroupInterface //@codingStandardsIgnoreLine
    {
        return $this->setData(self::GROUP_ID, $groupId);

    }//end setGroupId()

    /**
     * @inheritDoc
     */
    public function getGroupId():?int
    {
        return $this->getData(self::GROUP_ID);
    }

    /**
     * @inheritDoc
     */
    public function setGroupAdminId(int $groupAdminId):GroupInterface //@codingStandardsIgnoreLine
    {
        return $this->setData(self::GROUP_ADMIN_ID, $groupAdminId);

    }//end setGroupAdminId()

    /**
     * @inheritDoc
     */
    public function getGroupAdminId():?int
    {
        return $this->getData(self::GROUP_ADMIN_ID);
    }

    /**
     * @inheritDoc
     */
    public function setShowContributionToGuest(bool $isShowContributionToGuest):GroupInterface //@codingStandardsIgnoreLine
    {
        return $this->setData(self::SHOW_CONTRIBUTION_TO_GUEST, $isShowContributionToGuest);

    }//end setShowContributionToGuest()

    /**
     * @inheritDoc
     */
    public function getShowContributionToGuest() : ?bool
    {
        return $this->getData(self::SHOW_CONTRIBUTION_TO_GUEST);
    }

    /**
     * @inheritDoc
     */
    public function setOriginalProductId(int $productId):GroupInterface //@codingStandardsIgnoreLine
    {
        return $this->setData(self::ORIGINAL_PRODUCT_ID, $productId);

    }//end setOriginalProductId()

    /**
     * @inheritDoc
     */
    public function getOriginalProductId() : ?int
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPrice(int $price):GroupInterface //@codingStandardsIgnoreLine
    {
        return $this->setData(self::PRICE, $price);

    }//end setPrice()

    /**
     * @inheritDoc
     */
    public function getPrice() : ?int
    {
        return $this->getData(self::PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setGroupMemberName(string $groupMemberName):GroupInterface //@codingStandardsIgnoreLine
    {
        return $this->setData(self::GROUP_MEMBER_NAME, $groupMemberName);

    }//end setGroupMemberName()

    /**
     * @inheritDoc
     */
    public function getGroupMemberName() : ?string
    {
        return $this->getData(self::GROUP_MEMBER_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setGroupMemberEmail(string $groupMemberEmail) :GroupInterface //@codingStandardsIgnoreLine
    {
        return $this->setData(self::GROUP_MEMBER_EMAIL, $groupMemberEmail);

    }//end setGroupMemberEmail()

    /**
     * @inheritDoc
     */
    public function getGroupMemberEmail() : ?string
    {
        return $this->getData(self::GROUP_MEMBER_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setGroupInvitationMessage(string $groupInvitationMessage) :GroupInterface //@codingStandardsIgnoreLine
    {
        return $this->setData(self::GROUP_INVITATION_MESSAGE, $groupInvitationMessage);

    }//end setGroupInvitationMessage()

    /**
     * @inheritDoc
     */
    public function getGroupInvitationMessage() : ?string
    {
        return $this->getData(self::GROUP_INVITATION_MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setGroupCreatedAt(string $groupCreatedAt) :GroupInterface //@codingStandardsIgnoreLine
    {
        return $this->setData(self::GROUP_CREATED_AT, $groupCreatedAt);

    }//end setGroupCreatedAt()

    /**
     * @inheritDoc
     */
    public function getGroupCreatedAt() : ?string
    {
        return $this->getData(self::GROUP_CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(int $status):GroupInterface //@codingStandardsIgnoreLine
    {
        return $this->setData(self::STATUS, $status);

    }//end setStatus()

    /**
     * @inheritDoc
     */
    public function getStatus() : ?int
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setGroupSize(int $groupSize):GroupInterface //@codingStandardsIgnoreLine
    {
        return $this->setData(self::GROUP_SIZE, $groupSize);

    }//end setGroupSize()

    /**
     * @inheritDoc
     */
    public function getGroupSize() : ?int
    {
        return $this->getData(self::GROUP_SIZE);
    }

    /**
     * @inheritDoc
     */
    public function setGroupStartDate(string $groupStartDate):GroupInterface //@codingStandardsIgnoreLine
    {
        return $this->setData(self::GROUP_START_DATE, $groupStartDate);

    }//end setGroupStartDate()

    /**
     * @inheritDoc
     */
    public function getGroupStartDate() : ?string
    {
        return $this->getData(self::GROUPBUYING_START_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setGroupEndDate(string $groupEndDate):GroupInterface //@codingStandardsIgnoreLine
    {
        return $this->setData(self::GROUP_END_DATE, $groupEndDate);

    }//end setGroupEndDate()

    /**
     * @inheritDoc
     */
    public function getGroupEndDate() : ?string
    {
        return $this->getData(self::GROUPBUYING_END_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setGroupApprovalStatus(int $groupApprovalStatus):GroupInterface //@codingStandardsIgnoreLine
    {
        return $this->setData(self::GROUP_APPROVAL_STATUS, $groupApprovalStatus);

    }//end setGroupApprovalStatus()

    /**
     * @inheritDoc
     */
    public function getGroupApprovalStatus() : ?int
    {
        return $this->getData(self::GROUP_APPROVAL_STATUS);
    }


}//end class
