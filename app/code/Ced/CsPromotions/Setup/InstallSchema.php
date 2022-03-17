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
 * @package     Ced_CsPromotions
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
 

namespace Ced\CsPromotions\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;


class InstallSchema implements InstallSchemaInterface
{
    
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        
        $connection = $setup->getConnection();
        $column = [
            'type' => Table::TYPE_INTEGER,
            'length' => 5,
            'nullable' => false,
            'comment' => 'Vendor Id',
            'default' => '0'
            ];
        $column1 = [
            'type' => Table::TYPE_TEXT,
            'length' => 50,
            'nullable' => false,
            'comment' => 'Vendor Name',
            'default' => 'Admin'
            ];
        $connection->addColumn($setup->getTable('catalogrule'), 'vendor_id', $column);
        $connection->addColumn($setup->getTable('salesrule'), 'vendor_id', $column);
        $connection->addColumn($setup->getTable('catalogrule'), 'vendor_name', $column1);
        $connection->addColumn($setup->getTable('salesrule'), 'vendor_name', $column1);
    }
}