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

namespace Ced\GroupBuying\Model\ResourceModel\Main;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Ced\GroupBuying\Model\Main;
use Ced\GroupBuying\Model\ResourceModel\Main as MainResource;

class Collection extends AbstractCollection
{


    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            Main::class,
            MainResource::class
        );

    }//end _construct()


}//end class
