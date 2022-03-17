<?php
/* app/code/Atwix/TestAttribute/Setup/InstallData.php */

namespace Ced\CsSla\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
	/**
	 * EAV setup factory
	 *
	 * @var EavSetupFactory
	 */
	private $eavSetupFactory;

	/**
	 * Init
	 *
	 * @param EavSetupFactory $eavSetupFactory
	 */
	public function __construct(EavSetupFactory $eavSetupFactory)
	{
		$this->eavSetupFactory = $eavSetupFactory;
	}

	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{

		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        /**
         *
          Add attributes to the eav/attribute
         */
        $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'dispatch_time', [
            'group'=> 'General',
            'type'=>'int',
            'backend'=>'',
            'frontend'=>'',
            'label'=>'dispatch_time',
            'input'=>'text',
            'class'=>'',
            'source'=>'',
            'global'=>\Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible'=>true,
            'required'=>false,
            'user_defined'=>false,
            'default'=>'',
            'searchable'=>false,
            'filterable'=>false,
            'comparable'=>false,
            'visible_on_front'=>false,
	        'visible_in_advanced_search' => true,
            'used_in_product_listing'=>true,
        	'is_used_in_grid' =>true,
            'unique'=>false,
            'apply_to'=>'simple,virtual,downloadable,grouped,bundled,configurable'  
        ]
        );
        $eavSetup->addAttributeToSet ( 'catalog_product', 'Default', 'General', 'dispatch_time');
        
     
        
        
	}
}