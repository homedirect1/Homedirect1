<?php
//@codingStandardsIgnoreStart
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
namespace Ced\GroupBuying\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface GroupSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Returns list of groups.
     *
     * @return GroupInterface[]
     */
    public function getItems(): array;

    /**
     * Sets list of groups.
     *
     * @param GroupInterface[] $items Array of group DTO objects.
     *
     * @return GroupSearchResultsInterface
     */
    public function setItems(array $items): GroupSearchResultsInterface;
}
