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
 * @package     Ced_CsPromotions
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsPromotions\Model;

use Magento\Quote\Model\Quote\Address;

class Rule extends \Magento\SalesRule\Model\Rule
{
    
    /**
     * Set coupon code and uses per coupon
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        self::loadRelations();
        return parent::_afterLoad();
    }

    /**
     * Load all relative data
     *
     * @return void
     */
    public function loadRelations()
    {
        self::loadCouponCode();
    }
    
}
