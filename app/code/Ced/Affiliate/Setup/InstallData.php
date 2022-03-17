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

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class InstallData implements InstallDataInterface
{
  /** @var EavSetupFactory */
  protected $_eavSetupFactory;

  /** @var Config */
  protected $_eavConfig;

  
  /**
   * @var CustomerSetupFactory
   */
  protected $customerSetupFactory;
  
  /**
   * @var AttributeSetFactory
   */
  private $attributeSetFactory;
  /**
   * InstallData constructor.
   * @param EavSetupFactory $eavSetupFactory
   * @param Config $config
   */
  public function __construct(
  		CustomerSetupFactory $customerSetupFactory,
  		AttributeSetFactory $attributeSetFactory,
    EavSetupFactory $eavSetupFactory,
    Config $config
  )
  {
  	$this->customerSetupFactory = $customerSetupFactory;
  	$this->attributeSetFactory = $attributeSetFactory;
    $this->_eavSetupFactory = $eavSetupFactory;
    $this->_eavConfig = $config;
  }

  public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
  {
    $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
    $eavSetup->addAttribute(
      Customer::ENTITY,
      'paypal_email',
      [
        'type' => 'text',
        'label' => 'Paypal Email',
        'input' => 'text',
        'required' => false,
        'sort_order' => 150,
        'system' => false,
        'position' => 100
      ]
    );
    $eavSetup->addAttribute(
    		Customer::ENTITY,
    		'referring_website',
    		[
    		'type' => 'text',
    		'label' => 'Referring Website',
    		'input' => 'text',
    		'required' => false,
    		'sort_order' => 150,
    		'system' => false,
    		'position' => 100
    		]
    );
    $eavSetup->addAttribute(
    		Customer::ENTITY,
    		'referred_by',
    		[
    		'type' => 'text',
    		'label' => 'Referred By',
    		'input' => 'text',
    		'required' => false,
    		'sort_order' => 150,
    		'system' => false,
    		'position' => 100
    		]
    );
    

    
    $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
    
    $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
    $attributeSetId = $customerEntity->getDefaultAttributeSetId();
    
    /** @var $attributeSet AttributeSet */
    $attributeSet = $this->attributeSetFactory->create();
    $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
    
    $customerSetup->addAttribute(Customer::ENTITY, 'referral_code', [
    		'type' => 'varchar',
    		'label' => 'Referral Code',
    		'input' => 'text',
    		'required' => false,
    		'visible' => true,
    		'user_defined' => true,
    		'sort_order' => 1000,
    		'position' => 1000,
    		'system' => 0,
    		]);
    
    $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'referral_code')
    ->addData([
    		'attribute_set_id' => $attributeSetId,
    		'attribute_group_id' => $attributeGroupId,
    		'used_in_forms' => ['adminhtml_customer'],
    		]);
    
    $attribute->save();
    
    
    $customerSetup->addAttribute(Customer::ENTITY, 'invitation_code', [
    		'type' => 'varchar',
    		'label' => 'Invitation Code',
    		'input' => 'text',
    		'required' => false,
    		'visible' => true,
    		'user_defined' => true,
    		'sort_order' => 1000,
    		'position' => 1000,
    		'system' => 0,
    		]);
    $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'invitation_code')
    ->addData([
    		'attribute_set_id' => $attributeSetId,
    		'attribute_group_id' => $attributeGroupId,
    		'used_in_forms' => ['adminhtml_customer'],
    		]);
    
    $attribute->save();
    
    /* $eavSetup->addAttribute(
    		Customer::ENTITY,
    		'paypal_email',
    		[
    		'type' => 'text',
    		'label' => 'Stripe Saved Card',
    		'input' => 'text',
    		'required' => false,
    		'sort_order' => 150,
    		'system' => false,
    		'position' => 100
    		]
    ); */
    /* $stripeCustomerId = $this->_eavConfig->getAttribute(Customer::ENTITY, 'stripe_customer_id');
    $stripeCustomerId->setData('used_in_forms', ['adminhtml_customer']);
    $stripeCustomerId->save(); */
  }
}