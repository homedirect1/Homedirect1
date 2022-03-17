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
 * @package     Ced_CsProductReview
 * @author   	 CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsProductReview\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
 
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
    	$setup->startSetup();
    	
    	$table = $setup->getConnection()->newTable(
    			$setup->getTable('ced_vendorproduct_review')
    	)->addColumn(
    			'id',
    			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    			null,
    			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
    			'Id'
    	)->addColumn(
    			'vendor_id',
    			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    			11,
    			[],
    			'Vendor Id'
    	)->addColumn(
    			'rating_id',
    			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    			null,
    			[],
    			'Rating Id'
    	)->addColumn(
    			'store_id',
    			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    			null,
    			[],
    			'Store Id'
    	)->setComment(
    			'Vendor Product Review Table'
    	);
    	$setup->getConnection()->createTable($table);
    	$setup->endSetup();
    }
}