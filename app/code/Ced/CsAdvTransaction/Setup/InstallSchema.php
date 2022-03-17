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
 * @package     Ced_CsAdvTransaction
 * @author   	 CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsAdvTransaction\Setup;
 
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
 
/**
 * @codeCoverageIgnore
 */
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

        $table = $installer->getConnection()->newTable(
            $installer->getTable('ced_csadvtransaction_payment_field')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'unsigned' => true,'nullable'  => false,'primary'   => true,),
            'id'
        )
       ->addColumn(
                'field_code', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 2055, array(
                ), 'field_code'
          )
            ->addColumn(
                'field_label', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 2055, array(
                ), 'field_label'
            )
           
            ->addColumn(
                'mode', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
                ), 'mode'
            )
            ->addColumn(
            		'order_state', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
            		), 'order_state'
            )
            ->addColumn(
            		'is_system', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
            		), 'is_system'
            )
            ->addColumn(
                'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_DATE, null, array(
                ), 'created_at'
            )
            ->addColumn(
                'status', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 20, array(
                ), 'status'
            )
          ->addColumn(
        		'type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 2055, array(
        		), 'type'
        )
        ->addColumn(
        		'value', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 2055, array(
        		), 'value'
        );
        
        $installer->getConnection()->createTable($table);
        
        $table1 = $installer->getConnection()->newTable(
        		$installer->getTable('ced_csadvtransaction_vendor_orderfees')
        )->addColumn(
        		'id',
        		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        		null,
        		array('identity' => true,'unsigned' => true,'nullable'  => false,'primary'   => true,),
        		'id'
        )
        ->addColumn(
        		'vendor_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 2055, array(
        		), 'vendor_id'
        )
        ->addColumn(
        		'order_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 2055, array(
        		), 'order_id'
        )
        ->addColumn(
        		'fee_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
        		), 'fee_id'
        )
        ->addColumn(
        		'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_DATE, null, array(
        		), 'created_at'
        )
       
        ->addColumn(
        		'status', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 20, array(
        		), 'status'
        )
        ->addColumn(
        		'amount', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 2055, array(
        		), 'amount'
        )
        ->addColumn(
        		'type',
        		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        		255,
        		['nullable' => false],
        		'type'
        ) ;
        
        		$installer->getConnection()->createTable($table1);
        		$installer->endSetup();
        
    
    }
}
