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
 * @package     Ced_CsDeliveryDate
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeliveryDate\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

            /**
             * add columns (cedDeliveryDate,cedDeliveryComment) to additional tables-
             * quote
             * quote_item
             * sales_order
             * sales_order_item
             *
             **/

            $installer->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'cs_deliverydate',
                [
                    'type' => 'text',
                    'nullable' => true,
                    'comment' => ' vendor  Delivery Date',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'cs_deliverycomment',
                [
                    'type' => 'text',
                    'nullable' => true,
                    'comment' => ' vendor  Delivery Comment',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'cs_timestamp',
                [
                    'type' => 'text',
                    'nullable' => true,
                    'comment' => ' vendor  cedTimestamp',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_item'),
                'cs_deliverydate',
                [
                    'type' => 'text',
                    'nullable' => true,
                    'comment' => ' vendor  Delivery Date',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_item'),
                'cs_deliverycomment',
                [
                    'type' => 'text',
                    'nullable' => true,
                    'comment' => ' vendor  Delivery Comment',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_item'),
                'cs_timestamp',
                [
                    'type' => 'text',
                    'nullable' => true,
                    'comment' => 'Ced vendor Timestamp',
                ]
            );

        $installer->endSetup();

    }
}