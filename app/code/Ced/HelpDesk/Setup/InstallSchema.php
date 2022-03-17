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
 * @package     Ced_HelpDesk
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\HelpDesk\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup; 
        $installer->startSetup();
        /**
         * Create table 'ced_helpdesk_priority'
         */

        $table = $installer->getConnection()->newTable(
            $installer->getTable('ced_helpdesk_priority')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],	
            'ID'						

        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Status'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Title'
        )->addColumn(
            'code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Priority Code'
        )->addColumn(
            'bgcolor',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            25,
            [],
            'Background Color'
        )->setComment(
            'Priority Table'
        );

        $installer->getConnection()->createTable($table);
        /**
         * Create table 'ced_helpdesk_status'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ced_helpdesk_status')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],	
            'ID'						

        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Status'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Title'
        )->addColumn(
            'code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Status Code'
        )->addColumn(
            'bgcolor',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            25,
            [],
            'Background Color'
        )->setComment(
            'Status Table'
        );
        $installer->getConnection()->createTable($table);
        /**
         * Create table 'ced_helpdesk_department'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ced_helpdesk_department')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],	
            'ID'						
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Name'
        )->addColumn(
            'agent',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Agent'
        )->addColumn(
            'department_head',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Head of Department'
        )
        ->addColumn(
            'code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Code'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Sort Order'
        )->addColumn(
            'active',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Active'
        )->addColumn(
            'dept_signature',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Department Signature'
        )->setComment(
            'Department Table'
        );

        $installer->getConnection()->createTable($table);
        /**
         * Create table 'ced_helpdesk_ticket'
         */

        $table = $installer->getConnection()->newTable(
            $installer->getTable('ced_helpdesk_ticket')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],	
            'ID'						

        )->addColumn(
            'ticket_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Ticket Id'
        )->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Message'
        )->addColumn(
            'department',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Department'
        )->addColumn(
            'agent',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Agent'
        )->addColumn(
            'agent_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Agent Name'
        )->addColumn(
            'subject',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Subject'
        )->addColumn(
            'order',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Order'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Customer Id'
        )->addColumn(
            'customer_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Customer Name'
        )->addColumn(
            'customer_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Customer Email'
        )->addColumn(
            'priority',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Priority'
        )->addColumn(
            'store_view',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Store View'
        )->addColumn(
            'num_msg',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Num Msg'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Status'
        )->addColumn(
            'created_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            255,
            [],
            'Created Time'
        )->addColumn(
            'closing_message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Closing Message'
        )->setComment(
            'Ticket Table'
        );
        $installer->getConnection()->createTable($table);
        /**
         * Create table 'ced_helpdesk_agent'
         */

        $table = $installer->getConnection()->newTable(
            $installer->getTable('ced_helpdesk_agent')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],	
            'ID'						
        )->addColumn(
            'username',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Username'
        )->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Email'
        )->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'User Id'
        )->addColumn(
            'active',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Active'
        )->addColumn(
            'role_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Role Name'
        )->setComment(
            'Agent Table'
        );

        $installer->getConnection()->createTable($table);
        /**
         * Create table 'ced_helpdesk_messages'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ced_helpdesk_messages')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],   
            'ID'                        

        )->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Message'
        )->addColumn(
            'from',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'From'
        )->addColumn(
            'to',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'To'
        )->addColumn(
            'ticket_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Ticket Id'
        )->addColumn(
            'created',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            255,
            [],
            'Created'
        )->addColumn(
            'attachment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Attachment'
        )->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Reply or Re-Assign'
        )->setComment(
            'Messages Table'
        );

        $installer->getConnection()->createTable($table);
		
		$installer->endSetup(); 
    }
}
