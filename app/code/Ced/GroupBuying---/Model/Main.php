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
    public function setGroupId(int $groupId) : Main
    {
        return $this->setData(self::GROUP_ID, $groupId);
    }

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
    public function setGroupAdminId(int $groupAdminId) : Main
    {
        return (int)$this->setData(self::GROUP_ADMIN_ID, $groupAdminId);
    }

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
    public function setShowContributionToGuest(bool $isShowContributionToGuest) : Main
    {
        return $this->setData(self::SHOW_CONTRIBUTION_TO_GUEST, $isShowContributionToGuest);
    }

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
    public function setOriginalProductId(int $productId) : Main
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

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
    public function setPrice(int $price) : Main
    {
        return $this->setData(self::PRICE, $price);
    }

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
    public function setGroupMemberName(string $groupMemberName) : Main
    {
        return $this->setData(self::GROUP_MEMBER_NAME, $groupMemberName);
    }

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
    public function setGroupMemberEmail(string $groupMemberEmail) : Main
    {
        return $this->setData(self::GROUP_MEMBER_EMAIL, $groupMemberEmail);
    }

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
    public function setGroupInvitationMessage(string $groupInvitationMessage) : Main
    {
        return $this->setData(self::GROUP_INVITATION_MESSAGE, $groupInvitationMessage);
    }

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
    public function setGroupCreatedAt(string $groupCreatedAt) : Main
    {
        return $this->setData(self::GROUP_CREATED_AT, $groupCreatedAt);
    }

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
    public function setStatus(int $status)  : Main
    {
        return $this->setData(self::STATUS, $status);
    }

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
    public function setGroupSize(int $groupSize) : Main
    {
        return $this->setData(self::GROUP_SIZE, $groupSize);
    }

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
    public function setGroupStartDate(string $groupStartDate) : Main
    {
        return $this->setData(self::GROUPBUYING_START_DATE, $groupStartDate);
    }

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
    public function setGroupEndDate(string $groupEndDate) : Main
    {
        return $this->setData(self::GROUPBUYING_END_DATE, $groupEndDate);
    }

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
    public function setGroupApprovalStatus(int $groupApprovalStatus) : Main
    {
        return $this->setData(self::GROUP_APPROVAL_STATUS, $groupApprovalStatus);
    }

    /**
     * @inheritDoc
     */
    public function getGroupApprovalStatus() : ?int
    {
        return $this->getData(self::GROUP_APPROVAL_STATUS);
    }


}//end class
