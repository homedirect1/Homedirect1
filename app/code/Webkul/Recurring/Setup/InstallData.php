<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Entity\TypeFactory;
use Magento\Eav\Model\Entity\Attribute\GroupFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class InstallData implements InstallDataInterface
{
    const ATTRIBUTE_GROUP = 'Subscription Configuration';

    /**
     * @var Magento\Sales\Setup\SalesSetupFactory
     */
    protected $salesSetupFactory;
 
    /**
     * @var Magento\Quote\Setup\QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var AttributeFactory
     */
    private $attributeFactory;

    /**
     * @var SetFactory
     */
    private $attributeSetFactory;

    /**
     * @var TypeFactory
     */
    private $eavTypeFactory;

    /**
     * @var GroupFactory
     */
    private $attributeGroupFactory;

    /**
     * @var AttributeManagement
     */
    private $attributeManagement;

    /**
     * @var \Webkul\Recurring\Model\Subscription
     */
    protected $plans;

    /**
     * @param \Magento\Eav\Model\AttributeManagement $attributeManagement
     * @param EavSetupFactory $eavSetupFactory
     * @param TypeFactory $typeFactory
     * @param GroupFactory $attributeGroupFactory
     * @param \Webkul\Recurring\Model\Term $terms
     * @param SalesSetupFactory $salesSetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        \Magento\Eav\Model\AttributeManagement $attributeManagement,
        EavSetupFactory $eavSetupFactory,
        TypeFactory $typeFactory,
        GroupFactory $attributeGroupFactory,
        \Webkul\Recurring\Model\Term $terms,
        SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->eavSetupFactory       = $eavSetupFactory;
        $this->attributeSetFactory   = $attributeSetFactory;
        $this->attributeManagement   = $attributeManagement;
        $this->terms                 = $terms;
        $this->attributeGroupFactory = $attributeGroupFactory;
        $this->eavTypeFactory        = $typeFactory;
        $this->salesSetupFactory     = $salesSetupFactory;
        $this->quoteSetupFactory     = $quoteSetupFactory;
    }

    /**
     * Install Data
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->createQuoteAndOrderAttribute($setup);
        $this->addDefaultData();
        $attributeGroup = self::ATTRIBUTE_GROUP;
        $attributes = $this->getAttributeData();

        /** @var entityType $entityType */
        $entityType = $this->eavTypeFactory->create()->loadByCode('catalog_product');
        /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $setCollection */
        $setCollection = $this->attributeSetFactory->create()->getCollection();
        $setCollection->addFieldToFilter('entity_type_id', $entityType->getId());
        $attributeGroupCode =  str_replace(' ', '-', strtolower($attributeGroup));
         /** @var Set $attributeSet */
        foreach ($setCollection as $attributeSet) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            /** @var Group $group */
            $eavSetup->addAttributeGroup(
                $entityType->getId(),
                $attributeSet->getId(),
                $attributeGroup,
                60
            );
            $group = $this->attributeGroupFactory->create()->getCollection()
                ->addFieldToFilter(
                    'attribute_group_code',
                    ['eq' => $attributeGroupCode]
                )
                ->addFieldToFilter(
                    'attribute_set_id',
                    ['eq' => $attributeSet->getId()]
                );

            $groupId = $attributeSet->getDefaultGroupId();
            foreach ($group as $grp) {
                $groupId = $grp->getId();
                break;
            }

            foreach ($attributes as $attribute_code => $attributeOptions) {
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $attribute_code,
                    $attributeOptions
                );
            }
            foreach ($attributes as $attribute_code => $attributeOptions) {
                // Assign:
                $this->attributeManagement->assign(
                    'catalog_product',
                    $attributeSet->getId(),
                    $groupId,
                    $attribute_code,
                    $attributeSet->getCollection()->getSize() * 10
                );
            }
        }
    }

    /**
     * Create quote and order attribute
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function createQuoteAndOrderAttribute($setup)
    {
         /** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
        $salesInstaller = $this->salesSetupFactory
                        ->create(
                            [
                                'resourceName' => 'sales_setup',
                                'setup' => $setup
                            ]
                        );
        /** @var \Magento\Quote\Setup\QuoteSetup $quoteInstaller */
        $quoteInstaller = $this->quoteSetupFactory
                        ->create(
                            [
                                'resourceName' => 'quote_setup',
                                'setup' => $setup
                            ]
                        );
 
        $this->addQuoteAttributes($quoteInstaller);
        $this->addOrderAttributes($salesInstaller);
    }

    /**
     * Add attribute in quote address
     * @param object $installer
     */
    private function addQuoteAttributes($installer)
    {
        $installer->addAttribute('quote', 'initial_fee', ['type' => 'text']);
    }
 
    /**
     * Add attribute in sales_order
     * @param object $installer
     */
    private function addOrderAttributes($installer)
    {
        $installer->addAttribute('order', 'initial_fee', ['type' => 'text']);
    }

    /**
     * Get attribute data
     *
     * @return void
     */
    private function getAttributeData()
    {
        $attributeGroup = self::ATTRIBUTE_GROUP;
        return [
            'subscription' => [
                'group'                      => $attributeGroup,
                'input'                      => 'select',
                'type'                       => 'int',
                'label'                      => 'Subscription',
                'visible'                    => true,
                'required'                   => false,
                'user_defined'               => true,
                'searchable'                 => false,
                'filterable'                 => false,
                'comparable'                 => false,
                'visible_on_front'           => false,
                'visible_in_advanced_search' => false,
                'is_html_allowed_on_front'   => false,
                'used_for_promo_rules'       => true,
                'source'                     => \Webkul\Recurring\Model\Config\Source\Options::class,
                'frontend_class'             => '',
                'global'                     => ScopedAttributeInterface::SCOPE_WEBSITE,
                'unique'                     => false,
                'apply_to'                   => 'simple,configurable,downloadable,virtual'
            ]
        ];
    }

    /**
     * Add default data to recurring durations
     *
     * @return void
     */
    private function addDefaultData()
    {
        $rows = [
                [
                    'id' =>    '',
                    'title' =>    'Weekly',
                    'duration' =>    '7',
                    'sort_order' =>    '1',
                    'status' =>    1
                ],
                [
                    'id' =>    '',
                    'title' =>    'Monthly',
                    'duration' =>    '30',
                    'sort_order' =>    '2',
                    'status' =>    1
                ],
                [
                    'id' =>    '',
                    'title' =>    'Yearly',
                    'duration' =>    '365',
                    'sort_order' =>    '3',
                    'status' =>    1
                ],
            ];
        foreach ($rows as $row) {
            if ($this->checkAvailability($row['duration'])) {
                $this->saveTerms($row);
            }
        }
    }

    /**
     * Check if durations are saved or not
     *
     * @param [type] $duration
     * @return void
     */
    private function checkAvailability($duration)
    {
        $collection = $this->terms->getCollection()->addFieldToFilter('duration', $duration);
        if ($collection->getSize()) {
            return false;
        }
        return true;
    }

    /**
     * This function saves the terms row wise
     *
     * @param array $row
     * @return void
     */
    private function saveTerms($row)
    {
        $time = date('Y-m-d H:i:s');
        $model = $this->terms;
        $row['update_time'] = $time;
        if ($row['id'] == 0 || $row['id'] == "") {
            $row['created_time'] = $time;
        }
        $model->setData($row);
        if ($row['id'] > 0) {
            $model->setId($row['id']);
        }
        $model->save();
    }
}
