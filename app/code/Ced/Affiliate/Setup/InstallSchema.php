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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Affiliate\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable(
            $setup->getTable('ced_affiliate_account')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'customer_id',
             \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            '11',
            ['nullable' => false],
            'Customer Id'
        )->addColumn(
            'customer_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Customer Name'
        )->addColumn(
            'referral_website',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Referral Website'
        )->addColumn(
            'referred_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Referral Email'
        )->addColumn(
            'paypal_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Paypal Email'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            255,
            [],
            'Referral Website'
        )->addColumn(
            'customer_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Customer Email'
        )->addColumn(
            'affiliate_url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            1500,
            [],
            'Affiliate Url'
        )->addColumn(
            'affiliate_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            1500,
            [],
            'Affiliate Id'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            1500,
            [],
            'Status'
        )->addColumn(
            'approve',
             \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            '11',
            ['nullable' => false],
            'approve'
        )->addColumn(
            'identity_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            1500,
            [],
            'Status'
        )->addColumn(
            'identityfile',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            1500,
            [],
            'Status'
        )->addColumn(
            'addressfile',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            1500,
            [],
            'Status'
        )->addColumn(
            'companyfile',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            1500,
            [],
            'Status'
        )->setComment(
            'Custom Table'
        );
       $setup->getConnection()->createTable($table);




       $table = $setup->getConnection()->newTable(
       		$setup->getTable('ced_affiliate_banner')
       )->addColumn(
       		'id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
       		'Id'
       )->addColumn(
       		'customer_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Customer Id'
       )->addColumn(
       		'banner_name',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Banner Name'
       )->addColumn(
       		'banner_type',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Banner Type'
       )->addColumn(
       		'banner_link',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Banner Link'
       )->addColumn(
       		'banner_data',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Banner Image'
       )->addColumn(
       		'banner_height',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Banner Height'
       )->addColumn(
       		'banner_width',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Banner Width'
       )->addColumn(
       		'validity',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Validity'
       )->addColumn(
       		'status',
       		 \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            '11',
            ['nullable' => false],
       		'Status'
       )->setComment(
       		'Banner DB'
       );
       $setup->getConnection()->createTable($table);





       $table = $setup->getConnection()->newTable(
       		$setup->getTable('ced_affiliate_comission')
       )->addColumn(
       		'id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
       		'Id'
       )->addColumn(
       		'customer_id',
       		 \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            '11',
            ['nullable' => false],
       		'Customer Id'
       )->addColumn(
       		'increment_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Increment Id'
       )->addColumn(
       		'product_name',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Product Name'
       )->addColumn(
       		'total_amount',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Total Amount'
       )->addColumn(
       		'comission',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Comission'
       )->addColumn(
       		'comission_mode',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Comission'
       )->addColumn(
       		'second_order_comission',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Comission'
       )->addColumn(
       		'customer_email',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Comission'
       )->addColumn(
       		'comission_second_mode',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Comission'
       )->addColumn(
       		'comission_second_fee',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Comission'
       )->addColumn(
       		'affiliate_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Affiliate Id'
       )->addColumn(
       		'holding_period',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Affiliate Id'
       )->addColumn(
       		'customer_name',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Customer Name'
       )->addColumn(
       		'user_type',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'User Type'
       )->addColumn(
       		'eligible_for_payment',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Eligible For Payment'
       )->addColumn(
       		'create_at',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
       		255,
       		[],
       		'Created At'
       )->addColumn(
       		'status',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
            ['nullable' => false],
       		'Status'
       )->addColumn(
       		'comission_giveaway_status',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Status'
       )->setComment(
       		'Comission DB'
       );
       $setup->getConnection()->createTable($table);





       $table = $setup->getConnection()->newTable(
       		$setup->getTable('ced_affiliate_withdrawlrequest')
       )->addColumn(
       		'id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
       		'Id'
       )->addColumn(
       		'customer_id',
       		 \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            '11',
            ['nullable' => false],
       		'Customer Id'
       )->addColumn(
       		'request_amount',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'request_amount'
       )->addColumn(
       		'payment_mode',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Status'
       )->addColumn(
       		'total_amount',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Status'
       )->addColumn(
       		'status',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Status'
       )->addColumn(
       		'affiliate_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Status'
       )->addColumn(
       		'service_tax',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Status'
       )->addColumn(
       		'service_tax_mode',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Status'
       )->addColumn(
       		'payable_amount',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Status'
       )->addColumn(
       		'customer_email',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Status'
       )->addColumn(
       		'amount_paid',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Status'
       )->addColumn(
       		'transaction_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'transaction_id'
       )->addColumn(
       		'note',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Note'
       )->addColumn(
       		'customer_name',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Customer Name'
       )->addColumn(
       		'created_at',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
       		255,
       		[],
       		'Request date'
       )->addColumn(
       		'iscredit',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Is Credit'
       )->addColumn(
       		'redemmed_from_wallet',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		255,
       		[],
       		'Redemmed From Wallet'
       )->setComment(
       		'Withdrawl DB'
       );
       $setup->getConnection()->createTable($table);




       $table = $setup->getConnection()->newTable(
       		$setup->getTable('ced_affiliatewallet')
       )
       ->addColumn(
       		'id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
       		'Id'
       )
       ->addColumn(
       		'credit_amount',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		'64k',
       		[],
       		'credit_amount'
       )
       ->addColumn(
       		'used_amount',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		'64k',
       		[],
       		'Used Amount'
       )
       ->addColumn(
       		'remaining_amount',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		'64k',
       		['nullable' => false],
       		'Remaining Amount'
       )->addColumn(
       		'customer_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['unsigned' => true, 'nullable' => false],
       		'Customer Id'
       )->addColumn(
       		'affiliate_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['unsigned' => true, 'nullable' => false],
       		'Affiliate Id'
       )->addColumn(
       		'customer_email',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['unsigned' => true, 'nullable' => false],
       		'Customer Email'
       )
       ->setComment(
       		'Credit Limits'
       );
       $setup->getConnection()->createTable($table);






       $table = $setup->getConnection()->newTable(
       		$setup->getTable('ced_affiliateamount_summary')
       )
       ->addColumn(
       		'id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
       		'Id'
       )
       ->addColumn(
       		'affiliate_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		'64k',
       		[],
       		'credit_amount'
       )
       ->addColumn(
       		'total_amount',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		[],
       		'Used Amount'
       )
       ->addColumn(
       		'earned_amount',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		0,
       		['nullable' => false],
       		'Remaining Amount'
       )->addColumn(
       		'remaining_amount',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['unsigned' => true, 'nullable' => false],
       		'Customer Id'
       )->addColumn(
       		'customer_email',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['unsigned' => true, 'nullable' => false],
       		'Customer Email'
       )
       ->setComment(
       		'Credit Limits'
       );
       $setup->getConnection()->createTable($table);


      $table = $setup->getConnection()->newTable(
       		$setup->getTable('ced_affiliatereferral_list')
       )->addColumn(
       		'id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
       		'ID'
       )->addColumn(
       		'customer_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['unsigned' => true, 'nullable' => false, 'default' => '0'],
       		'Customer ID'
       )->addColumn(
       		'referred_email',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'Referral Email'
       )->addColumn(
       		'signup_status',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['unsigned' => true, 'nullable' => true, 'nullable' => false, 'default' => 0],
       		'Signup Status'
       )->addColumn(
       		'invite_date',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
       		null,
       		['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
       		'Invite Date'
       )->addColumn(
       		'signup_date',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
       		null,
       		['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
       		'Sign Up Date'
       )->addColumn(
       		'amount',
       		\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
       		'10,2',
       		['unsigned' => true, 'nullable' => false,  'default' => 0],
       		'Total Earned Amount'
       );


       $setup->getConnection()->createTable($table);

       $table = $setup->getConnection()->newTable(
       		$setup->getTable('ced_affiliatereferral_transaction')
       )->addColumn(
       		'id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
       		'ID'
       )->addColumn(
       		'customer_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['unsigned' => true, 'nullable' => false, 'default' => '0'],
       		'Customer ID'
       )->addColumn(
       		'description',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'description'
       )->addColumn(
       		'earned_amount',
       		\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
       		'10,2',
       		['nullable' => false,  'default' => 0],
       		'Total Earned Amount'
       )->addColumn(
       		'transaction_type',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'description'
       )->addColumn(
       		'creation_date',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
       		null,
       		['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
       		'Creation Date'
       );
       $setup->getConnection()->createTable($table);



       $table = $setup->getConnection()->newTable(
       		$setup->getTable('ced_affiliate_payment_settings')
       )->addColumn(
       		'setting_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
       		'Setting Id'
       )->addColumn(
       		'customer_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['unsigned' => true, 'nullable' => false],
       		'Vendor Id'
       )->addColumn(
       		'group',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		'32',
       		['nullable' => false],
       		'Group'
       )->addColumn(
       		'key',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		'64',
       		['nullable' => false],
       		'Key'
       )->addColumn(
       		'value',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		'500',
       		['nullable' => false],
       		'value'
       )->addColumn(
       		'serialized',
       		\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
       		null,
       		['unsigned' => true, 'nullable' => false],
       		'serialized'
       )->setComment(
       		'Affiliate Payment Settings'
       );
       $setup->getConnection()->createTable($table);



       $table = $setup->getConnection()->newTable(
       		$setup->getTable('ced_affiliate_transaction')
       )->addColumn(
       		'id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
       		'Id'
       )->addColumn(
       		'affiliate_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Affiliate Id'
       )->addColumn(
       		'customer_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Customer Id'
       )->addColumn(
       		'customer_email',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Customer Email'
       )->addColumn(
       		'service_tax',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Service Tax'
       )->addColumn(
       		'transaction_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Transaction Id'
       )->addColumn(
       		'withdrawl_request_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Withdrawl Request Id'
       )->addColumn(
       		'amount_paid',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'amount_paid'
       )->addColumn(
       		'request_amount',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Request Amount'
       )->addColumn(
       		'created_at',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
       		255,
       		[],
       		'Customer Email'
       )->addColumn(
       		'invoice_sent',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Invoice Sent'
       )->addColumn(
       		'affiliate_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		1500,
       		[],
       		'Affiliate Id'
       )->addColumn(
       		'status',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		1500,
       		[],
       		'Status'
       )->addColumn(
       		'payment_mode',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		1500,
       		[],
       		'Status'
       )->addColumn(
       		'note',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		255,
       		[],
       		'Note'
       )->setComment(
       		'Custom Table'
       );
       $setup->getConnection()->createTable($table);

       $table = $setup->getConnection()->newTable(
       		$setup->getTable('ced_affiliatereferral_source')
       )->addColumn(
       		'id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
       		'Id'
       )->addColumn(
       		'customer_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['unsigned' => true, 'nullable' => false, 'default' => '0'],
       		'Customer ID'
       )->addColumn(
       		'referred_email',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'Referral Email'
       )->addColumn(
       		'created_at',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
       		null,
       		['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
       		'Created At'
       )->addColumn(
       		'source',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'Referral Source'
       )->addColumn(
       		'affiliate_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'Referral Source'
       )->setComment(
       		'Referral Source Table'
       );
       $setup->getConnection()->createTable($table);



       $table = $setup->getConnection()->newTable(
       		$setup->getTable('ced_affiliatereferral_traffics')
       )->addColumn(
       		'id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
       		'Id'
       )->addColumn(
       		'user_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['unsigned' => true, 'nullable' => false, 'default' => '0'],
       		'user ID'
       )->addColumn(
       		'affiliate_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'Referral Email'
       )->addColumn(
       		'created_at',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
       		null,
       		['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
       		'Created At'
       )->addColumn(
       		'total_click',
       		 \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            '11',
            ['nullable' => false],
       		'Referral Source'
       )->addColumn(
       		'unique_click',
       		 \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            '11',
            ['nullable' => false],
       		'Referral Source'
       )->addColumn(
       		'traffic_source',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'Referral Source'
       )->addColumn(
       		'landing_page',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'Referral Source'
       )->addColumn(
       		'shared_url',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'Referral Source'
       )->addColumn(
       		'facebook_click',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'Referral Source'
       )->addColumn(
       		'google_click',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'Referral Source'
       )->addColumn(
       		'twitter_click',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'Referral Source'
       )->addColumn(
       		'email_click',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'Referral Source'
       )->setComment(
       		'Referral Source Table'
       );
       $setup->getConnection()->createTable($table);

       $table = $setup->getConnection()->newTable(
       		$setup->getTable('ced_affiliatediscount_coupons')
       )->addColumn(
       		'id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
       		'ID'
       )->addColumn(
       		'customer_id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['unsigned' => true, 'nullable' => false, 'default' => '0'],
       		'Customer ID'
       )->addColumn(
       		'coupon_code',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'Coupon Code'
       )->addColumn(
       		'amount',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'Amount'
       )->addColumn(
       		'cart_amount',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'Amount'
       )->addColumn(
       		'status',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['unsigned' => true, 'nullable' => false, 'default' => '0'],
       		'Status'
       )->addColumn(
       		'created_at',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
       		null,
       		['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
       		'Creation At'
       )->addColumn(
       		'used_at',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
       		null,
       		['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
       		'Used At'
       )->addColumn(
       		'expiration_date',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
       		null,
       		['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
       		'Expiration Date'
       );
       $setup->getConnection()->createTable($table);



       $table = $setup->getConnection()->newTable(
       		$setup->getTable('ced_affiliatediscount_denomination_rules')
       )->addColumn(
       		'id',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
       		'ID'
       )->addColumn(
       		'rule_name',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
       		null,
       		['nullable' => false],
       		'Rule Name'
       )->addColumn(
       		'discount_amount',
       		\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
       		'10,2',
       		['unsigned' => true, 'nullable' => false,  'default' => 0],
       		'Discount Amount'
       )->addColumn(
       		'cart_amount',
       		\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
       		'10,2',
       		['unsigned' => true, 'nullable' => false,  'default' => 0],
       		'Cart Amount'
       )->addColumn(
       		'status',
       		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
       		null,
       		['unsigned' => true, 'nullable' => false, 'default' => '0'],
       		'Status'
       )->addColumn(
       		'created_at',
       		\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
       		null,
       		['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
       		'Created At'
       );
       $setup->getConnection()->createTable($table);


        $setup->endSetup();
	}
}
