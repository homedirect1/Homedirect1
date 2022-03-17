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
 * @package     Ced_GoogleMap
 * @author 	    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */


namespace Ced\GoogleMap\Block\Google;


use Ced\GoogleMap\Helper\Data;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Places
 * @package Ced\GoogleMap\Block\Google
 */
class Places extends \Magento\Framework\View\Element\Template
{

    protected $_template = 'Ced_GoogleMap::places.phtml';

    /**
     * @return mixed
     */
    public function getGoogleMapApiKey()
    {
        return $this->_scopeConfig->getValue(
            Data::GOOGLE_MAP_API_KEY,
            ScopeInterface::SCOPE_STORE
        );
    }
}
