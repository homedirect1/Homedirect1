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
namespace Ced\GroupBuying\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;


interface MainRepositoryInterface
{


    /**
     * Create Group
     *
     * @param Data\GroupInterface $group DTO class for Group.
     *
     * @return integer
     */
    public function save(Data\GroupInterface $group): int;


    /**
     * Get group data by using Group ID.
     *
     * @param integer $groupId Group ID.
     *
     * @return Data\GroupInterface DTO class for Group.
     *
     * @throws NoSuchEntityException If group doesn't exist.
     */
    public function getById(int $groupId): Data\GroupInterface;


    /**
     * Delete Group by ID
     *
     * @param integer $groupId Group ID.
     *
     * @return boolean Returns true if data deleted successfully.
     * @throws LocalizedException Throws error message.
     */
    public function delete(int $groupId): bool;


    /**
     * Get Group list
     *
     * @param SearchCriteriaInterface $searchCriteria Build logical search conditions like AND, OR, etc.
     *
     * @return Data\GroupSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): Data\GroupSearchResultsInterface;


}//end interface
