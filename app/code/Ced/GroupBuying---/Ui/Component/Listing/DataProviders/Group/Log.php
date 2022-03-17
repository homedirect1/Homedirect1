<?php

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

namespace Ced\GroupBuying\Ui\Component\Listing\DataProviders\Group;

use Ced\GroupBuying\Model\ResourceModel\GroupLog\CollectionFactory;
use Ced\GroupBuying\Model\Session;

class Log extends \Magento\Ui\DataProvider\AbstractDataProvider
{


    /**
     * Constructor
     *
     * @param string            $name
     * @param string            $primaryFieldName
     * @param string            $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param Session           $session
     * @param array             $meta
     * @param array             $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        Session $session,
        array $meta=[],
        array $data=[]
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $groupId = $session->getData('group_id');

        $this->collection = $collectionFactory->create()->addFieldToFilter('group_id', $groupId);

    }//end __construct()


}//end class
