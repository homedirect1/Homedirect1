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
 * @package     Ced_GShop
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GShop\Model\ResourceModel;

class Productchange extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init('gxpress_product_change', 'id');
    }

    public function deleteFromProductChange($productIds, $type)
    {
        if (count($productIds)<=0) {
            return $this;
        }

        $dbh = $this->getConnection();
        $condition = "{$this->getTable('gxpress_product_change')}.product_id in (" . $dbh->quote($productIds) . ")";
        $condition .= " AND {$this->getTable('gxpress_product_change')}.cron_type = '" . $type . "'";
        $dbh->delete($this->getTable('gxpress_product_change'), $condition);
        return $this;
    }
}
