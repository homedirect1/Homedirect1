<?php /** @noinspection PhpUndefinedClassInspection */ //@codingStandardsIgnoreStart
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
namespace Ced\GroupBuying\Model;

use Ced\GroupBuying\Api\Data;
use Ced\GroupBuying\Api\MainRepositoryInterface;
use Exception;
use Ced\GroupBuying\Api\Data\GroupSearchResultsInterfaceFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Ced\GroupBuying\Model\ResourceModel\Main as MainResource;
use Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SortOrder;

class MainRepository implements MainRepositoryInterface //@codingStandardsIgnoreLine
{

    /**
     * Resource Model Group Main table
     *
     * @var MainResource
     */
    private $mainResource;

    /**
     * Group model factory class
     *
     * @var MainFactory
     */
    private $mainFactory;

    /**
     * Group table collection.
     *
     * @var CollectionFactory
     */
    private $groupCollectionFactory;

    /**
     * Search Interface
     *
     * @var GroupSearchResultsInterfaceFactory
     */
    private $searchResultsInterfaceFactory;


    /**
     * Constructor
     *
     * @param MainResource                       $mainResource                  Resource Model Group Main table.
     * @param MainFactory                        $mainFactory                   Group model factory class.
     * @param CollectionFactory                  $groupCollectionFactory        Group table collection.
     * @param GroupSearchResultsInterfaceFactory $searchResultsInterfaceFactory Search Interface.
     */
    public function __construct(
        MainResource $mainResource,
        MainFactory $mainFactory,
        CollectionFactory $groupCollectionFactory,
        GroupSearchResultsInterfaceFactory $searchResultsInterfaceFactory
    ) {
        $this->mainResource                  = $mainResource;
        $this->mainFactory                   = $mainFactory;
        $this->groupCollectionFactory        = $groupCollectionFactory;
        $this->searchResultsInterfaceFactory = $searchResultsInterfaceFactory;

    }//end __construct()


    /**
     * Create Group
     *
     * @param Data\GroupInterface $group DTO class for Group.
     *
     * @return integer
     * @throws AlreadyExistsException If group already exist.
     */
    public function save(Data\GroupInterface $group): int
    {
        $this->mainResource->save($group);
        return $group->getId(); //@codingStandardsIgnoreLine

    }//end save()


    /**
     * Get group data by using Group ID.
     *
     * @param integer $groupId Group ID.
     *
     * @return Data\GroupInterface DTO class for Group.
     *
     * @throws NoSuchEntityException If group doesn't exist.
     */
    public function getById(int $groupId): Data\GroupInterface
    {
        $group = $this->mainFactory->create();
        $this->mainResource->load($group, $groupId);
        if ($group->getId() === null) {
            throw new NoSuchEntityException(__("Group doesn't exist!"));
        }

        return $group;

    }//end getById()


    /**
     * Delete Group by ID
     *
     * @param integer $groupId Group ID.
     *
     * @return boolean Returns true if data deleted successfully.
     * @throws LocalizedException|Exception Throws error message.
     */
    public function delete(int $groupId): bool
    {
        $group = $this->mainFactory->create();
        $group->setId($groupId);
        return (bool)$this->mainResource->delete($group) === true; //@codingStandardsIgnoreLine

    }//end delete()


    /**
     * Get Group list
     *
     * @param SearchCriteriaInterface $searchCriteria Build logical search conditions like AND, OR, etc.
     *
     * @return Data\GroupSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): Data\GroupSearchResultsInterface
    {
        $collection = $this->groupCollectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }

        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            $field = $sortOrder->getField();
            $collection->addOrder(
                $field,
                $this->getDirection($sortOrder->getDirection())
            );
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();

        $searchResults = $this->searchResultsInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $groups = [];
        foreach ($collection as $group) {
            $groups[] = $group;
        }

        $searchResults->setItems($groups);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;

    }//end getList()


    /**
     * Apply filter to collection
     *
     * @param FilterGroup             $group      Filter Group.
     * @param MainResource\Collection $collection Group table collection.
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $group, MainResource\Collection $collection): void
    {
        $fields     = [];
        $conditions = [];

        foreach ($group->getFilters() as $filter) {
            $condition    = $filter->getConditionType() ?: 'eq'; //@codingStandardsIgnoreLine
            $field        = $filter->getField();
            $value        = $filter->getValue();
            $fields[]     = $field;
            $conditions[] = [$condition => $value];
        }

        $collection->addFieldToFilter($fields, $conditions);

    }//end addFilterGroupToCollection()


    /**
     * Get ascending or descending.
     *
     * @param string $direction Ascending or descending order.
     *
     * @return boolean
     */
    private function getDirection(string $direction): bool
    {
        return $direction === SortOrder::SORT_ASC ?: SortOrder::SORT_DESC; // @codingStandardsIgnoreLine

    }//end getDirection()


}//end class
