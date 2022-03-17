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

class Zipcode extends AbstractModel

{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    const ADMIN_ID = 0;

    /**
     *  _construct
     */
    protected function _construct()

    {
        $this->_init('Ced\CsHyperlocal\Model\ResourceModel\Zipcode');
    }

    public function saveData($data,$vendorId,$shipareaId=null,$status)
    {
        try {
            $data['vendor_id'] = $vendorId;
            $data['status'] = $status;
            if ($shipareaId)
            {
                $this->load($shipareaId)->addData($data)->save();
            } else {
                $this->addData($data)->save();
            }
        } catch (\Exception $e)
        {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
        return $this;
    }
}