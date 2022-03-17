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

use Magento\Framework\Model\AbstractModel;

class Shiparea extends AbstractModel

{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    const ADMIN_ID = 0;
    const ORIGIN_ADDRESS = 1;

    /**
     *  _construct
     */
    protected function _construct()

    {
        $this->_init('Ced\CsHyperlocal\Model\ResourceModel\Shiparea');
    }

    /**
     * @param $data
     * @param $vendorId
     * @param null $shipareaId
     * @param $status
     * @param int $isOriginAddress
     * @return $this|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveData($data,$vendorId,$shipareaId=null,$status,$isOriginAddress=0)
    {
        try {
            $data['vendor_id'] = $vendorId;
            $data['status'] = $status;
            $data['is_origin_address'] = $isOriginAddress;
            if ($shipareaId)
            {
                $this->load($shipareaId)->addData($data)->save();
            } else {
                $this->addData($data)->save();
            }
            return $this->getId();
        } catch (\Exception $e)
        {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
        return $this;
    }

    /**
     * @param $locationId
     * @return mixed
     */
    public function getVendorIdByLocationId($locationId)
    {
        return $this->load($locationId)->getVendorId();
    }
}