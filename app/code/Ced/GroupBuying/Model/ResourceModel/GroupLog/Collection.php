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

namespace Ced\GroupBuying\Model\ResourceModel\GroupLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Ced\GroupBuying\Model\GroupLog;
use Ced\GroupBuying\Model\ResourceModel\GroupLog as GroupLogResource;
class Collection extends AbstractCollection
{


    /**
     * TODO
     */
    protected function _construct()
    {
        $this->_init(
            GroupLog::class,
            GroupLogResource::class
        );

    }//end _construct()


}//end class
