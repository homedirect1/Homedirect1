<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsHyperlocal
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsHyperlocal\Ui\DataProvider;

class Shiparea extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * @var array
     */
    protected $addFieldStrategies;

    /**
     * @var array
     */
    protected $addFilterStrategies;

    /**
     * Shiparea constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $collection
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $collection,
        \Ced\CsMarketplace\Model\ResourceModel\Vendor\Collection $vendorCollection,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collection->create();
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
        $this->vendorCollection = $vendorCollection;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $collection = $this->collection;
        $collection->getSelect()->where("is_origin_address IS NULL OR is_origin_address = '0'");
        return [
            'totalRecords' => count($collection->getData()),
            'items' => $collection->getData(),
        ];
    }


    /**
     * Add field to select
     *
     * @param string|array $field
     * @param string|null $alias
     * @return void
     */
    public function addField($field, $alias = null)
    {
        if (isset($this->addFieldStrategies[$field])) {
            $this->addFieldStrategies[$field]->addField($this->getCollection(), $field, $alias);
        } else {
            parent::addField($field, $alias);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $vId = [];
        if ($filter->getField() == 'vendor_id') {
            $vId = $this->vendorCollection->addAttributeToSelect('*')->addFieldToFilter('email', ['like' => '%' . $filter->getValue() . '%'])->getColumnValues('entity_id');
            if (strpos('admin', strtolower(trim($filter->getValue(),'%'))) !== false) {
                $vId[] = 0;
            }
        }
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            if ($filter->getField() == 'vendor_id') {

                $this->getCollection()->addFieldToFilter(
                    $filter->getField(),['in'=>$vId]);
            } else {
                $this->getCollection()->addFieldToFilter(
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
            }
           // parent::addFilter($filter);
        }
    }
}