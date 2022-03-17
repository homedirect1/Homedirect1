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
 * @package     Ced_Inventory
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsInventory\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Inventory extends AbstractDb {

    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('cs_inventory_minimumproduct', 'id');
    }

}
