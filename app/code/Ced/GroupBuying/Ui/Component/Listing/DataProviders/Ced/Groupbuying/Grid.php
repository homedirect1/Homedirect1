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

namespace Ced\GroupBuying\Ui\Component\Listing\DataProviders\Ced\Groupbuying;

use Ced\GroupBuying\Model\ResourceModel\Main\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class Grid
 */
class Grid extends AbstractDataProvider
{


    /**
     * Constructor
     *
     * @param string            $name
     * @param string            $primaryFieldName
     * @param string            $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array             $meta
     * @param array             $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta=[],
        array $data=[]
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        foreach ($this->collection as $key => $collection) {
            $isApprove = (int) $collection->getIsApprove();
            switch (true) {
                case ($isApprove === 0):
                    $collection->setIsApprove('Disapproved');
                    // Remove original item from collection.
                    $this->collection->removeItemByKey($key);
                    // Add modified item to collection.
                    $this->collection->addItem($collection);
                break;

                case ($isApprove === 1):
                    $collection->setIsApprove('Approved');
                    // Remove original item from collection.
                    $this->collection->removeItemByKey($key);
                    // Add modified item to collection.
                    $this->collection->addItem($collection);
                break;

                case ($isApprove === 2):
                    $collection->setIsApprove('Pending');
                    // Remove original item from collection.
                    $this->collection->removeItemByKey($key);
                    // Add modified item to collection.
                    $this->collection->addItem($collection);
                break;

                default:
                    // If group status is something else leave it default.
                break;
            }//end switch
        }//end foreach

    }//end __construct()


}//end class
