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

namespace Ced\CsHyperlocal\Plugin\Vendor\Profile;

use Ced\CsHyperlocal\Model\Shiparea;

/**
 * Class Save
 * @package Ced\CsHyperlocal\Plugin\Vendor\Profile
 */
class Save
{
    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory
     */
    protected $shipareaCollection;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory
     */
    protected $zipcodeCollection;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Ced\CsHyperlocal\Model\ShipareaFactory
     */
    protected $shipareaModel;

    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $marketplaceHelper;

    /**
     * @var \Ced\CsHyperlocal\Model\ZipcodeFactory
     */
    protected $zipcodeFactory;

    /**
     * Save constructor.
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaCollection
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaModel
     * @param \Ced\CsMarketplace\Helper\Data $marketplaceHelper
     * @param \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory
     */
    public function __construct(
        \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaCollection,
        \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaModel,
        \Ced\CsMarketplace\Helper\Data $marketplaceHelper,
        \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory
    )
    {
        $this->shipareaCollection = $shipareaCollection;
        $this->zipcodeCollection = $zipcodeCollection;
        $this->request = $request;
        $this->customerSession = $customerSession;
        $this->shipareaModel = $shipareaModel;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->zipcodeFactory = $zipcodeFactory;
    }

    /**
     * @param \Magento\Catalog\Controller\Product\View $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundExecute(\Ced\CsMarketplace\Controller\Vendor\Save $subject, \Closure $proceed
    )
    {
        $returnValue = $proceed();
        $city = false;
        $state = false;
        $country = false;
        $zipcode = false;
        $vendorId = $this->customerSession->getVendorId();
        $shipareaCollection = $this->shipareaCollection->create()
            ->addFieldToFilter('vendor_id', $vendorId)
            ->addFieldToFilter('is_origin_address', Shiparea::ORIGIN_ADDRESS);
        $post = $this->request->getPost();

        if (isset($post['vendor']['latitude']) && isset($post['vendor']['longitude']) && isset($post['vendor']['location'])) {
            $apiKey = $this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::API_KEY);

            $lat = $post['vendor']['latitude'];
            $long = $post['vendor']['longitude'];
            $data = [];
            $data['latitude'] = $lat;
            $data['longitude'] = $long;
            $data['location'] = $post['vendor']['location'];
            /**  get city , country and state */
            $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $long . '&sensor=false&key=' . $apiKey;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result = curl_exec($ch);
            curl_close($ch);
            $result_array = json_decode($result, true);
            if ($result_array['status'] == 'OK') {
                foreach ($result_array['results'][0]['address_components'] as $address) {
                    if ($address['types'][0] == 'administrative_area_level_2') {
                        $data['city'] = $address['long_name'];
                        $city = true;
                    }
                    if ($address['types'][0] == 'administrative_area_level_1') {
                        $data['state'] = $address['long_name'];
                        $state = true;
                    }
                    if ($address['types'][0] == 'country') {
                        $data['country'] = $address['long_name'];
                        $country = true;
                    }
                    if ($address['types'][0] == 'postal_code') {
                        $data['zipcode'] = $address['long_name'];
                        $zipcode = true;
                    }
                }
            }
            if ($shipareaCollection->count()) {
                $shipareaId = $shipareaCollection->getFirstItem()->getId();
            } else {
                $shipareaId = '';
            }

            $data['city'] = !$city ? '' : $data['city'];
            $data['state'] = !$state ? '' : $data['state'];
            $data['country'] = !$country ? '' : $data['country'];
            $data['zipcode'] = !$zipcode ? '' : $data['zipcode'];

            $locationId = $this->shipareaModel->create()
                ->saveData($data, $vendorId, $shipareaId, Shiparea::STATUS_ENABLED, Shiparea::ORIGIN_ADDRESS);

            /** save zipcode*/
            $zipcodeCollection = $this->zipcodeCollection->create()
                ->addFieldToFilter('location_id', $locationId);
            if (isset($data['zipcode']) && $data['zipcode'] != '') {
                if ($zipcodeCollection->count()) {
                    $this->zipcodeFactory->create()->load($zipcodeCollection->getFirstItem()->getId())
                        ->setZipcode($data['zipcode'])
                        ->save();
                } else {
                    $this->zipcodeFactory->create()->setLocationId($locationId)
                        ->setVendorId($vendorId)
                        ->setZipcode($data['zipcode'])
                        ->save();
                }
            }
        }
        return $returnValue;
    }
}