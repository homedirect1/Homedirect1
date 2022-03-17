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

namespace Ced\CsHyperlocal\Model;

use Ced\CsHyperlocal\Model\ResourceModel\Shiparea\Collection;

/**
 * Class ShipareaDataProvider
 * @package Ced\CsHyperlocal\Model
 */
class ShipareaDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magento\Framework\Registry
     */
    public $_coreRegistry;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var ZipcodeFactory
     */
    protected $zipcode;

    /**
     * ShipareaDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Collection $collectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param ZipcodeFactory $zipcode
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Collection $collectionFactory,
        \Magento\Framework\Registry $registry,
        \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcode,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory;
        $this->_coreRegistry = $registry;
        $this->zipcode = $zipcode;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {

        $data = $this->_coreRegistry->registry('ced_shiparea_data');
        $selectedVendorIds = $this->_coreRegistry->registry('selected_vendors');
        if ($selectedVendorIds) {
            $arr[0]['shiparea_form']['vendor_id'] = implode($selectedVendorIds, ',');
        } elseif ($data && $id = $data->getId()) {
            $shipareaData = $data->toArray();
            $arr = [$id => ['shiparea_form' => []]];
            foreach ($shipareaData as $key => $value) {

                if ($data->getZipcodeType() == 'single') {
                    $zipcodeData = $this->zipcode->create()->load($data->getId(), 'location_id');
                    $arr[$id]['shiparea_form']['zipcode'] = $zipcodeData->getZipcode();
                }
                $arr[$id]['shiparea_form'][$key] = $value;
            }
        } else {
            $arr = [];
        }
        return $arr;
    }
}