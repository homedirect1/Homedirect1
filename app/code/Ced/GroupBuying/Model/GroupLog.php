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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GroupBuying\Model;

use Magento\Framework\Model\AbstractModel;
use Ced\GroupBuying\Model\ResourceModel\GroupLog as GroupLogResource;
class GroupLog extends AbstractModel
{


    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(GroupLogResource::class);

    }//end _construct()


}//end class
