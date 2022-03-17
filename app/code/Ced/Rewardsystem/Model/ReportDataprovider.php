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
 * @package     Ced_Rewardsystem
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Rewardsystem\Model;

use Ced\Rewardsystem\Helper\Data;

/**
 * Class ReportDataprovider
 * @package Ced\Rewardsystem\Model
 */
class ReportDataprovider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * @var Data
     */
    protected $rewardsystem_helper;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer
     */
    protected $customerResource;

    /**
     * ReportDataprovider constructor.
     * @param ResourceModel\Regisuserpoint\CollectionFactory $collectionFactory
     * @param Data $rewardsystem_helper
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer $customerResource
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint\CollectionFactory $collectionFactory,
        Data $rewardsystem_helper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\ResourceModel\Customer $customerResource,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->rewardsystem_helper = $rewardsystem_helper;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getData()
    {
        $data = $this->rewardsystem_helper->getCustomerWisePointSheet();
        $responseArray = [];
        $j = 0;

        foreach ($data as $customer_id => $point_details) {
            $customer = $this->customerFactory->create();
            $this->customerResource->load($customer, $customer_id);
            $responseArray[$j]['customer_id'] = $customer_id;
            $responseArray[$j]['customer_name'] = $customer->getName();
            $responseArray[$j]['customer_email'] = $customer->getEmail();
            $responseArray[$j]['point'] = !empty($point_details['points']) ? $point_details['points'] : 0;
            $responseArray[$j]['point_used'] = !empty($point_details['points_data']) ? array_sum(array_column($point_details['points_data'], 'point_used')) : 0;
            $responseArray[$j]['earned_point'] = !empty($point_details['points_data']) ? array_sum(array_column($point_details['points_data'], 'point')) : 0;
            $j++;
        }

        return [
            'totalRecords' => count($responseArray),
            'items' => $responseArray,
        ];
    }
}
