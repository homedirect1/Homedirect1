<?php

namespace Ced\GroupBuying\Api\Data;

interface GroupInterface
{
    public const GROUP_ID = "group_id";
    public const GROUP_ADMIN_ID = "owner_customer_id";
    public const SHOW_CONTRIBUTION_TO_GUEST = "show_contribution_to_guest";
    public const PRODUCT_ID = "product_id";
    public const PRICE = "price";
    public const GROUP_MEMBER_NAME = "receiver_name";
    public const GROUP_MEMBER_EMAIL = "gift_receiver_email";
    public const GROUP_INVITATION_MESSAGE = "gift_msg";
    public const GROUP_CREATED_AT = "created_at";
    public const STATUS = "status";
    public const GROUP_SIZE = "group_size";
    public const GROUPBUYING_START_DATE = "start_date";
    public const GROUPBUYING_END_DATE = "end_date";
    public const GROUP_APPROVAL_STATUS = "is_approve";

    public const ATTRIBUTES = [
        self::GROUP_ID,
        self::GROUP_ADMIN_ID,
        self::SHOW_CONTRIBUTION_TO_GUEST,
        self::PRODUCT_ID,
        self::PRICE,
        self::GROUP_MEMBER_NAME,
        self::GROUP_MEMBER_EMAIL,
        self::GROUP_INVITATION_MESSAGE,
        self::GROUP_CREATED_AT,
        self::STATUS,
        self::GROUP_SIZE,
        self::GROUPBUYING_START_DATE,
        self::GROUPBUYING_END_DATE,
        self::GROUP_APPROVAL_STATUS,
    ];

    /**
     * Setter for group ID
     *
     * @param int $groupId Group ID.
     *
     * @return $this
     */
    public function setGroupId(int $groupId): GroupInterface;

    /**
     * Getter for group ID
     *
     * @return int|null
     */
    public function getGroupId():?int;

    /**
     * Setter for Group Creator Customer ID
     *
     * @param int $groupAdminId Group admin entity ID.
     *
     * @return $this
     */
    public function setGroupAdminId(int $groupAdminId): GroupInterface;

    /**
     * Getter for Group Creator Customer ID
     *
     * @return int|null
     */
    public function getGroupAdminId(): ?int;

    /**
     * Setter for Show contribution to guest
     *
     * @param bool $isShowContributionToGuest Show Contribution to guest.
     *
     * @return $this
     */
    public function setShowContributionToGuest(bool $isShowContributionToGuest): GroupInterface;

    /**
     * Getter for Show contribution to guest
     *
     * @return bool|null
     */
    public function getShowContributionToGuest(): ?bool;

    /**
     * Setter for product ID
     *
     * @param int $productId Product ID.
     *
     * @return $this
     */
    public function setOriginalProductId(int $productId): GroupInterface;

    /**
     * Getter for product ID
     *
     * @return int|null
     */
    public function getOriginalProductId(): ?int;

    /**
     * Setter for price
     *
     * @param int $price Price.
     *
     * @return $this
     */
    public function setPrice(int $price): GroupInterface;

    /**
     * Getter for price
     *
     * @return int|null
     */
    public function getPrice(): ?int;

    /**
     * Setter for group member name
     *
     * @param string $groupMemberName Group member name.
     *
     * @return $this
     */
    public function setGroupMemberName(string $groupMemberName): GroupInterface;

    /**
     * Getter for group member name
     *
     * @return string|null
     */
    public function getGroupMemberName() : ?string;

    /**
     * Setter for group member email
     *
     * @param string $groupMemberEmail Group member email.
     *
     * @return $this
     */
    public function setGroupMemberEmail(string $groupMemberEmail): GroupInterface;

    /**
     * Getter for group member email
     *
     * @return string|null
     */
    public function getGroupMemberEmail() : ?string;

    /**
     * Setter for group invitation message
     *
     * @param string $groupInvitationMessage Group invitation message.
     *
     * @return $this
     */
    public function setGroupInvitationMessage(string $groupInvitationMessage): GroupInterface;

    /**
     * Getter for group invitation message
     *
     * @return string|null
     */
    public function getGroupInvitationMessage() : ?string;

    /**
     * Setter for group created at
     *
     * @param string $groupCreatedAt Group created at.
     *
     * @return $this
     */
    public function setGroupCreatedAt(string $groupCreatedAt): GroupInterface;

    /**
     * Getter for group created at
     *
     * @return string|null
     */
    public function getGroupCreatedAt() : ?string;

    /**
     * Setter for status
     *
     * @param int $status Status.
     *
     * @return $this
     */
    public function setStatus(int $status): GroupInterface;

    /**
     * Getter for status
     *
     * @return int|null
     */
    public function getStatus() : ?int;

     /**
      * Setter for Group Size
      *
      * @param int $groupSize Group capacity.
      *
      * @return $this
      */
    public function setGroupSize(int $groupSize): GroupInterface;

    /**
     * Getter for Group Size
     *
     * @return int|null
     */
    public function getGroupSize() : ?int;

    /**
     * Setter for Group start date
     *
     * @param string $groupStartDate Group start date.
     *
     * @return $this
     */
    public function setGroupStartDate(string $groupStartDate): GroupInterface;

    /**
     * Getter for Group start date
     *
     * @return string|null
     */
    public function getGroupStartDate() : ?string;

    /**
     * Setter for Group end date
     *
     * @param string $groupEndDate Group end date.
     *
     * @return $this
     */
    public function setGroupEndDate(string $groupEndDate): GroupInterface;

    /**
     * Getter for Group end date
     *
     * @return string|null
     */
    public function getGroupEndDate() : ?string;

    /**
     * Setter for Group approval status
     *
     * @param int $groupApprovalStatus Group approval status.
     *
     * @return $this
     */
    public function setGroupApprovalStatus(int $groupApprovalStatus): GroupInterface;

    /**
     * Getter for Group approval status
     *
     * @return int|null
     */
    public function getGroupApprovalStatus() : ?int;

}
