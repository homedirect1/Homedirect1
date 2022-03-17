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
 
namespace Ced\Affiliate\Model\Customer\Payment\Methods;

class Storecredit extends AbstractModel
{
    protected $_code = 'storecredit';
    
    /**
     * Retreive input fields
     *
     * @return array
     */
    public function getFields() 
    {
        $fields = parent::getFields();
        $fields['credit_name'] = array('type'=>'text');
        return $fields;
    }
    
    /**
     * Retreive labels
     *
     * @param  string $key
     * @return string
     */
    public function getLabel($key) 
    {
        switch($key) {
        case 'label' : 
            return __('Store Credit');break;
        case 'credit_name' : 
            return __('Store Credit Name');break;
        default : 
            return parent::getLabel($key); break;
        }
    }
}
