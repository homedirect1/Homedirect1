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

namespace Ced\Affiliate\Model;
class Paymentsettings extends \Ced\Affiliate\Model\FlatAbstractModel
{
    const PAYMENT_SECTION = 'payment';
    
    protected function _construct()
    {
        $this->_init('Ced\Affiliate\Model\ResourceModel\Paymentsettings');
    }

    public function getValue() 
    {
        if ($this->getId() && $this->getSerialized()) {
            return unserialize($this->getData('value'));
        }
        return $this->getData('value');
    }
    
}
