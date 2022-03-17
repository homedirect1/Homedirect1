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

namespace Ced\CsHyperlocal\Plugin;

use Ced\CsHyperlocal\Model\Shiparea;

/**
 * Class ConfigPlugin
 * @package Ced\CsHyperlocal\Plugin
 */
class ConfigPlugin
{
    /**
     * @var \Ced\CsHyperlocal\Model\ShipareaFactory
     */
    protected $shipareaModel;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory
     */
    protected $shipareaCollectionFactory;

    /**
     * ConfigPlugin constructor.
     * @param \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaModel
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaCollectionFactory
     */
    public function __construct(
        \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaModel,
        \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaCollectionFactory
    )
    {
        $this->shipareaModel = $shipareaModel;
        $this->shipareaCollectionFactory = $shipareaCollectionFactory;
    }

    /**
     * @param \Magento\Config\Model\Config $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundSave(
        \Magento\Config\Model\Config $subject,
        \Closure $proceed
    )
    {
        $postData = $subject->getData();
        $data = [];

        if (isset($postData['groups']['admin_default_address']['fields']['location']['value'])) {
            $data['location'] = $postData['groups']['admin_default_address']['fields']['location']['value'];
        }
        if (isset($postData['groups']['admin_default_address']['fields']['city']['value'])) {
            $data['city'] = $postData['groups']['admin_default_address']['fields']['city']['value'];
        }
        if (isset($postData['groups']['admin_default_address']['fields']['state']['value'])) {
            $data['state'] = $postData['groups']['admin_default_address']['fields']['state']['value'];
        }
        if (isset($postData['groups']['admin_default_address']['fields']['country']['value'])) {
            $data['country'] = $postData['groups']['admin_default_address']['fields']['country']['value'];
        }
        if (isset($postData['groups']['admin_default_address']['fields']['latitude']['value'])) {
            $data['latitude'] = $postData['groups']['admin_default_address']['fields']['latitude']['value'];
        }
        if (isset($postData['groups']['admin_default_address']['fields']['longitude']['value'])) {
            $data['longitude'] = $postData['groups']['admin_default_address']['fields']['longitude']['value'];
        }
        if (!empty($data))
        {
            $shipareaCollection = $this->shipareaCollectionFactory->create()
                ->addFieldToFilter('vendor_id', Shiparea::ADMIN_ID)
                ->addFieldToFilter('is_origin_address', Shiparea::ORIGIN_ADDRESS);
            if ($shipareaCollection->count()) {
                $shipareaId = $shipareaCollection->getFirstItem()->getId();
            } else {
                $shipareaId = '';
            }
        
            $this->shipareaModel->create()
                ->saveData($data, Shiparea::ADMIN_ID, $shipareaId, Shiparea::STATUS_ENABLED, Shiparea::ORIGIN_ADDRESS);
        }
        return $proceed();
    }
}

