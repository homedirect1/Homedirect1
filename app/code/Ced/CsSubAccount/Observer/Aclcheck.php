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
 * @package     Ced_CsSubAccount
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;

/**
 * Class Aclcheck
 * @package Ced\CsSubAccount\Observer
 */
Class Aclcheck implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Ced\CsSubAccount\Model\CsSubAccountFactory
     */
    protected $csSubAccountFactory;

    /**
     * Aclcheck constructor.
     * @param Session $customerSession
     * @param \Magento\Framework\App\Request\Http $request
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeinterface
     * @param \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory
     */
    public function __construct(
        Session $customerSession,
        \Magento\Framework\App\Request\Http $request,
        UrlInterface $urlBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeinterface,
        \Ced\CsSubAccount\Model\CsSubAccountFactory $csSubAccountFactory
    )
    {
        $this->session = $customerSession;
        $this->_request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->_scopeConfig = $scopeinterface;
        $this->csSubAccountFactory = $csSubAccountFactory;
    }

    /**
     * Adds catalog categories to top menu
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
        $parameters = $this->_request->getParams();
        $moduleName = $this->_request->getModuleName();
        $controllerName = $this->_request->getControllerName();
        $actionName = $this->_request->getActionName();
        $urlredirect = "csmarketplace/vendor/index";
        $aclresources = $this->_scopeConfig->getValue('vendor_acl');
        $marketplaceurls = array();
        if (empty($aclresources)) {
            return $this; 
        }
        foreach ($aclresources['resources']['vendor']['children'] as $key => $value) {
            if (isset($value['paths'])) {
                $value['path'] = $value['paths'];
            }
            if (isset($value['ifconfig']) && !$this->_scopeConfig->getValue($value['ifconfig'], \Magento\Store\Model\ScopeInterface::SCOPE_STORE) && !isset($value['dependsonparent']))
                continue;
            elseif (isset($value['ifconfig']) && !$this->_scopeConfig->getValue($value['ifconfig'], \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
                if (isset($value['dependsonparent'][$key])) {
                    $value = $value['dependsonparent'][$key];
                }
            }

            if (isset($value['children']) && is_array($value['children']) && !empty($value['children'])) {
                foreach ($value['children'] as $key2 => $value2) {
                    if (isset($value2['ifconfig']) && !$this->_scopeConfig->getValue($value2['ifconfig'], \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
                        continue;
                    $marketplaceurls[] = $value2['path'];
                }
                if (isset($value['path']))
                    $marketplaceurls[] = $value['path'];

            } else {
                $marketplaceurls[] = $value['path'];
            }
        }
        $marketplaceurls = array_unique($marketplaceurls);
        if (($key = array_search("#", $marketplaceurls)) !== false) {
            unset($marketplaceurls[$key]);
        }
        $vendor = $this->_getSession()->getVendor();
        if (!$vendor) {
            return $this;
        }
        $group = $vendor['group'];
        $subVendor = $this->session->getSubVendorData();
        if (!empty($subVendor)) {
            $sub_vendordata = $this->csSubAccountFactory->create()->load($subVendor['id'])->getRole();

            if ($sub_vendordata == 'all') {
                return $this;
            }
            if (strpos($sub_vendordata, $moduleName . '/' . $controllerName . '/' . $actionName) === true) {
                return $this;
            } elseif (strpos($sub_vendordata, $moduleName . '/' . $controllerName . '/' . $actionName) != -1) {
                return $this;
            } elseif (($moduleName . '/' . $controllerName . '/' . $actionName === 'csmarketplace/vendor/index') || ($moduleName . '/' . $controllerName . '/' . $actionName === 'csmarketplace/vendor/map') || ($moduleName . '/' . $controllerName . '/' . $actionName === 'csmarketplace/vendor/chart')) {
                return $this;
            } else {
                $url = $this->urlBuilder->getUrl($urlredirect, array('_secure' => true));
                $observer->getEvent()->getAction()->getResponse()->setRedirect($url);
                $observer->getEvent()->getCurrent()->_allowedResource = false;
                return $this;
            }
        } else
            return $this;

    }

    /**
     * @return Session
     */
    protected function _getSession()
    {
        return $this->session;
    }

    /**
     * @param $string
     * @param $arr
     * @return bool
     */
    protected function checkurl($string, $arr)
    {

        foreach ($arr as $_arr) {

            if (strpos($_arr, $string) !== false) {
                return true;
            }
        }

        return false;

    }

    /**
     * @param $urlpath
     * @return bool
     */
    protected function isallDashboardallow($urlpath)
    {
        if (strpos('csmarketplace/vendor/chart', $urlpath) !== false || strpos('csmarketplace/vendor/map', $urlpath) !== false) {
            return true;

        }
        return false;
    }

}
