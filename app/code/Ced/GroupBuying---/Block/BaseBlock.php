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
 * @package     Ced_GroupBuying
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GroupBuying\Block;

use Ced\GroupBuying\Helper\Data;
use Ced\GroupBuying\Model\Config;
use Magento\Framework\Url;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\Template;

class BaseBlock extends Template
{

    /**
     * @var Data
     */
    protected $_devToolHelper;

    /**
     * @var Url
     */
    protected $_urlApp;

    /**
     * @var Config
     */
    protected $_config;


    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->_devToolHelper = $context->getGroupBuyingHelper();
        $this->_config        = $context->getConfig();
        $this->_urlApp        = $context->getUrlFactory()->create();
        parent::__construct($context);

    }//end __construct()


    /**
     * Function for getting event details
     *
     * @return array
     */
    public function getEventDetails()
    {
        return $this->_devToolHelper->getEventDetails();

    }//end getEventDetails()


    /**
     * Function for getting current url
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlApp->getCurrentUrl();

    }//end getCurrentUrl()


    /**
     * Function for getting controller url for given router path
     *
     * @param string $routePath
     *
     * @return string
     */
    public function getControllerUrl($routePath)
    {
        return $this->_urlApp->getUrl($routePath);

    }//end getControllerUrl()


    /**
     * Function for getting current url
     *
     * @param  string $path
     * @return string
     */
    public function getConfigValue($path)
    {
        return $this->_config->getCurrentStoreConfigValue($path);

    }//end getConfigValue()


    /**
     * Function canShowGroupBuying
     *
     * @return boolean
     */
    public function canShowGroupBuying()
    {
        $isEnabled = $this->getConfigValue('groupbuying/module/is_enabled');
        if ($isEnabled) {
            $allowedIps = $this->getConfigValue('groupbuying/module/allowed_ip');
            if (is_null($allowedIps)) {
                return true;
            } else {
                $remoteIp = $_SERVER['REMOTE_ADDR'];
                if (strpos($allowedIps, $remoteIp) !== false) {
                    return true;
                }
            }
        }

        return false;

    }//end canShowGroupBuying()


}//end class
